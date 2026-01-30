<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    register_rest_route('aptaive/v1', '/post-categories', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_post_categories',
        'permission_callback' => '__return_true', // public
    ]);

    register_rest_route('aptaive/v1', '/posts', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_posts',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('aptaive/v1', '/post/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_post_detail',
        'permission_callback' => '__return_true',
    ]);
});
