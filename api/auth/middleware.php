<?php

function aptaive_auth_user(): WP_User
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (!$header || stripos($header, 'Bearer ') !== 0) {
        throw new Aptaive_Auth_Exception(
            'NO_TOKEN',
            'Authorization token missing'
        );
    }

    $token = trim(substr($header, 7));

    if (!$token) {
        throw new Aptaive_Auth_Exception(
            'NO_TOKEN',
            'Authorization token missing'
        );
    }

    $result = aptaive_jwt_decode($token);

    if (isset($result['error'])) {
        if ($result['error'] === 'TOKEN_EXPIRED') {
            throw new Aptaive_Auth_Exception(
                'TOKEN_EXPIRED',
                'Access token expired'
            );
        }

        throw new Aptaive_Auth_Exception(
            'INVALID_TOKEN',
            'Invalid access token'
        );
    }

    $payload = $result['payload'];

    $user = get_user_by('id', $payload['uid'] ?? 0);

    if (!$user) {
        throw new Aptaive_Auth_Exception(
            'USER_NOT_FOUND',
            'User not found'
        );
    }

    return $user;
}
