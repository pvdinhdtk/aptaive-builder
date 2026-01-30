<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    register_rest_route('aptaive/v1', '/orders/create', [
        'methods'  => 'POST',
        'callback' => 'aptaive_create_order',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1', '/orders', [
        'methods'             => 'GET',
        'callback'            => 'aptaive_get_orders',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1', '/order/(?P<order_id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_order_detail',
        'permission_callback' => '__return_true',
        'args' => [
            'order_id' => [
                'required' => true,
                'validate_callback' => function ($param) {
                    return is_numeric($param);
                },
            ],
            'key' => [
                'required' => false,
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ]);
});
