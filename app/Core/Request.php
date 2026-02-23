<?php
namespace Core;
class Request
{
    private array $data;

    public function __construct()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $this->data = json_decode($raw, true) ?? [];
        } else {
            $this->data = $_POST;
        }
    }

    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }
}