<?php
defined('ABSPATH') || exit;

function aptaive_delete_account(WP_REST_Request $request)
{
    return aptaive_handle(function () use ($request) {
        $user = aptaive_auth_user();
        $params = $request->get_json_params();
        $password = $params['password'] ?? '';
        $rate_limit = aptaive_auth_rate_limit_check('delete_account', (string) $user->ID);

        if ($rate_limit instanceof WP_Error) {
            return $rate_limit;
        }

        if (!$password) {
            return aptaive_response([], 'Vui lòng nhập mật khẩu hiện tại', 422);
        }

        $blocked_roles = ['administrator', 'shop_manager'];

        if (array_intersect($blocked_roles, (array) $user->roles)) {
            return aptaive_response([], 'Không thể xóa tài khoản quản trị', 403);
        }

        if (!wp_check_password($password, $user->user_pass, $user->ID)) {
            aptaive_auth_rate_limit_hit('delete_account', (string) $user->ID);
            return aptaive_response([], 'Mật khẩu không chính xác', 401);
        }

        aptaive_auth_rate_limit_clear('delete_account', (string) $user->ID);
        delete_user_meta($user->ID, APTAIVE_REFRESH_META_KEY);

        if (!function_exists('wp_delete_user')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        $deleted = wp_delete_user($user->ID);

        if (!$deleted) {
            return aptaive_response([], 'Xóa tài khoản thất bại', 500);
        }

        return aptaive_response([], 'Xóa tài khoản thành công');
    });
}
