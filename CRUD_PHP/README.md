# PHP MySQL CRUD Application

A simple article management system built with PHP and MySQL using the MVC design pattern.

## Features

- Create, Read, Update, and Delete articles
- Clean MVC architecture
- Secure PDO prepared statements
- Input validation and XSS protection

## Technologies

- PHP 8.0+
- MySQL 8.0+
- PDO for database access
- MVC design pattern

## Quick Start

### 1. Create Database

```sql
CREATE DATABASE crud_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'your_username'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON crud_app.* TO 'your_username'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Import Schema

```bash
mysql -u your_username -p crud_app < sql/data.sql
```

### 3. Configure Database

Create `config/database.php` (see project structure for template) and add your credentials:

```php
$host = 'localhost';
$db   = 'crud_app';
$user = 'your_username';
$pass = 'your_password';
```

### 4. Run Application

```bash
php -S localhost:8000
```

Open `http://localhost:8000` in your browser.

## Project Structure

```
CRUD_PHP/
├── config/          # Database configuration (not in git)
├── controllers/     # Request handling
├── models/          # Database operations
├── views/           # HTML templates
├── sql/             # Database schema
└── index.php        # Application entry point
```

## Security

- PDO prepared statements prevent SQL injection
- XSS protection with `htmlspecialchars()`
- Server-side input validation
- Database credentials excluded from git

## License

Educational project for learning purposes.
