# Laravel 11 Contacts App (SQLite)

Навчальний проєкт на **Laravel 11** для керування контактами (Contact) та групами (Group),
налаштований під **SQLite**, без MySQL.

Реалізовано:

- 2 сутності: `Contact` та `Group` (`contact_groups`).
- Звʼязок **one-to-many**: одна група → багато контактів.
- CRUD для контактів:
  - список з пошуком, фільтрацією за групою та простою пагінацією;
  - створення;
  - редагування;
  - видалення з підтвердженням.
- Blade-шаблони для списку та форми.
- Валідація на сервері через `$request->validate(...)`.
- Міграції для таблиць та сидери з тестовими даними.
- Працює на **SQLite** (файл `database/database.sqlite`).

> **Важливо:** архів містить *користувацькі* файли (моделі, контролер, міграції, сидери,
> Blade-шаблони, маршрути) + порожній файл БД `database/database.sqlite`.
> Сам каркас Laravel (vendor, artisan, config тощо) потрібно створити командою
> `composer create-project`, а потім **замінити/додати** файли з цього архіву.

---

## 1. Вимоги

- PHP 8.2+ з увімкненими розширеннями `fileinfo`, `zip`, `pdo_sqlite` та `openssl`.
- Composer 2.x.
- Laravel 11 (встановлюється через `composer create-project`).
- Будь-який вебсервер (достатньо `php artisan serve`).
- Редактор коду (VS Code / PhpStorm тощо).
- **Без MySQL** — використовується SQLite.

> 🔧 **Як увімкнути відсутні розширення PHP (Windows):**
> 1. Визначте, яке `php.ini` використовується, командою `php --ini`.
> 2. Відкрийте цей файл і знайдіть рядки `;extension=fileinfo`, `;extension=zip`,
>    `;extension=pdo_sqlite`.
> 3. Приберіть крапку з комою на початку (`extension=fileinfo`).
> 4. Збережіть файл і перезапустіть термінал. Перевірте, що розширення підвантажилося: `php -m | findstr fileinfo`.
> 5. Повторіть запуск `composer`.

Без `fileinfo` або `zip` Composer не зможе завантажити залежності і ви отримаєте помилку
на кшталт: `league/flysystem-local require ext-fileinfo * -> it is missing from your system`. 
Увімкнення розширень вирішує проблему.

---

## 2. Створення базового проєкту Laravel

```bash
composer create-project laravel/laravel contacts-app "11.*"
cd contacts-app
```

> Якщо Composer повідомляє, що не може завантажити пакет через відсутність
> розширення `fileinfo` / `zip`, поверніться до розділу «Вимоги» та увімкніть їх у `php.ini`.

Перевірка запуску "голого" проєкту:

```bash
php artisan serve
# http://127.0.0.1:8000
```

Зупиніть сервер (Ctrl+C).

---

## 3. Копіювання файлів з архіву

1. Розпакуйте архів `contacts-app-laravel-sqlite.zip` у будь‑яку тимчасову папку.
2. Всередині буде папка `contacts-app-sqlite/...`.
3. Скопіюйте **вміст цієї папки** поверх створеного Laravel‑проєкту, зберігаючи структуру директорій:

   - `app/Models/Contact.php`
   - `app/Models/Group.php`
   - `app/Http/Controllers/ContactController.php`
   - `routes/web.php`
   - `resources/views/layouts/app.blade.php`
   - `resources/views/contacts/index.blade.php`
   - `resources/views/contacts/form.blade.php`
   - `database/migrations/2025_11_16_000000_create_contact_groups_table.php`
   - `database/migrations/2025_11_16_000010_create_contacts_table.php`
   - `database/seeders/ContactGroupSeeder.php`
   - `database/seeders/ContactSeeder.php`
   - `database/seeders/DatabaseSeeder.php`
   - `database/database.sqlite` (порожній файл БД)

Якщо система запитає "замінити файл?" — погоджуйтесь (особливо для `routes/web.php`
та `database/seeders/DatabaseSeeder.php`).

---

## 4. Налаштування `.env`

1. Скопіюйте `.env.example` у `.env`, якщо цього ще не зроблено (`cp .env.example .env`).
2. Згенеруйте APP_KEY (потрібно лише один раз):

   ```bash
   php artisan key:generate
   ```

3. Замініть блок налаштувань БД на конфігурацію SQLite:

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite

   # решта змінних для sqlite не критичні, але можна залишити як у прикладі
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_USERNAME=null
   DB_PASSWORD=null
   ```

4. Переконайтесь, що у `database/` існує **порожній файл** `database.sqlite`.
   Якщо його немає — створіть вручну:

   ```bash
   type NUL > database/database.sqlite   # Windows (PowerShell / cmd)
   # або
   touch database/database.sqlite       # Linux / macOS
   ```

---

## 5. Міграції та сидери

1. Встановіть залежності (якщо ще не робили після `composer create-project`):

   ```bash
   composer install
   ```

2. Запустіть міграції та сидери:

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

   Це створить структуру таблиць у `database/database.sqlite` та заповнить їх тестовими даними.

> Якщо бачите помилку `Failed opening required vendor/autoload.php`, це означає,
> що залежності не встановлено. Спочатку виконайте `composer install`.

---

## 6. Запуск застосунку

```bash
php artisan serve
```

Відкрийте в браузері:

```text
http://127.0.0.1:8000/contacts
```

---

## 7. Реалізований функціонал

### 7.1. Сутності та зв’язки

- `Group` (`contact_groups`):
  - `id`
  - `name` (унікальне, до 150 символів)
  - `hasMany(Contact::class, 'group_id')`.

- `Contact` (`contacts`):
  - `id`
  - `name` (required, max 150)
  - `email` (required, email, max 180)
  - `phone` (nullable, max 50)
  - `note` (nullable)
  - `group_id` (nullable, foreign key → `contact_groups.id`).

### 7.2. CRUD-операції

Маршрути (`routes/web.php`):

- `GET /contacts` — список контактів з:
  - пошуком по `name`, `email`, `phone`, `note`;
  - фільтром за групою;
  - простою пагінацією (`page`, `perPage`).
- `GET|POST /contacts/new` — створення контакту.
- `GET|POST /contacts/{id}/edit` — редагування.
- `POST /contacts/{id}/delete` — видалення з підтвердженням.
- `/` редіректить на `/contacts`.

Валідація у контролері через `$request->validate([...])`.

### 7.3. UI (Blade)

- `layouts/app.blade.php` — базовий макет.
- `contacts/index.blade.php` — список + пошук + фільтр + пагінація.
- `contacts/form.blade.php` — форма створення/редагування + виведення помилок.

---

## 8. Для звіту / захисту

Проєкт демонструє вміння:

1. Створювати базовий Laravel‑проєкт і налаштовувати підключення до БД (у нашому випадку SQLite).
2. Описувати структуру таблиць через міграції.
3. Реалізовувати моделі Eloquent та зв’язки **One‑to‑Many**.
4. Налаштовувати маршрути та контролери для CRUD‑операцій.
5. Використовувати серверну валідацію форм.
6. Побудувати інтерфейс на Blade‑шаблонах.
7. Реалізувати пошук, фільтрацію та пагінацію.

---

## 9. Швидкий чекліст запуску (TL;DR)

```bash
# 1. Клон/розпакування архіву з користувацькими файлами не потрібні,
#    якщо ви вже перебуваєте у готовому репозиторії.

composer create-project laravel/laravel contacts-app "11.*"
cd contacts-app
# (скопіюйте файли з цього репозиторію поверх щойно створеного каркасу)

cp .env.example .env
php artisan key:generate

# увімкніть fileinfo/zip/pdo_sqlite у php.ini, якщо Composer скаржиться

composer install
php artisan migrate --seed

php artisan serve
# відкрийте http://127.0.0.1:8000/contacts
```
