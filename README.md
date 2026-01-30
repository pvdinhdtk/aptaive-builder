aptaive-builder/
│
├─ aptaive-builder.php        ← file plugin chính
│
├─ admin/
│  ├─ build/ 
│  ├─ dev/                       ← ❌ CHỈ DEV (KHÔNG SHIP)
│  │  └─ src.php
│  │  └─ package.json
│  │  └─ node_modules/
│  ├─ enqueue.php
│  ├─ menu.php
│  │
├─ api/
│  ├─ config/
│  │  └─ routes.php
│  ├─ products/
│  └─ posts/
├─ config/
│  ├─ constants.php
└─ README.md

php -d memory_limit=1G vendor/bin/phpstan analyse

## build

bash bin/release.sh

## Installation

Add this to your wp-config.php:

```php
define('APTAIVE_JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_SECRET');