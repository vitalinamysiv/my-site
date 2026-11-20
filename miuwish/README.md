# MiuWish

Сайт списков желаний. У каждого человека один список: друзья открывают его по ссылке, бронируют подарок и не показываются владельцу. Можно искать людей по имени и подписываться на них.

Стек: PHP 8.3, Laravel 13, MySQL 8, Blade, Tailwind и Vite. Регистрация, вход и сессии взяты из Laravel Breeze.

Какие файлы пришли с фреймворком, а какие написаны для MiuWish, перечислено в [SOURCE.md](SOURCE.md).

## Что установить

- PHP 8.3. В `php.ini` должны быть включены `curl`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, `zip`.
- [Composer](https://getcomposer.org/)
- MySQL 8
- Node.js 22

Каталоги `vendor` и `node_modules`, файл `.env`, собранные стили `public/build` и загруженные фото в репозиторий не входят. Их создают команды ниже.

## База данных

В MySQL выполните, подставив свой пароль:

```sql
CREATE DATABASE miuwish CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'miuwish'@'127.0.0.1' IDENTIFIED BY 'пароль';
GRANT ALL PRIVILEGES ON miuwish.* TO 'miuwish'@'127.0.0.1';
```

## Первый запуск

В папке проекта скопируйте настройки:

```bash
copy .env.example .env
```

На macOS и Linux та же команда: `cp .env.example .env`.

В `.env` впишите пароль базы в `DB_PASSWORD`. Затем:

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Откройте http://127.0.0.1:8000. Окно, в котором запущена последняя команда, не закрывайте: пока оно открыто, сайт работает. Остановка — Ctrl+C.

Повторный запуск, когда зависимости уже стоят, — только `php artisan serve`. MySQL при этом должен быть запущен. `npm run build` нужен снова, только если менялись стили или скрипты.

## Учебные входы

Команда `migrate --seed` создаёт четыре аккаунта. Пароль у всех `password`. Войти можно логином или почтой.

- `anna` — Аня, `anna@miuwish.test`
- `misha` — Миша, `misha@miuwish.test`
- `lina` — Лина, `lina@miuwish.test`
- `kostya` — Костя, `kostya@miuwish.test`

## Проверки

```bash
php artisan test
```

Тесты используют SQLite в памяти и не трогают базу MySQL.

## Сервер

`php artisan serve` — локальный просмотр. Для Ubuntu 24.04 с Nginx, PHP-FPM, MySQL 8 и HTTPS есть `deploy/ubuntu/install.sh` и `deploy/nginx/miuwish.conf`. Пароль базы на сервере берётся из переменной `MIUWISH_DB_PASSWORD`. Если её не задать, скрипт использует `miuwish_local`.
