<?php
defined('ABSPATH') || exit;

function aptaive_refresh(WP_REST_Request $req)
{
    $refresh = $req->get_json_params()['refreshToken'] ?? '';
    if (!$refresh) return aptaive_response([], 'Invalid token', 401);

    $rate_limit = aptaive_auth_rate_limit_check('refresh', $refresh);
    if ($rate_limit instanceof WP_Error) {
        return $rate_limit;
    }

    $parts = explode('.', $refresh, 2);
    $user_id = isset($parts[0]) ? absint($parts[0]) : 0;

    if ($user_id <= 0) {
        aptaive_auth_rate_limit_hit('refresh', $refresh);

        return aptaive_response([], 'Refresh token expired', 401);
    }

    $record = aptaive_auth_get_refresh_token_record($user_id);
    $hash = $record['hash'];
    $expires_at = $record['expires_at'];

    if ($expires_at > 0 && $expires_at < time()) {
        delete_user_meta($user_id, APTAIVE_REFRESH_META_KEY);
        aptaive_auth_rate_limit_hit('refresh', $refresh);

        return aptaive_response([], 'Refresh token expired', 401);
    }

    if ($hash && password_verify($refresh, $hash)) {
        aptaive_auth_rate_limit_clear('refresh', $refresh);

        $now = time();
        $token = aptaive_jwt_encode([
            'uid' => $user_id,
            'iat' => $now,
            'exp' => $now + APTAIVE_ACCESS_TOKEN_TTL,
        ]);

        $new_refresh_token = aptaive_auth_issue_refresh_token($user_id);

        return aptaive_response([
            'accessToken' => $token,
            'expiresIn'   => APTAIVE_ACCESS_TOKEN_TTL,
            'refreshToken' => $new_refresh_token,
        ]);
    }

    aptaive_auth_rate_limit_hit('refresh', $refresh);

    return aptaive_response([], 'Refresh token expired', 401);
}
