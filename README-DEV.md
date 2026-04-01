php -d memory_limit=1G vendor/bin/phpstan analyse

## build

bash bin/release.sh

## Installation

Add this to your wp-config.php:

```php
define('APTAIVE_JWT_SECRET', 'CHANGE_THIS_TO_A_RANDOM_SECRET');
```

//Xoá cấu hình
DELETE FROM wp_options WHERE option_name = 'aptaive_builder_config';
DELETE FROM wp_usermeta WHERE meta_key = 'aptaive_refresh_token';
