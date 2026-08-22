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

use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Calls;

/**
 * Seeds package metadata into modules_packages.
 */
class Package
{
    /**
     * Registers package entry and triggers worker calls seeding if Core package.
     *
     * @param mixed                $db           PDO database connection adapter.
     * @param mixed                $ff           FlatFile database manager.
     * @param array<string, mixed> $packageFile  Package metadata array.
     * @param mixed                $helper       Helpers service instance.
     * @param mixed                $basepackages Basepackages manager instance.
     * @param mixed                $container    DI container.
     * @param string               $databasetype Database driver type (db, ff, hybrid).
     *
     * @return void
     */
    public function register(
        mixed $db,
        mixed $ff,
        array $packageFile,
        mixed $helper,
        mixed $basepackages,
        mixed $container,
        string $databasetype
    ): void {
        $name = $packageFile['name'] ?? '';

        $package = [
            'name'         => $name,
            'display_name' => $packageFile['display_name'] ?? $name,
            'description'  => $packageFile['description'] ?? '',
            'module_type'  => $packageFile['module_type'] ?? 'packages',
            'app_type'     => $packageFile['app_type'] ?? 'core',
            'category'     => $packageFile['category'] ?? '',
            'version'      => $packageFile['version'] ?? '1.0.0',
            'repo'         => $packageFile['repo'] ?? '',
            'class'        => $packageFile['class'] ?? '',
            'settings'     => isset($packageFile['settings']) ? $helper->encode($packageFile['settings']) : $helper->encode([]),
            'dependencies' => isset($packageFile['dependencies']) ? $helper->encode($packageFile['dependencies']) : $helper->encode([]),
            'apps'         => $helper->encode(['1' => ['enabled' => true]]),
            'api_id'       => 1,
            'installed'    => 1,
            'updated_by'   => 0
        ];

        $corePackageId = null;

        if ($db) {
            $db->insertAsDict('modules_packages', $package);

            if ($name === 'Core') {
                $corePackageId = (int) $db->lastInsertId();
            }
        }

        if ($ff) {
            $packageStore = $ff->store('modules_packages');

            $packageStore->updateOrInsert($package);

            if ($name === 'Core') {
                $corePackageId = (int) $packageStore->getLastInsertedId();
            }
        }

        if ($corePackageId !== null) {
            $registerCalls = new Calls();

            $registerCalls->register($db, $ff, $basepackages, $container, $corePackageId, $databasetype);
        }
    }
}