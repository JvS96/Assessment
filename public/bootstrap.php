<?php

require_once __DIR__ . '/../app/helpers.php';

spl_autoload_register(function ($class) {

    $baseDir = realpath(__DIR__ . '/../app') . '/';

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});