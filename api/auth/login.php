<?php
defined('ABSPATH') || exit;

function aptaive_login(WP_REST_Request $req)
{
    $p = $req->get_json_params();

    // identifier = username OR email
    $login = trim(
        $p['identifier'] ?? ''
    );
    $password = $p['password'] ?? '';

    $rate_limit = aptaive_auth_rate_limit_check('login', $login);
    if ($rate_limit instanceof WP_Error) {
        return $rate_limit;
    }

    // Nếu là email → tìm username
    if (is_email($login)) {
        $u = get_user_by('email', $login);
        if ($u) {
            $login = $u->user_login;
        }
    }

    $user = wp_authenticate($login, $password);

    if (is_wp_error($user)) {
        if (in_array($user->get_error_code(), ['empty_username', 'empty_password'], true)) {
            return aptaive_response([], 'Vui lòng nhập tài khoản và mật khẩu', 422);
        }

        aptaive_auth_rate_limit_hit('login', $login);

        return aptaive_response([], 'Sai tài khoản hoặc mật khẩu', 401);
    }

    aptaive_auth_rate_limit_clear('login', $login);

    $now = time();

    $accessToken = aptaive_jwt_encode([
        'uid' => $user->ID,
        'iat' => $now,
        'exp' => $now + APTAIVE_ACCESS_TOKEN_TTL,
    ]);

    $refreshToken = aptaive_auth_issue_refresh_token($user->ID);

    return aptaive_response(
        [
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken,
            'expiresIn'    => APTAIVE_ACCESS_TOKEN_TTL,
            'user' => [
                'id'          => $user->ID,
                'displayName' => $user->display_name,
                'email'       => $user->user_email,
                'avatar'      => get_avatar_url($user->ID),
            ],
        ],
        'Đăng nhập thành công'
    );
}
