# PHP CMS Starter

A lightweight CMS built with PHP, MySQL, and PDO. This project demonstrates clean architecture with separated concerns, type safety, and modern PHP practices.

## Teaching stages

This repo is structured as a tutorial: the same CMS, rebuilt in progressively more organized stages. Each stage is a branch you can check out and run on its own.

| Stage | Branch | What it shows |
|-------|--------|----------------|
| 0 | [`stage-0-flat-script`](../../tree/stage-0-flat-script) | Everything in one `index.php` - no classes, no models, no views. |
| 1 | [`stage-1-basic-mvc`](../../tree/stage-1-basic-mvc) | Models (`includes/`) and views (`views/`) split out; routing still inline in `index.php`. |
| 2 | [`stage-2-front-controller`](../../tree/stage-2-front-controller) | Front Controller pattern: a `Router` and dedicated `Controller` classes handle requests. |
| 3 | [`stage-3-view-layer`](../../tree/stage-3-view-layer) | A `View` class replaces manual `ob_start()`/`include` in `index.php` for rendering templates and layouts. |

`stage-2-front-controller` and `stage-3-view-layer` are frozen checkpoints of `main` as of their respective commits - `main` keeps evolving past them, so they may drift over time.

`git diff stage-0-flat-script stage-1-basic-mvc`, `git diff stage-1-basic-mvc stage-2-front-controller`, and `git diff stage-2-front-controller stage-3-view-layer` show exactly what changed at each step.

## Features

- **User Management**: Registration, login/logout, role-based access (admin/user)
- **Post Management**: Create, read, update, delete posts with user ownership
- **PDO Database**: Secure database access with prepared statements
- **Singleton Pattern**: Efficient single database connection
- **MVC-like Structure**: Separated views from business logic
- **Type Hints**: Full PHP type declarations for better IDE support

## Structure

```
php-newbie/
├── config.php              # Configuration (DB, site settings, autoloader)
├── index.php               # Front controller
├── setup.sql               # MySQL database schema + sample data
├── README.md
├── docs/
│   └── connection.md       # Database connection documentation
└── includes/
    ├── Database.php        # PDO connection with Singleton pattern
    ├── User.php            # User model with auth
    └── Post.php            # Post model with CRUD
└── views/
    ├── layout.php          # Base template
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

## Usage

### Login

```php
$user = new User();
$user->login('email@example.com', 'password');
```

### Get all users

```php
$user = new User();
$allUsers = $user->getAll();
```

### Get all published posts

```php
$post = new Post();
$posts = $post->getPublished();
```

### Create a post

```php
$post = new Post();
$post->create([
    'title' => 'My Post',
    'content' => 'Post content here',
    'status' => 'published'
]);
```

## Architecture

### Singleton Pattern

The `Database` class uses the Singleton pattern to ensure only one database connection exists per request:

```php
$db = Database::getInstance();
```

### Separation of Concerns

- **Controllers**: `index.php` handles HTTP requests and routing
- **Models**: `User.php`, `Post.php` handle data and business logic
- **Views**: `views/*.php` handle presentation
- **Configuration**: `config.php` centralizes settings

### Type Safety

All classes use PHP type hints for properties, parameters, and return values:

```php
class Database {
    private PDO $connection;
    private static ?self $instance = null;
    
    public static function getInstance(): self { ... }
    public function query(string $sql, array $params = []): PDOStatement { ... }
}
```

## Security

- **Prepared Statements**: All SQL queries use PDO prepared statements
- **Password Hashing**: Uses `password_hash()` and `password_verify()`
- **HTML Escaping**: Uses `htmlspecialchars()` for output
- **CSRF Protection**: Recommended to add for forms in production
- **SQL Injection**: Prevented by PDO prepared statements with `ATTR_EMULATE_PREPARES => false`

## Documentation

- [Database Connection & Singleton Pattern](docs/connection.md)

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
