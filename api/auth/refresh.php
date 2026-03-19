<?php
defined('ABSPATH') || exit;

function aptaive_refresh(WP_REST_Request $req)
{
    $refresh = $req->get_json_params()['refreshToken'] ?? '';
    if (!$refresh) return aptaive_response([], 'Invalid token', 401);

    $users = get_users(['meta_key' => APTAIVE_REFRESH_META_KEY]);

    foreach ($users as $user) {
        $hash = get_user_meta($user->ID, APTAIVE_REFRESH_META_KEY, true);
        if ($hash && password_verify($refresh, $hash)) {

            $now = time();
            $token = aptaive_jwt_encode([
                'uid' => $user->ID,
                'iat' => $now,
                'exp' => $now + APTAIVE_ACCESS_TOKEN_TTL,
            ]);

            return aptaive_response([
                'accessToken' => $token,
                'expiresIn'   => APTAIVE_ACCESS_TOKEN_TTL,
            ]);
        }
    }

    return aptaive_response([], 'Refresh token expired', 401);
}
