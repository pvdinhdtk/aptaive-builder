<?php
defined('ABSPATH') || exit;

add_action('admin_menu', function () {

    add_menu_page(
        'Aptaive Builder',
        'Aptaive Builder',
        'manage_options',
        'aptaive-builder',
        'aptaive_render_admin',
        'dashicons-smartphone',
        3
    );

    // submenu mặc định → App Settings
    add_submenu_page(
        'aptaive-builder',
        'App Settings',
        'App Settings',
        'manage_options',
        'aptaive-builder',
        'aptaive_render_admin'
    );

    add_submenu_page(
        'aptaive-builder',
        'App Layouts',
        'App Layouts',
        'manage_options',
        'aptaive-builder-layouts',
        'aptaive_render_admin'
    );
});

function aptaive_render_admin()
{
    echo '<div id="aptaive-admin-root"></div>';
}
