# PHP CMS Starter

> **Teaching stage 0 of 4: Flat script.** Everything lives in `index.php` - the database connection, request handling, SQL queries, and HTML output. There are no classes, no models, and no separate view files. This is the starting point before any MVC-style split. See `stage-1-views` for the next step (the same script with its markup pulled into view files), or `main` for the latest stage.

A lightweight CMS built with PHP, MySQL, and PDO. This stage keeps everything in a single procedural script so the whole request/response cycle is visible top to bottom in one file.

## Features

- **User Management**: Login/logout, role-based access (admin/user)
- **Post Management**: Read published posts, listed with their author
- **PDO Database**: Secure database access with prepared statements
- **Single File**: No classes, no models, no views - `index.php` handles the whole request

## Structure

```
php-newbie/
├── config.php              # Configuration (DB, site settings)
├── index.php               # Everything: DB connection, request handling, HTML output
├── setup.sql               # MySQL database schema + sample data
└── README.md
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
git checkout stage-0-flat-script
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
6. Print the HTML directly, with PHP tags mixed into the markup.

There's no router, no controller classes, and no template files - the next stage (`stage-1-views`) pulls the markup out into view files, still without introducing a single class.

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
