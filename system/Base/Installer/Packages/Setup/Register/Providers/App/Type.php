<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Providers\App
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Providers\App;

/**
 * Seeds default Core Application Type provider metadata.
 */
class Type
{
    /**
     * Registers default application type into database and FlatFile stores.
     *
     * @param mixed                $db       PDO database connection adapter.
     * @param mixed                $ff       FlatFile database manager.
     * @param array<string, mixed> $typeFile Decoded type.json metadata array.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $typeFile): void
    {
        $type = [
            'name'        => $typeFile['name'] ?? 'Core',
            'app_type'    => $typeFile['app_type'] ?? 'core',
            'description' => $typeFile['description'] ?? '',
            'api_id'      => 1,
            'repo'        => $typeFile['repo'] ?? '',
            'version'     => $typeFile['version'] ?? '1.0.0',
            'updated_by'  => 0,
            'installed'   => 1
        ];

        if ($db) {
            $db->insertAsDict('service_provider_apps_types', $type);
        }

        if ($ff) {
            $appTypeStore = $ff->store('service_provider_apps_types');

            $appTypeStore->updateOrInsert($type);
        }
    }
}