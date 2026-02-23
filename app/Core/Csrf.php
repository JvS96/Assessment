<?php
namespace Core;

class Csrf
{
    public static function generateToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf'] = $token;

        return $token;
    }

    public static function validate(string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return isset($_SESSION['_csrf']) &&
            hash_equals($_SESSION['_csrf'], $token);
    }
}