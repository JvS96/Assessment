<?php
namespace Core;
class Response
{
    public static function json($data, int $status = 200)
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json');
        }

        echo json_encode($data);
    }
}