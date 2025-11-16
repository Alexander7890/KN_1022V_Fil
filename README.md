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

## 2. Швидке автоматичне налаштування

У репозиторії є готові скрипти, які виконують **усі кроки** нижче:

- `scripts/setup.sh` — для Linux/macOS;
- `scripts/setup.ps1` — для Windows PowerShell (Core або Windows PowerShell 5+).

Приклади запуску (у корені репозиторію):

```bash
# Linux / macOS
./scripts/setup.sh contacts-app

# Windows PowerShell
pwsh ./scripts/setup.ps1 -TargetDir contacts-app
```

Скрипт:

1. Створює чистий каркас Laravel 11 (`composer create-project`).
2. Копіює кастомні каталоги (`app`, `routes`, `resources`, `database`).
3. Підставляє `.env` із налаштованим SQLite (`.env.example`).
4. Створює файл `database/database.sqlite` (якщо його ще нема).
5. Запускає `php artisan key:generate`, `php artisan migrate`, `php artisan db:seed`.

Після завершення достатньо перейти у вказану директорію та виконати `php artisan serve`.

> Якщо хочете виконати всі кроки вручну — дотримуйтесь інструкцій нижче.

---

## 3. Створення базового проєкту Laravel

```bash
composer create-project laravel/laravel contacts-app "11.*"
cd contacts-app
```

> 💡 **Якщо Composer повідомляє про відсутність `ext-fileinfo`, потрібно увімкнути
> відповідне розширення PHP.**
>
> ### Windows (php.ini)
> 1. Визначте файл конфігурації CLI: `php --ini`.
> 2. Відкрийте зазначений `php.ini` та знайдіть рядок `;extension=fileinfo`.
> 3. Приберіть крапку з комою (`extension=fileinfo`) і перезапустіть термінал.
> 4. Перевірте, що розширення активне: `php -m | findstr fileinfo`.
>
> ### macOS / Linux
> - Homebrew / apt / dnf: встановіть або активуйте пакет `php-fileinfo`
>   (наприклад, `sudo apt install php-fileinfo`).
> - Для збірок із `php.ini` також переконайтесь, що рядок `extension=fileinfo`
>   не закоментований.
>
> Після увімкнення розширення перезапустіть команду `composer create-project` —
> саме воно встановлює `vendor/` та створює `artisan`.

Перевірка запуску "голого" проєкту:

```bash
php artisan serve
# http://127.0.0.1:8000
```

Зупиніть сервер (Ctrl+C).

---

## 4. Копіювання файлів з архіву

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

## 5. Налаштування SQLite у `.env`

У корені проєкту вже є `.env.example` із налаштованим SQLite. Після запуску
скрипта (або вручну скопіюйте `.env.example` у `.env`) переконайтесь, що блок БД
має вигляд:

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

## 6. Міграції та сидери

Виконайте:

```bash
php artisan migrate
php artisan db:seed
```

Це створить структуру таблиць в `database/database.sqlite`:

- `contact_groups`
- `contacts`

Якщо у проєкті присутня стандартна таблиця `users`, сидер автоматично
створить/оновить тестового користувача (`test@example.com` / `password`) через
`updateOrInsert`, тому повторний запуск `db:seed` не спричинить помилки
унікальності.

та заповнить їх початковими даними.

---

## 7. Запуск застосунку

```bash
php artisan serve
```

Відкрийте в браузері:

```text
http://127.0.0.1:8000/contacts
```

> **Поширена помилка (Windows/PowerShell):** URL не потрібно вводити як команду в
> терміналі — так PowerShell видає `CommandNotFoundException`. Замість цього
> відкрийте посилання у браузері (скопіюйте адресу в Chrome/Edge/Firefox). Якщо
> хочете запустити браузер із терміналу, скористайтесь командами типу
> `start http://127.0.0.1:8000/contacts` (PowerShell / CMD) або
> `xdg-open http://127.0.0.1:8000/contacts` (Linux).

---

## 8. Реалізований функціонал

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
