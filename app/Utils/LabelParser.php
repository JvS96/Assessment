<?php
namespace Utils;
class LabelParser
{
    public static function parse(array $labels): array
    {
        $result = [
            'client' => null,
            'priority' => null,
            'type' => null,
        ];

        foreach ($labels as $label) {
            $name = $label['name'] ?? '';

            if (str_starts_with($name, 'C:')) {
                $result['client'] = trim(substr($name, 2));
            }

            if (str_starts_with($name, 'P:')) {
                $result['priority'] = trim(substr($name, 2));
            }

            if (str_starts_with($name, 'T:')) {
                $result['type'] = trim(substr($name, 2));
            }
        }

        return $result;
    }
}