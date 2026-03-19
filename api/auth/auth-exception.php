<?php
defined('ABSPATH') || exit;

class Aptaive_Auth_Exception extends Exception
{
    public string $code_name;

    public function __construct(
        string $code_name,
        string $message,
        int $http_code = 401
    ) {
        parent::__construct($message, $http_code);
        $this->code_name = $code_name;
    }
}
