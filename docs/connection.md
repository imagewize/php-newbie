# Database Connection

This document explains how the CMS handles database connections using PDO and the Singleton pattern.

## PDO vs MySQLi

This project uses **PDO (PHP Data Objects)** instead of MySQLi for database access.

### Comparison

| Feature | PDO | MySQLi |
|---------|-----|--------|
| Database support | MySQL, PostgreSQL, SQLite, Oracle, SQL Server, etc. | MySQL only |
| Prepared statements | Yes, consistent syntax | Yes, but two APIs (prepared vs direct) |
| Named parameters | Yes (`:name`) | No, only `?` placeholders |
| Error handling | Exceptions by default | Mixed (can use exceptions) |
| Fetch modes | Multiple (objects, arrays, classes) | Arrays mostly |
| Security | Automatic escaping in prepared statements | Same |

### Why PDO was chosen

1. **Future-proof**: If you ever switch from MySQL to PostgreSQL or SQLite, only the connection string in `Database.php` needs to change. Your `User.php` and `Post.php` classes remain unchanged.

2. **Named parameters**: Cleaner queries with readable placeholders:
   ```php
   // PDO
   $db->query("INSERT INTO users (name, email) VALUES (:name, :email)");
   
   // MySQLi would require
   $db->query("INSERT INTO users (name, email) VALUES (?, ?)");
   ```

3. **Consistent API**: PDO uses the same methods across all supported databases.

4. **Better error handling**: PDO exceptions are more predictable and configurable.

5. **Fetch flexibility**: Results can be fetched as objects, associative arrays, or custom classes.

6. **Industry standard**: Most modern PHP frameworks (Laravel, Symfony) use PDO.

## Singleton Pattern

The `Database` class implements the **Singleton pattern** to ensure only one database connection exists throughout the application.

### The Singleton Implementation

In `includes/Database.php`:

```php
class Database {
    private PDO $connection;
    private static ?self $instance = null;

    private function __construct() {
        // Create the PDO connection
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
```

### Understanding `private static ?self $instance = null;`

| Part | Meaning |
|------|---------|
| `private` | Only accessible within the `Database` class |
| `static` | Belongs to the **class itself**, not to individual objects |
| `?self` | Type hint: can be an instance of `Database` (`self`) **or** `null` |
| `$instance = null` | Initially, no instance exists |

### How it works

1. The constructor is `private`, so you cannot create a Database instance with `new Database()`.

2. You must use `Database::getInstance()` to get the Database object.

3. On the first call to `getInstance()`:
   - `$instance` is `null`
   - A new `Database` object is created
   - The object is stored in the static `$instance` property

4. On subsequent calls to `getInstance()`:
   - `$instance` is **not** `null`
   - The existing instance is returned

### Why this matters

1. **One connection only** - Instead of creating a new MySQL connection every time you need the database, you reuse the same one.

2. **Efficiency** - Opening a database connection is resource-intensive. The Singleton ensures it happens only once per request.

3. **Consistency** - All your models (`User`, `Post`) use the exact same connection, preventing transaction issues and ensuring data consistency.

### In practice

```php
// Both use the SAME connection:
$user = new User();      // Calls Database::getInstance() internally
$post = new Post();      // Calls Database::getInstance() again
                       // Returns the SAME $instance
```

Without the Singleton pattern, every `new User()` or `new Post()` would create a **new** database connection, wasting server resources.

## Configuration

Database connection settings are defined in `config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

These constants are used by the `Database` class when creating the PDO connection.

## Security

The Database class uses PDO with the following security settings:

- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` - Throws exceptions on errors (better for debugging)
- `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` - Returns associative arrays by default
- `PDO::ATTR_EMULATE_PREPARES => false` - Uses **real** prepared statements (not emulated), which provides true protection against SQL injection

All queries in the User and Post models use **prepared statements with parameter binding**, which automatically escapes user input and prevents SQL injection attacks.
