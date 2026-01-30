<?php
defined('ABSPATH') || exit;

/**
 * Chạy khi plugin activate / update
 */
function aptaive_on_activate()
{
    $config = get_option(APTAIVE_CONFIG_OPTION);

    // Chưa có config → tạo mới theo schema hiện tại
    if (!$config || !is_array($config)) {
        update_option(APTAIVE_CONFIG_OPTION, aptaive_default_config());
        return;
    }

    $currentVersion = $config['schemaVersion'] ?? 1;

    if ($currentVersion < APTAIVE_SCHEMA_VERSION) {
        $config = aptaive_migrate_config($config, $currentVersion);
        update_option(APTAIVE_CONFIG_OPTION, $config);
    }
}

function aptaive_migrate_config(array $config, int $fromVersion): array
{
    $version = $fromVersion;

    while ($version < APTAIVE_SCHEMA_VERSION) {
        switch ($version) {
            case 1:
                $config = aptaive_migrate_v1_to_v2($config);
                $version = 2;
                break;
        }
    }

    return $config;
}

function aptaive_migrate_v1_to_v2(array $old): array
{
    $config = $old;

    //update schema
    $config['schemaVersion'] = 2;

    //đảm bảo app tồn tại
    $config['app'] = $config['app'] ?? [];

    //thêm field mới NẾU CHƯA CÓ
    $config['app']['versionUpdate'] = $config['app']['versionUpdate'] ?? '';

    $config['app']['download'] = array_merge(
        [
            'android' => '',
            'ios'     => '',
        ],
        $config['app']['download'] ?? []
    );

    //layouts giữ nguyên
    $config['layouts'] = $config['layouts'] ?? [];

    return $config;
}

function aptaive_default_config(): array
{
    return [
        'schemaVersion' => APTAIVE_SCHEMA_VERSION,

        'app' => [
            'appName' => '',
            'applicationId' => '',
            'version' => '1.0.0',
            'versionUpdate' => '',
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
                        'icon' => 'post',
                        'targetType' => 'post',
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
