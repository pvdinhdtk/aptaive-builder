<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('aptaive/v1', '/products', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_products',
        'permission_callback' => '__return_true', // public
    ]);

    register_rest_route('aptaive/v1', '/product/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_product_detail',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1', '/product-categories', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_product_categories',
        'permission_callback' => '__return_true',
    ]);
});
