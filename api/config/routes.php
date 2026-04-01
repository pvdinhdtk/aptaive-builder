<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    register_rest_route('aptaive/v1', '/config', [
        [
            'methods'  => 'GET',
            'callback' => 'aptaive_get_config',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ],
        [
            'methods'  => 'POST',
            'callback' => 'aptaive_save_config',
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ],
    ]);
});

function aptaive_get_config()
{
    $config = get_option(
        APTAIVE_CONFIG_OPTION,
        aptaive_default_config()
    );
    $config = aptaive_normalize_config($config);
    return rest_ensure_response($config);
}

function aptaive_save_config(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    if (!$data || !is_array($data)) {
        return new WP_Error(
            'invalid_data',
            __('Invalid config', 'aptaive-builder'),
            ['status' => 400]
        );
    }

    $normalized = aptaive_normalize_config($data);

    if (($data['minAppVersion'] ?? null) !== $normalized['minAppVersion']) {
        return new WP_Error(
            'invalid_min_app_version',
            __('minAppVersion must be in x.y.z format', 'aptaive-builder'),
            ['status' => 422]
        );
    }

    update_option(APTAIVE_CONFIG_OPTION, $normalized, false);

    return [
        'success' => true,
        'data' => $normalized,
    ];
}
