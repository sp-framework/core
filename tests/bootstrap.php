<?php

/**
 * SP Framework Test Suite Bootstrap
 *
 * @package   Tests
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (!function_exists('json_trace')) {
    require_once __DIR__ . '/../system/Base/Helpers.php';
}

spl_autoload_register(function ($class) {
    if (str_starts_with($class, 'System\\')) {
        $file = __DIR__ . '/../system/' . str_replace('\\', '/', substr($class, 7)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    } elseif (str_starts_with($class, 'Apps\\')) {
        $file = __DIR__ . '/../apps/' . str_replace('\\', '/', substr($class, 5)) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
