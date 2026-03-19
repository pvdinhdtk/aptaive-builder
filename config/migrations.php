<?php
defined('ABSPATH') || exit;

/**
 * Chạy khi plugin activate / update
 */
function aptaive_on_activate()
{
    $config = get_option(APTAIVE_CONFIG_OPTION);

    if (!$config || !is_array($config)) {
        update_option(APTAIVE_CONFIG_OPTION, aptaive_default_config());
        return;
    }

    // Hien tai chua co migrate vi chua dua len production.
    // Neu sau nay tang schemaVersion, co the khoi phuc lai flow migrate theo mau cu:
    //
    // $current_version = (int) ($config['schemaVersion'] ?? 1);
    // if ($current_version < APTAIVE_SCHEMA_VERSION) {
    //     $config = aptaive_migrate_config($config, $current_version);
    // }
    //
    // function aptaive_migrate_config(array $config, int $from_version): array
    // {
    //     $version = $from_version;
    //
    //     while ($version < APTAIVE_SCHEMA_VERSION) {
    //         switch ($version) {
    //             case 1:
    //                 $config = aptaive_migrate_v1_to_v2($config);
    //                 $version = 2;
    //                 break;
    //             case 2:
    //                 $config = aptaive_migrate_v2_to_v3($config);
    //                 $version = 3;
    //                 break;
    //             default:
    //                 $version = APTAIVE_SCHEMA_VERSION;
    //                 break;
    //         }
    //     }
    //
    //     return $config;
    // }
    //
    // function aptaive_migrate_v1_to_v2(array $config): array
    // {
    //     $config['schemaVersion'] = 2;
    //     return $config;
    // }
    //
    // function aptaive_migrate_v2_to_v3(array $config): array
    // {
    //     $config['schemaVersion'] = 3;
    //     return $config;
    // }
    update_option(
        APTAIVE_CONFIG_OPTION,
        aptaive_normalize_config($config)
    );
}

function aptaive_normalize_config(array $config): array
{
    $default = aptaive_default_config();

    $config['pluginVersion'] = APTAIVE_PLUGIN_VERSION;
    $config['schemaVersion'] = APTAIVE_SCHEMA_VERSION;
    $config['minAppVersion'] = aptaive_normalize_version_string(
        $config['minAppVersion'] ?? APTAIVE_MIN_APP_VERSION,
        APTAIVE_MIN_APP_VERSION
    );
    $config['app'] = array_merge(
        $default['app'],
        is_array($config['app'] ?? null) ? $config['app'] : []
    );

    $config['app']['download'] = array_merge(
        $default['app']['download'],
        is_array($config['app']['download'] ?? null) ? $config['app']['download'] : []
    );

    $config['layouts'] = array_merge(
        $default['layouts'],
        is_array($config['layouts'] ?? null) ? $config['layouts'] : []
    );

    unset($config['app']['version'], $config['app']['versionUpdate'], $config['app']['minAppVersion']);

    return $config;
}

function aptaive_normalize_version_string($value, string $fallback): string
{
    if (!is_string($value)) {
        return $fallback;
    }

    $value = trim($value);

    if ($value === '') {
        return $fallback;
    }

    if (!preg_match('/^\d+\.\d+\.\d+$/', $value)) {
        return $fallback;
    }

    return $value;
}

function aptaive_default_config(): array
{
    return [
        'pluginVersion' => APTAIVE_PLUGIN_VERSION,
        'schemaVersion' => APTAIVE_SCHEMA_VERSION,
        'minAppVersion' => APTAIVE_MIN_APP_VERSION,
        'app' => [
            'appName' => '',
            'applicationId' => '',
            'download' => [
                'ios' => '',
                'android' => '',
            ],

            'logo' => '',
            'icon' => '',

            'primaryColor' => '#FF5722',
            'secondaryColor' => '#FFFFFF',
            'textPrimaryColor' => '#000000',
            'textSecondaryColor' => '#666666',
        ],

        'layouts' => [

            'home' => [
                [
                    'type' => 'slider',
                    'title' => null,
                    'items' => [
                        [
                            'image' => '',
                            'targetType' => 'home',
                            'targetId' => null,
                        ],
                    ],
                ],

                [
                    'type' => 'categoryGrid',
                    'title' => 'Danh mục',
                    'mode' => 'productCategory',
                    'rows' => 2,
                    'items' => [],
                ],

                [
                    'type' => 'productList',
                    'title' => 'Sản phẩm nổi bật',
                    'columns' => 2,
                    'limit' => null,
                    'categoryIds' => [],
                ],
            ],

            'bottomNavigation' => [
                'items' => [
                    [
                        'label' => 'Trang chủ',
                        'icon' => 'house',
                        'targetType' => 'home',
                        'targetId' => null,
                    ],
                    [
                        'label' => 'Cửa hàng',
                        'icon' => 'bag',
                        'targetType' => 'product',
                        'targetId' => null,
                    ],
                    [
                        'label' => 'Bài viết',
                        'icon' => 'text',
                        'targetType' => 'doc_text',
                        'targetId' => null,
                    ],
                    [
                        'label' => 'Tài khoản',
                        'icon' => 'person',
                        'targetType' => 'account',
                        'targetId' => null,
                    ],
                ],
            ],
        ],
    ];
}
