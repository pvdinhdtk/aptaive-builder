<?php
defined('ABSPATH') || exit;

function aptaive_jwt_decode(string $jwt)
{
    if (!defined('APTAIVE_JWT_SECRET')) {
        return ['error' => 'JWT_SECRET_MISSING'];
    }

    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return ['error' => 'TOKEN_MALFORMED'];
    }

    [$header, $payload, $signature] = $parts;

    $valid = aptaive_base64url(
        hash_hmac(
            'sha256',
            "$header.$payload",
            APTAIVE_JWT_SECRET,
            true
        )
    );

    if (!hash_equals($valid, $signature)) {
        return ['error' => 'TOKEN_INVALID'];
    }

    $data = json_decode(
        base64_decode(strtr($payload, '-_', '+/')),
        true
    );

    if (!$data) {
        return ['error' => 'TOKEN_INVALID'];
    }

    if (($data['exp'] ?? 0) < time()) {
        return ['error' => 'TOKEN_EXPIRED'];
    }

    return [
        'payload' => $data,
    ];
}
