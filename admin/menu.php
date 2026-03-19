<?php
defined('ABSPATH') || exit;

add_action('admin_menu', function () {

    add_menu_page(
        __('Aptaive Builder', 'aptaive-builder'),
        __('Aptaive Builder', 'aptaive-builder'),
        'manage_options',
        'aptaive-builder',
        'aptaive_render_admin',
        'dashicons-smartphone',
        3
    );

    // submenu mặc định → App Settings
    add_submenu_page(
        'aptaive-builder',
        __('App Settings', 'aptaive-builder'),
        __('App Settings', 'aptaive-builder'),
        'manage_options',
        'aptaive-builder',
        'aptaive_render_admin'
    );

    add_submenu_page(
        'aptaive-builder',
        __('App Layouts', 'aptaive-builder'),
        __('App Layouts', 'aptaive-builder'),
        'manage_options',
        'aptaive-builder-layouts',
        'aptaive_render_admin'
    );

    add_submenu_page(
        'aptaive-builder',
        __('Build App', 'aptaive-builder'),
        __('Build App', 'aptaive-builder'),
        'manage_options',
        'aptaive-builder-publish',
        'aptaive_render_admin'
    );
});

function aptaive_render_admin()
{
    echo '<div id="aptaive-admin-root"></div>';
}
