<?php
defined('ABSPATH') || exit;

add_action('rest_api_init', function () {

    register_rest_route('aptaive/v1', '/config', [
        [
            'methods'  => 'GET',
            'callback' => 'aptaive_get_config',
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
    return rest_ensure_response($config);
}

function aptaive_save_config(WP_REST_Request $request)
{
    $data = $request->get_json_params();

    if (!$data) {
        return new WP_Error('invalid_data', 'Invalid config', ['status' => 400]);
    }

    update_option('aptaive_builder_config', $data, false);

    return [
        'success' => true,
    ];
}
