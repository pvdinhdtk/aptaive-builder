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

    // Nếu là email → tìm username
    if (is_email($login)) {
        $u = get_user_by('email', $login);
        if ($u) {
            $login = $u->user_login;
        }
    }

    $user = wp_authenticate($login, $password);

    if (is_wp_error($user)) {
        $code = $user->get_error_code();

        switch ($code) {
            case 'empty_username':
                $message = 'Vui lòng nhập tài khoản';
                break;
            case 'empty_password':
                $message = 'Vui lòng nhập mật khẩu';
                break;
            case 'invalid_username':
                $message = 'Tài khoản không tồn tại';
                break;
            case 'incorrect_password':
                $message = 'Mật khẩu không chính xác';
                break;
            default:
                $message = 'Sai tài khoản hoặc mật khẩu';
        }

        return aptaive_response([], $message, 401);
    }

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
