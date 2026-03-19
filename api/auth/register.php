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

    // Validate
    if (!$username || !$email || !$password) {
        return aptaive_response([], 'Vui lòng nhập thông tin chính xác các trường', 422);
    }

    if (username_exists($username)) {
        return aptaive_response([], 'Username đã tồn tại', 409);
    }

    if (email_exists($email)) {
        return aptaive_response([], 'Email đã được sử dụng', 409);
    }

    // Create user (role theo Admin)
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        return aptaive_response([], $user_id->get_error_message(), 400);
    }

    $user = get_user_by('id', $user_id);

    // Auto login
    $now = time();

    $accessToken = aptaive_jwt_encode([
        'uid' => $user->ID,
        'iat' => $now,
        'exp' => $now + APTAIVE_ACCESS_TOKEN_TTL,
    ]);

    $refreshToken = wp_generate_password(64, false);
    update_user_meta(
        $user->ID,
        APTAIVE_REFRESH_META_KEY,
        password_hash($refreshToken, PASSWORD_DEFAULT)
    );

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
