<?php
defined('ABSPATH') || exit;

function aptaive_response($data = [], $message = '', $status = 200)
{
    return new WP_REST_Response([
        'success' => $status >= 200 && $status < 300,
        'message' => $message,
        'data'    => $data,
        'code'    => $status,
    ], $status);
}
