<?php
/*
Plugin Name: Aptaive Builder
Plugin URI: https://app.taive.net
Description: Build downloadable mobile apps from WordPress using configurable APIs and layouts.
Version: 1.0.0
Author: Aptaive
Text Domain: aptaive
*/

defined('ABSPATH') || exit;

/**
 * =========================
 * Constants
 * =========================
 */
define('APTAIVE_PLUGIN_READY', defined('APTAIVE_JWT_SECRET'));
if (!APTAIVE_PLUGIN_READY) {
    add_action('admin_notices', function () {
        echo '<div class="notice notice-error"><p>
            Please define <code>APTAIVE_JWT_SECRET</code> in wp-config.php
        </p></div>';
    });
}

if (!defined('APTAIVE_BUILDER_PATH')) {
    if (function_exists('plugin_dir_path')) {
        define('APTAIVE_BUILDER_PATH', plugin_dir_path(__FILE__));
    } else {
        // Fallback cho PHPStan / CLI
        define('APTAIVE_BUILDER_PATH', __DIR__ . '/');
    }
}

if (!defined('APTAIVE_BUILDER_URL')) {
    if (function_exists('plugin_dir_url')) {
        define('APTAIVE_BUILDER_URL', plugin_dir_url(__FILE__));
    } else {
        define('APTAIVE_BUILDER_URL', '');
    }
}

/**
 * =========================
 * Load core config / helpers
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'config/constants.php';
require_once APTAIVE_BUILDER_PATH . 'api/helpers/response.php';
require_once APTAIVE_BUILDER_PATH . 'api/helpers/handler.php';


// Load migrate logic
require_once APTAIVE_BUILDER_PATH . '/config/migrations.php';
register_activation_hook(__FILE__, 'aptaive_on_activate');

/**
 * =========================
 * Admin (Builder UI)
 * =========================
 */
if (is_admin()) {
    require_once APTAIVE_BUILDER_PATH . 'admin/menu.php';
    require_once APTAIVE_BUILDER_PATH . 'admin/enqueue.php';
}

/**
 * =========================
 * API: App Config
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'api/config/routes.php';

/**
 * =========================
 * AUTH APIs
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'api/jwt/encoder.php';
require_once APTAIVE_BUILDER_PATH . 'api/jwt/decoder.php';

require_once APTAIVE_BUILDER_PATH . 'api/auth/auth-exception.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/middleware.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/login.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/register.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/refresh.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/me.php';
require_once APTAIVE_BUILDER_PATH . 'api/auth/routes.php';

/**
 * =========================
 * PRODUCTS APIs
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'api/products/routes.php';
require_once APTAIVE_BUILDER_PATH . 'api/products/list.php';
require_once APTAIVE_BUILDER_PATH . 'api/products/detail.php';
require_once APTAIVE_BUILDER_PATH . 'api/products/categories.php';

/**
 * =========================
 * CART / CHECKOUT / ORDERS
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'api/cart/routes.php';
require_once APTAIVE_BUILDER_PATH . 'api/cart/products.php';

require_once APTAIVE_BUILDER_PATH . 'api/checkout/checkout.php';
require_once APTAIVE_BUILDER_PATH . 'api/checkout/routes.php';

require_once APTAIVE_BUILDER_PATH . 'api/orders/routes.php';
require_once APTAIVE_BUILDER_PATH . 'api/orders/create.php';
require_once APTAIVE_BUILDER_PATH . 'api/orders/list.php';
require_once APTAIVE_BUILDER_PATH . 'api/orders/detail.php';

/**
 * =========================
 * POSTS / PAGES APIs
 * =========================
 */
require_once APTAIVE_BUILDER_PATH . 'api/posts/routes.php';
require_once APTAIVE_BUILDER_PATH . 'api/posts/categories.php';
require_once APTAIVE_BUILDER_PATH . 'api/posts/list.php';
require_once APTAIVE_BUILDER_PATH . 'api/posts/detail.php';

require_once APTAIVE_BUILDER_PATH . 'api/pages/routes.php';
require_once APTAIVE_BUILDER_PATH . 'api/pages/detail.php';
