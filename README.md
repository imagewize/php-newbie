# PHP CMS Starter

> **Teaching stage 1 of 4: Views extracted.** The script is still completely procedural - raw `PDO`, no classes, no models, no router - but the HTML has moved out of `index.php` into `views/`. The one new idea is separating what a page *does* from what it *looks like*. See `stage-0-flat-script` for the single-file starting point, `stage-2-basic-mvc` for the next step (model classes), or `main` for the latest stage.

A lightweight CMS built with PHP, MySQL, and PDO. This stage takes stage 0's flat script and pulls the markup into template files, without introducing a single class.

## Features

- **User Management**: Login/logout, role-based access (admin/user)
- **Post Management**: Read published posts, listed with their author
- **PDO Database**: Secure database access with prepared statements
- **Separate Views**: Markup lives in `views/`; `index.php` handles only logic and data

## Structure

```
php-newbie/
├── config.php              # Configuration (DB, site settings)
├── index.php               # DB connection, request handling, data fetching
├── setup.sql               # MySQL database schema + sample data
├── README.md
└── views/
    ├── layout.php          # Page shell: <head>, header, prints $content
    ├── home.php            # Posts and users display
    └── login.php           # Login form
```

## Requirements

- PHP 8.0+
- MySQL 5.7+
- PDO MySQL extension enabled
- GitHub CLI (`gh`) for deployment (optional)

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/imagewize/php-newbie.git
cd php-newbie
git checkout stage-1-views
```

### 2. Create the database

Import the database schema and sample data:

```bash
mysql -u root -p < setup.sql
```

Or use phpMyAdmin to import `setup.sql` file.

### 3. Configure database connection

Edit `config.php` with your MySQL credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 4. Run the application

Point your web server to the project directory and visit:

```
http://localhost/php-newbie
```

### 5. Login

Use the demo credentials:
- **Email**: admin@example.com
- **Password**: password123

## Configuration

| Constant | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | MySQL host | localhost |
| `DB_NAME` | Database name | cms_db |
| `DB_USER` | Database user | root |
| `DB_PASS` | Database password | (empty) |
| `SITE_NAME` | Site title | My CMS |
| `SITE_URL` | Site URL | http://localhost/php-newbie |

## Database Schema

### Users Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | User ID |
| name | VARCHAR(100) | Display name |
| email | VARCHAR(100) | Unique email |
| password | VARCHAR(255) | Hashed password |
| role | ENUM('admin', 'user') | User role |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Last update time |

### Posts Table

| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AI) | Post ID |
| title | VARCHAR(255) | Post title |
| content | TEXT | Post content |
| user_id | INT (FK) | Author ID |
| status | ENUM('draft', 'published') | Post status |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Last update time |

## How it works

`index.php` runs top to bottom on every request:

1. Connect to MySQL with `PDO`.
2. Read `?action=` from the query string and figure out what's being requested (home, login, logout).
3. If the request is a login POST, look up the user and verify the password.
4. If the request is a logout, clear the session and redirect.
5. Fetch whatever data the page needs (published posts, and the user list if logged in) with plain `$pdo->query()` / `$pdo->prepare()` calls.
6. Render a view, then wrap it in the layout.

Steps 1-5 are byte-for-byte the same as stage 0. Only step 6 changed.

### The view/layout trick

```php
ob_start();                     // start capturing output
include 'views/home.php';       // this prints markup...
$content = ob_get_clean();      // ...but it lands in $content instead of the browser
include 'views/layout.php';     // the layout prints $content inside the page shell
```

Two things are worth pausing on, because they're the parts that surprise people:

- **`ob_start()` / `ob_get_clean()`** is PHP's output buffer. Between those two calls, anything a script prints is captured into a string rather than sent to the browser. That's what lets a page's markup be rendered *before* the layout that surrounds it.
- **Included files share the including file's variable scope.** `views/home.php` never declares `$posts` - it can just use it, because `index.php` defined it earlier. This is convenient and also the main weakness of this approach: nothing states which variables a view depends on, so it's easy to break a template by renaming a variable somewhere else.

Later stages address exactly that: `stage-2-basic-mvc` moves the data access into model classes, and the view layer eventually gets its own class that passes data in explicitly instead of relying on ambient scope.

## Security

Even at this early stage, the basics are non-negotiable:

- **Prepared Statements**: All SQL queries use PDO prepared statements
- **Password Hashing**: Uses `password_hash()` and `password_verify()`
- **HTML Escaping**: Uses `htmlspecialchars()` for output
- **SQL Injection**: Prevented by PDO prepared statements with `ATTR_EMULATE_PREPARES => false`

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-source and available under the [MIT License](LICENSE).

---

Built with PHP and MySQL. Maintained by [imagewize](https://github.com/imagewize).
