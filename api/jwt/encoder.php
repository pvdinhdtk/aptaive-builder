<?php

function aptaive_jwt_encode(array $payload): string
{
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];

    $segments = [];
    $segments[] = aptaive_base64url(json_encode($header));
    $segments[] = aptaive_base64url(json_encode($payload));

    $signing_input = implode('.', $segments);
    $signature = hash_hmac('sha256', $signing_input, APTAIVE_JWT_SECRET, true);
    $segments[] = aptaive_base64url($signature);

    return implode('.', $segments);
}

function aptaive_base64url($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
