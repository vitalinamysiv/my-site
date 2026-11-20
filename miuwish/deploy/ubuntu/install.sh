#!/usr/bin/env bash
# Ubuntu 24.04: Nginx, PHP 8.3 FPM, MySQL 8, HTTPS.
# The site is this same Laravel project. php artisan serve is not used.
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
export LANG=C.UTF-8
export COMPOSER_ALLOW_SUPERUSER=1
mkdir -p /var/log
exec > >(tee -a /var/log/miuwish-install.log) 2>&1

if [[ "${EUID}" -ne 0 ]]; then
  echo "Run as root: sudo bash deploy/ubuntu/install.sh" >&2
  exit 1
fi

shopt -s nullglob
path_files=(/mnt/c/Users/*/miuwish-src.path)
if [[ -n "${1:-}" ]]; then
  SRC="$1"
elif [[ ${#path_files[@]} -gt 0 ]] && command -v wslpath >/dev/null 2>&1; then
  WIN_PATH="$(tr -d '\r' < "${path_files[0]}")"
  SRC="$(wslpath -u "${WIN_PATH}")"
else
  SRC="$(cd "$(dirname "$0")/../.." && pwd)"
fi

APP=/var/www/miuwish
DB_NAME=miuwish
DB_USER=miuwish
DB_PASSWORD="${MIUWISH_DB_PASSWORD:-miuwish_local}"

echo "Source: ${SRC}"
test -f "${SRC}/artisan"
. /etc/os-release
echo "OS: ${PRETTY_NAME}"

apt-get update
apt-get install -y nginx mysql-server \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath \
  unzip curl ca-certificates openssl rsync git

if ! command -v composer >/dev/null 2>&1; then
  curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

install -d -m 755 "${APP}"
rsync -a --delete \
  --exclude '.env' \
  --exclude '.git' \
  --exclude 'node_modules' \
  --exclude 'vendor' \
  --exclude 'storage' \
  --exclude 'public/storage' \
  --exclude 'public/hot' \
  "${SRC}/" "${APP}/"

install -d -m 775 \
  "${APP}/storage/app/public" \
  "${APP}/storage/framework/cache/data" \
  "${APP}/storage/framework/sessions" \
  "${APP}/storage/framework/views" \
  "${APP}/storage/logs" \
  "${APP}/bootstrap/cache"

cat > "${APP}/.env" <<EOF
APP_NAME=MiuWish
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://localhost

APP_LOCALE=ru
APP_FALLBACK_LOCALE=ru
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=${DB_NAME}
DB_USERNAME=${DB_USER}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@miuwish.test"
MAIL_FROM_NAME="MiuWish"

VITE_APP_NAME="MiuWish"
EOF

(
  cd "${APP}"
  composer install --no-dev --optimize-autoloader --no-interaction
  php artisan key:generate --force
)

if [[ ! -f "${APP}/public/build/manifest.json" ]]; then
  if ! command -v node >/dev/null 2>&1 || ! node -e 'process.exit(Number(process.versions.node.split(".")[0]) >= 22 ? 0 : 1)'; then
    curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
    apt-get install -y nodejs
  fi
  (cd "${APP}" && npm ci && npm run build)
fi

cat > /etc/php/8.3/fpm/conf.d/99-miuwish.ini <<'EOF'
upload_max_filesize = 8M
post_max_size = 8M
EOF
cp /etc/php/8.3/fpm/conf.d/99-miuwish.ini /etc/php/8.3/cli/conf.d/99-miuwish.ini

install -d -m 755 /etc/nginx/ssl
if [[ ! -f /etc/nginx/ssl/miuwish.crt ]]; then
  openssl req -x509 -nodes -days 825 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/miuwish.key \
    -out /etc/nginx/ssl/miuwish.crt \
    -subj "/CN=localhost" \
    -addext "subjectAltName=DNS:localhost,IP:127.0.0.1"
fi
chmod 640 /etc/nginx/ssl/miuwish.key

sed 's/\r$//' "${SRC}/deploy/nginx/miuwish.conf" > /etc/nginx/sites-available/miuwish
ln -sfn /etc/nginx/sites-available/miuwish /etc/nginx/sites-enabled/miuwish
rm -f /etc/nginx/sites-enabled/default
nginx -t

start_service() {
  local unit="$1"
  if [[ -d /run/systemd/system ]]; then
    systemctl enable "${unit}"
    systemctl restart "${unit}"
  else
    service "${unit}" restart
  fi
}

start_service mysql
start_service php8.3-fpm
start_service nginx

for _ in $(seq 1 30); do
  if mysqladmin --protocol=socket -u root ping --silent; then
    break
  fi
  sleep 1
done

mysql --protocol=socket -u root <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL

(
  cd "${APP}"
  php artisan migrate --force
  php artisan storage:link --force
)

USER_COUNT="$(mysql --protocol=socket -N -u root -e "SELECT COUNT(*) FROM \`${DB_NAME}\`.users" 2>/dev/null || echo 0)"
if [[ "${USER_COUNT}" == "0" ]]; then
  (cd "${APP}" && php artisan db:seed --force)
fi

(
  cd "${APP}"
  php artisan config:cache
  php artisan view:cache
)

chown -R www-data:www-data "${APP}"
chmod -R ug+rwx "${APP}/storage" "${APP}/bootstrap/cache"

if [[ -d /run/systemd/system ]]; then
  systemctl reload php8.3-fpm
  systemctl reload nginx
else
  service php8.3-fpm restart
  service nginx restart
fi

curl -fsS -k -o /dev/null -w "health %{http_code}\n" https://127.0.0.1/up
echo "MIUWISH_READY https://localhost"
php -v
mysql --version
nginx -v
