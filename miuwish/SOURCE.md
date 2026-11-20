# MiuWish

Веб-приложение для ведения списков желаний, бронирования подарков и подписки на друзей. 

Проект построен на каркасе **Laravel** с пакетом **Laravel Breeze** (регистрация, вход, сессии, базовые шаблоны и служебные каталоги). Поверх него написана логика самого MiuWish: желания, брони, друзья и обрезка аватарок.

> **Примечание:** каталоги `vendor` и `node_modules` в структуру не включены — это внешние библиотеки, которые устанавливаются через `composer` и `npm`.

---

## 📁 Структура проекта

### 1. Базовый каркас (почти без изменений)

#### Файлы, создаваемые Laravel при установке
* **Корень и конфигурация:** `artisan`, `bootstrap`, `config`, `public/index.php`, `public/.htaccess`, `.env.example`
* **Зависимости и сборка:** `composer.json`, `composer.lock`, `package.json`, `vite.config.js`, `phpunit.xml`
* **Маршруты:** `routes/console.php`, `routes/api.php`
* **База данных:** миграции `cache` и `jobs`
* **Ядро и провайдеры:** `app/Http/Controllers/Controller.php`, `app/Providers/AppServiceProvider.php`
* **Тесты:** `tests/TestCase.php`, `tests/Unit/ExampleTest.php`

#### Файлы Laravel Breeze (оставлены в исходном виде)
* **Контроллеры авторизации:** `ConfirmablePasswordController.php`, `VerifyEmailController.php`, `EmailVerificationPromptController.php`, `EmailVerificationNotificationController.php` (в `app/Http/Controllers/Auth/`)
* **Шаблоны авторизации:** `confirm-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php` (в `resources/views/auth/`)
* **Дашборд:** `resources/views/dashboard.blade.php` (маршрут с неё сразу перенаправляет на «Мои желания»)
* **UI-компоненты форм:** `modal`, `text-input`, `input-label`, `input-error`, `dropdown`, `nav-link`, `application-logo`, `auth-session-status`
* **Тесты:** `tests/Feature/Auth` (за исключением тестов логина и регистрации)

#### Служебные файлы (генерируются при запуске и сборке)
* `storage/framework`, `storage/logs`, `bootstrap/cache`
* `public/build` — собранные стили и скрипты
* `public/storage` — загруженные пользователями фотографии
* `.env` — локальные настройки окружения (не входят в репозиторий)

---

### 2. Каркас, переписанный под MiuWish

Заготовки Breeze и Laravel, адаптированные под логику и дизайн проекта:

* **Модель пользователя:** `app/Models/User.php` — добавлены логин, возраст, подписки, автоматическое снятие броней при удалении аккаунта.
* **Авторизация и профиль:**
  * `RegisteredUserController.php`, `AuthenticatedSessionController.php`, `LoginRequest.php` — вход по логину или email.
  * `PasswordController.php`, `PasswordResetLinkController.php`, `NewPasswordController.php` — локализация ответов на русский язык.
  * `ProfileController.php`, `ProfileUpdateRequest.php` — редактирование профиля и загрузка квадратной аватарки.
* **Маршруты:** `routes/web.php`, `routes/auth.php`
* **Шаблоны и стили:**
  * `resources/views/layouts/` (`app.blade.php`, `guest.blade.php`, `navigation.blade.php`)
  * `resources/views/auth/` (`login.blade.php`, `register.blade.php`, `forgot-password.blade.php`)
  * `resources/views/profile/edit.blade.php` и три формы в `profile/partials/`
  * Кнопки: `primary-button`, `secondary-button`, `danger-button`
  * `tailwind.config.js` (кастомная палитра), `resources/css/app.css`, `resources/js/app.js`
  * Локализация: `lang/ru.json`
* **База данных и тесты:**
  * `database/migrations/0001_01_01_000000_create_users_table.php` — добавлены поля для логина, возраста, описания и аватара.
  * `database/factories/UserFactory.php`
  * `tests/Feature/ProfileTest.php`, `tests/Feature/ExampleTest.php`, `tests/Feature/Auth/RegistrationTest.php`, `AuthenticationTest.php`

---

### 3. Код, написанный специально для MiuWish

#### Желания и бронирование
* **Модели:** `app/Models/Wishlist.php`, `Category.php`, `Reservation.php`
* **Контроллеры:** `WishlistController.php`, `ReservationController.php`, `HomeController.php`, `PublicProfileController.php` (в `app/Http/Controllers/`)
* **Бизнес-логика и хелперы:**
  * `app/Services/WishlistReservation.php`
  * `app/Support/StoredImage.php`, `WishlistFilters.php`
  * `app/Http/Requests/WishlistRequest.php`
* **Права доступа (Policies):** `app/Policies/WishlistPolicy.php`, `ReservationPolicy.php`
* **База данных:** миграции `create_categories_table`, `create_wishlists_table`, `create_reservations_table` и сидер `database/seeders/DatabaseSeeder.php`
* **Интерфейс:**
  * `resources/views/home.blade.php`, `profiles/show.blade.php`
  * Разделы `resources/views/wishes/`, `reservations/`
  * Страницы ошибок `errors/403.blade.php`, `404.blade.php`
  * Компонент `components/copy-link.blade.php`
* **Тесты:** `tests/Feature/ReservationTest.php`

#### Друзья и аватарки
* **Контроллер:** `app/Http/Controllers/FriendController.php`
* **База данных:** миграция `create_follows_table.php`
* **Интерфейс и скрипты:**
  * Шаблоны в `resources/views/friends/`
  * `resources/js/avatar-crop.js` — модальное окно обрезки фото на базе библиотеки **Cropper.js**
* **Тесты:** `tests/Feature/FollowTest.php`

#### Деплой и оформление
* **Скрипты развёртывания и веб-сервер:** `deploy/ubuntu/install.sh`, `deploy/nginx/miuwish.conf`, `deploy/windows/install-wsl.ps1`
* **Контейнеризация:** `Dockerfile`, `.dockerignore`
* **Графика:** `public/images/logo.jpg`

---

## 📄 Прочие файлы в репозитории
* `MIUТЕХНИЧЕСКОЕ ЗАДАНИЕ.docx` — документация с требованиями к проекту (не является частью кода).
* `.cursorrules`, `AGENTS.md`, `CLAUDE.md` — инструкции и контекст для AI-ассистентов в редакторе кода (не влияют на работу сайта).
