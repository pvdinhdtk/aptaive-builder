<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    register_rest_route('aptaive/v1', '/page/(?P<id>\d+)', [
        'methods'  => 'GET',
        'callback' => 'aptaive_get_page_detail',
        'permission_callback' => '__return_true',
    ]);
});
