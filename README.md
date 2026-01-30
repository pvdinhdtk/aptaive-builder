# Aptaive Builder

Aptaive Builder is a WordPress plugin that allows you to build configurable mobile app layouts and APIs from WordPress.

This plugin is designed for **traditional WordPress environments** and does **not** require Composer or Node.js at runtime.

---

## ✨ Features

- Visual app layout configuration in WordPress Admin
- Configurable sections (Slider, Category Grid, Product List, etc.)
- JSON-based layout output for mobile apps
- Custom REST API endpoints
- Clean separation between admin UI and API logic

---

## 📦 Requirements

- WordPress 6.0+
- PHP 7.4+

---

## 🔐 Required Configuration

This plugin **requires** a JWT secret key to be defined.

Add the following line to your `wp-config.php`:

```php
define('APTAIVE_JWT_SECRET', 'CHANGE_THIS_SECRET_KEY');
