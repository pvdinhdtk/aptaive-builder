<?php

defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('aptaive/v1', '/cart/products', [
        'methods'  => 'POST',
        'callback' => 'aptaive_get_cart_products',
        'permission_callback' => '__return_true', // public
    ]);
});
