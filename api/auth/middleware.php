<?php
defined('ABSPATH') || exit;

function aptaive_auth_user(): WP_User
{
    $header = isset($_SERVER['HTTP_AUTHORIZATION'])
        ? sanitize_text_field(wp_unslash($_SERVER['HTTP_AUTHORIZATION']))
        : '';

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

function aptaive_rest_auth_error(Aptaive_Auth_Exception $e): WP_Error
{
    return new WP_Error(
        'aptaive_' . strtolower($e->code_name),
        $e->getMessage(),
        ['status' => $e->getCode()]
    );
}

function aptaive_rest_require_auth(WP_REST_Request $request)
{
    unset($request);

    try {
        aptaive_auth_user();

        return true;
    } catch (Aptaive_Auth_Exception $e) {
        return aptaive_rest_auth_error($e);
    }
}

function aptaive_rest_can_register(WP_REST_Request $request)
{
    unset($request);

    if (get_option('users_can_register')) {
        return true;
    }

    return new WP_Error(
        'aptaive_registration_disabled',
        'User registration is disabled',
        ['status' => 403]
    );
}

function aptaive_rest_allow_guest_checkout_or_auth(WP_REST_Request $request)
{
    if (get_option('woocommerce_enable_guest_checkout') === 'yes') {
        return true;
    }

    return aptaive_rest_require_auth($request);
}

function aptaive_rest_can_view_order(WP_REST_Request $request)
{
    if (!function_exists('wc_get_order')) {
        return new WP_Error(
            'aptaive_order_unavailable',
            'WooCommerce not available',
            ['status' => 400]
        );
    }

    $order_id = absint($request->get_param('order_id'));

    if (!$order_id) {
        return new WP_Error(
            'aptaive_invalid_order_id',
            'Invalid order id',
            ['status' => 400]
        );
    }

    $order = wc_get_order($order_id);

    if (!$order) {
        return new WP_Error(
            'aptaive_order_not_found',
            'Order not found',
            ['status' => 404]
        );
    }

    $order_user_id = (int) $order->get_user_id();

    if ($order_user_id === 0) {
        $order_key = sanitize_text_field($request->get_param('key'));

        if (!$order_key || $order_key !== $order->get_order_key()) {
            return new WP_Error(
                'aptaive_invalid_order_key',
                'Invalid order key',
                ['status' => 401]
            );
        }

        return true;
    }

    try {
        $user = aptaive_auth_user();
    } catch (Aptaive_Auth_Exception $e) {
        return aptaive_rest_auth_error($e);
    }

    if ((int) $user->ID !== $order_user_id) {
        return new WP_Error(
            'aptaive_forbidden_order',
            'Unauthorized',
            ['status' => 401]
        );
    }

    return true;
}

function aptaive_auth_client_ip(): string
{
    $ip = '';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded_header = sanitize_text_field(
            wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])
        );
        $forwarded = explode(',', $forwarded_header);
        $ip = trim((string) $forwarded[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = sanitize_text_field(
            wp_unslash($_SERVER['REMOTE_ADDR'])
        );
    }

    $sanitized_ip = sanitize_text_field($ip);

    return $sanitized_ip ?: 'unknown';
}

function aptaive_auth_identifier_fragment(string $value): string
{
    $normalized = strtolower(trim($value));

    if ($normalized === '') {
        return 'anonymous';
    }

    return substr(hash('sha256', $normalized), 0, 24);
}

function aptaive_auth_rate_limit_key(string $action, string $identifier = ''): string
{
    return 'aptaive_rl_' . md5(
        $action . '|' . aptaive_auth_client_ip() . '|' . aptaive_auth_identifier_fragment($identifier)
    );
}

function aptaive_auth_rate_limit_check(string $action, string $identifier = ''): ?WP_Error
{
    $key = aptaive_auth_rate_limit_key($action, $identifier);
    $state = get_transient($key);

    if (!is_array($state)) {
        return null;
    }

    $attempts = (int) ($state['attempts'] ?? 0);
    $expires_at = (int) ($state['expires_at'] ?? 0);

    if ($expires_at <= time()) {
        delete_transient($key);

        return null;
    }

    if ($attempts < APTAIVE_AUTH_RATE_LIMIT_MAX_ATTEMPTS) {
        return null;
    }

    return new WP_Error(
        'aptaive_rate_limited',
        'Too many attempts. Please try again later.',
        ['status' => 429]
    );
}

function aptaive_auth_rate_limit_hit(string $action, string $identifier = ''): void
{
    $key = aptaive_auth_rate_limit_key($action, $identifier);
    $state = get_transient($key);
    $attempts = is_array($state) ? (int) ($state['attempts'] ?? 0) : 0;

    set_transient($key, [
        'attempts' => $attempts + 1,
        'expires_at' => time() + APTAIVE_AUTH_RATE_LIMIT_WINDOW,
    ], APTAIVE_AUTH_RATE_LIMIT_WINDOW);
}

function aptaive_auth_rate_limit_clear(string $action, string $identifier = ''): void
{
    delete_transient(aptaive_auth_rate_limit_key($action, $identifier));
}

function aptaive_auth_issue_refresh_token(int $user_id): string
{
    $refresh_secret = wp_generate_password(64, false);
    $refresh_token = $user_id . '.' . $refresh_secret;

    update_user_meta($user_id, APTAIVE_REFRESH_META_KEY, [
        'hash' => password_hash($refresh_token, PASSWORD_DEFAULT),
        'expires_at' => time() + APTAIVE_REFRESH_TOKEN_TTL,
    ]);

    return $refresh_token;
}

function aptaive_auth_get_refresh_token_record(int $user_id): array
{
    $stored = get_user_meta($user_id, APTAIVE_REFRESH_META_KEY, true);

    if (is_array($stored)) {
        return [
            'hash' => (string) ($stored['hash'] ?? ''),
            'expires_at' => (int) ($stored['expires_at'] ?? 0),
        ];
    }

    if (is_string($stored) && $stored !== '') {
        return [
            'hash' => $stored,
            'expires_at' => 0,
        ];
    }

    return [
        'hash' => '',
        'expires_at' => 0,
    ];
}
