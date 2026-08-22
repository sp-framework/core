<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Modules
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Modules;

/**
 * Seeds framework middleware definitions into modules_middlewares.
 */
class Middleware
{
    /**
     * Registers middleware entry into database and FlatFile stores.
     *
     * @param mixed                $db             PDO database connection adapter.
     * @param mixed                $ff             FlatFile database manager.
     * @param array<string, mixed> $middlewareFile Middleware metadata array.
     * @param mixed                $helper         Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $middlewareFile, mixed $helper): void
    {
        $name = $middlewareFile['name'] ?? '';

        if ($name === 'Auth') {
            $apps = $helper->encode(['1' => ['enabled' => true, 'sequence' => 1]]);
        } elseif ($name === 'Acl') {
            $apps = $helper->encode(['1' => ['enabled' => true, 'sequence' => 2]]);
        } else {
            $apps = $helper->encode(['1' => ['enabled' => false, 'sequence' => 0]]);
        }

        $middleware = [
            'name'         => $name,
            'display_name' => $middlewareFile['display_name'] ?? $name,
            'description'  => $middlewareFile['description'] ?? '',
            'module_type'  => $middlewareFile['module_type'] ?? 'middlewares',
            'app_type'     => $middlewareFile['app_type'] ?? 'core',
            'category'     => $middlewareFile['category'] ?? '',
            'version'      => $middlewareFile['version'] ?? '1.0.0',
            'repo'         => $middlewareFile['repo'] ?? '',
            'class'        => $middlewareFile['class'] ?? '',
            'settings'     => isset($middlewareFile['settings']) ? $helper->encode($middlewareFile['settings']) : $helper->encode([]),
            'dependencies' => isset($middlewareFile['dependencies']) ? $helper->encode($middlewareFile['dependencies']) : $helper->encode([]),
            'apps'         => $apps,
            'api_id'       => 1,
            'installed'    => 1,
            'updated_by'   => 0
        ];

        if ($db) {
            $db->insertAsDict('modules_middlewares', $middleware);
        }

        if ($ff) {
            $middlewareStore = $ff->store('modules_middlewares');

            $middlewareStore->updateOrInsert($middleware);
        }
    }
}