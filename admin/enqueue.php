<?php
defined('ABSPATH') || exit;

/**
 * Enqueue admin React app for Aptaive Builder
 */
add_action('admin_enqueue_scripts', function ($hook) {

    /**
     * Chỉ load script ở đúng các trang của plugin
     */
    if (!str_contains($hook, 'aptaive-builder')) {
        return;
    }

    /**
     * 🔥 BẮT BUỘC: load WordPress media (cho MediaUpload)
     */
    wp_enqueue_media();

    $build_path = APTAIVE_BUILDER_PATH . 'admin/build/';
    $build_url  = APTAIVE_BUILDER_URL  . 'admin/build/';

    $asset_file = $build_path . 'index.tsx.asset.php';

    /**
     * Chưa build thì không load gì cả
     */
    if (!file_exists($asset_file)) {
        return;
    }

    $asset = require $asset_file;

    /**
     * Enqueue JS (React app)
     */
    wp_enqueue_script(
        'aptaive-admin',
        $build_url . 'index.tsx.js',
        $asset['dependencies'],
        $asset['version'],
        true
    );

    /**
     * Enqueue CSS nếu tồn tại
     */
    if (file_exists($build_path . 'index.tsx.css')) {
        wp_enqueue_style(
            'aptaive-admin',
            $build_url . 'index.tsx.css',
            [],
            $asset['version']
        );
    }
});
