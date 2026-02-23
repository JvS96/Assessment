<?php

function env($key)
{
    static $env = null;

    if ($env === null) {
        $path = __DIR__ . '/../.env';

        if (!file_exists($path)) {
            throw new Exception('.env file not found');
        }

        $env = parse_ini_file($path);
    }

    return $env[$key] ?? null;
}
