<?php
defined('ABSPATH') || exit;

function aptaive_delete_account(WP_REST_Request $request)
{
    return aptaive_handle(function () use ($request) {
        $user = aptaive_auth_user();
        $params = $request->get_json_params();
        $password = $params['password'] ?? '';

        if (!$password) {
            return aptaive_response([], 'Vui lòng nhập mật khẩu hiện tại', 422);
        }

        $blocked_roles = ['administrator', 'shop_manager'];

        if (array_intersect($blocked_roles, (array) $user->roles)) {
            return aptaive_response([], 'Không thể xóa tài khoản quản trị', 403);
        }

        if (!wp_check_password($password, $user->user_pass, $user->ID)) {
            return aptaive_response([], 'Mật khẩu không chính xác', 401);
        }

        if (!function_exists('wp_delete_user')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        delete_user_meta($user->ID, APTAIVE_REFRESH_META_KEY);

        $deleted = wp_delete_user($user->ID);

        if (!$deleted) {
            return aptaive_response([], 'Xóa tài khoản thất bại', 500);
        }

        return aptaive_response([], 'Xóa tài khoản thành công');
    });
}
