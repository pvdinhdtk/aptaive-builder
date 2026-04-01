<?php
defined('ABSPATH') || exit;

function aptaive_register(WP_REST_Request $req)
{
    // Rule: Anyone can register (WP Admin)
    if (!get_option('users_can_register')) {
        return aptaive_response([], 'Chức năng đăng ký đang bị tắt', 403);
    }

    $p = $req->get_json_params();

    $username = sanitize_user($p['username'] ?? '');
    $email    = sanitize_email($p['email'] ?? '');
    $password = $p['password'] ?? '';

    $rate_limit = aptaive_auth_rate_limit_check('register', $email ?: $username);
    if ($rate_limit instanceof WP_Error) {
        return $rate_limit;
    }

    // Validate
    if (!$username || !$email || !$password) {
        return aptaive_response([], 'Vui lòng nhập thông tin chính xác các trường', 422);
    }

    if (!is_email($email)) {
        aptaive_auth_rate_limit_hit('register', $email ?: $username);

        return aptaive_response([], 'Email không hợp lệ', 422);
    }

    if (strlen($password) < 8) {
        aptaive_auth_rate_limit_hit('register', $email ?: $username);

        return aptaive_response([], 'Mật khẩu phải có ít nhất 8 ký tự', 422);
    }

    if (username_exists($username)) {
        aptaive_auth_rate_limit_hit('register', $email ?: $username);
        return aptaive_response([], 'Username đã tồn tại', 409);
    }

    if (email_exists($email)) {
        aptaive_auth_rate_limit_hit('register', $email ?: $username);
        return aptaive_response([], 'Email đã được sử dụng', 409);
    }

    // Create user (role theo Admin)
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        aptaive_auth_rate_limit_hit('register', $email ?: $username);
        return aptaive_response([], $user_id->get_error_message(), 400);
    }

    aptaive_auth_rate_limit_clear('register', $email ?: $username);

    $user = get_user_by('id', $user_id);

    // Auto login
    $now = time();

    $accessToken = aptaive_jwt_encode([
        'uid' => $user->ID,
        'iat' => $now,
        'exp' => $now + APTAIVE_ACCESS_TOKEN_TTL,
    ]);

    $refreshToken = aptaive_auth_issue_refresh_token($user->ID);

    // ✅ Response giống LOGIN
    return aptaive_response(
        [
            'accessToken'  => $accessToken,
            'refreshToken' => $refreshToken,
            'expiresIn'    => APTAIVE_ACCESS_TOKEN_TTL,
            'user' => [
                'id'           => $user->ID,
                'displayName'  => $user->user_login, // 👈 displayName = username
                'email'        => $user->user_email,
                'avatar'       => get_avatar_url($user->ID),
            ],
        ],
        'Đăng ký thành công'
    );
}
