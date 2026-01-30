<?php

defined('ABSPATH') || exit;

add_action('rest_api_init', function () {
    register_rest_route('aptaive/v1', '/checkout', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_checkout_data',
        'permission_callback' => '__return_true',
    ]);
});
