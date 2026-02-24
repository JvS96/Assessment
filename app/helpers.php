<?php

function env($key, $default = null)
{
    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    static $env = null;

    if ($env === null) {
        $path = __DIR__ . '/../.env';

        if (file_exists($path)) {
            $env = parse_ini_file($path);
        } else {
            $env = [];
        }
    }

    return $env[$key] ?? $default;
}