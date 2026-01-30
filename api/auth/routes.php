<?php

add_action('rest_api_init', function () {
    register_rest_route('aptaive/v1/auth', '/login', [
        'methods'  => 'POST',
        'callback' => 'aptaive_login',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1/auth', '/register', [
        'methods'  => 'POST',
        'callback' => 'aptaive_register',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1/auth', '/refresh', [
        'methods'  => 'POST',
        'callback' => 'aptaive_refresh',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1/auth', '/me', [
        'methods'  => 'GET',
        'callback' => 'aptaive_me',
        'permission_callback' => '__return_true',
    ]);
});
