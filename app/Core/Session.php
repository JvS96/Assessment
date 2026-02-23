<?php
namespace Core;
class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',        // IMPORTANT
                'domain' => '',
                'secure' => true,     // since using ngrok HTTPS
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    public static function regenerate()
    {
        session_regenerate_id(true);
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy()
    {
        session_destroy();
    }
}