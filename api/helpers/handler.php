<?php
defined('ABSPATH') || exit;

function aptaive_handle(callable $callback)
{
    try {
        return $callback();
    } catch (Aptaive_Auth_Exception $e) {
        return aptaive_response(
            ['code' => $e->code_name],
            $e->getMessage(),
            $e->getCode()
        );
    } catch (Exception $e) {
        return aptaive_response(
            [],
            $e->getMessage(),
            500
        );
    }
}
