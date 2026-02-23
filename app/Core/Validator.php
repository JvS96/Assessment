<?php

namespace Core;

class Validator
{
    public static function validateIssue(array $data): array
    {
        $errors = [];

        // Title
        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Title is required.';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Title must not exceed 255 characters.';
        }

        // Body
        if (isset($data['body']) && strlen($data['body']) > 5000) {
            $errors['body'] = 'Body is too long.';
        }

        // Client
        if (empty(trim($data['client'] ?? ''))) {
            $errors['client'] = 'Client is required.';
        }

        // Priority
        $allowedPriorities = ['Low', 'Medium', 'High'];
        if (!in_array($data['priority'] ?? '', $allowedPriorities)) {
            $errors['priority'] = 'Invalid priority selected.';
        }

        // Type
        $allowedTypes = ['Bug', 'Feature', 'Task'];
        if (!in_array($data['type'] ?? '', $allowedTypes)) {
            $errors['type'] = 'Invalid issue type.';
        }

        return $errors;
    }
}