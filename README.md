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

- PHP 8.2+
- Composer
- Laravel 11
- Будь-який вебсервер (достатньо `php artisan serve`)
- Редактор коду (VS Code / PhpStorm тощо)
- **Без MySQL** — використовується SQLite.

---

## 2. Створення базового проєкту Laravel

```bash
composer create-project laravel/laravel contacts-app "11.*"
cd contacts-app
```

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

## 4. Налаштування SQLite у `.env`

Відкрийте `.env` у корені проєкту та замініть блок із БД на:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# ці змінні для sqlite не критичні, але можна залишити як є
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=null
DB_PASSWORD=null
```

Також переконайтесь, що в `config/database.php` для зʼєднання `sqlite`
використовується `database_path('database.sqlite')` (у Laravel 11 це так за замовчуванням).

У папці `database/` вже є **порожній файл** `database.sqlite`. Якщо його немає
(або ви створюєте проєкт з нуля) — просто створіть порожній файл вручну:

- у файловому менеджері — **New → Empty file → database.sqlite** у директорії `database/`,
- або командою:

```bash
type NUL > database/database.sqlite   # Windows (PowerShell / cmd)
# або
touch database/database.sqlite       # Linux / macOS
```

---

## 5. Міграції та сидери

Виконайте:

```bash
php artisan migrate
php artisan db:seed
```

Це створить структуру таблиць в `database/database.sqlite`:

- `contact_groups`
- `contacts`

та заповнить їх початковими даними.

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
