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
 * Seeds view themes and default view settings into modules_views and modules_views_settings.
 */
class View
{
    /**
     * Registers view theme entry and view theme settings.
     *
     * @param mixed                $db       PDO database connection adapter.
     * @param mixed                $ff       FlatFile database manager.
     * @param array<string, mixed> $viewFile View metadata array.
     * @param mixed                $helper   Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $viewFile, mixed $helper): void
    {
        $name = $viewFile['name'] ?? 'Default';

        $view = [
            'name'                 => $name,
            'display_name'         => $viewFile['display_name'] ?? $name,
            'description'          => $viewFile['description'] ?? '',
            'module_type'          => $viewFile['module_type'] ?? 'views',
            'app_type'             => $viewFile['app_type'] ?? 'core',
            'category'             => $viewFile['category'] ?? '',
            'version'              => $viewFile['version'] ?? '1.0.0',
            'view_modules_version' => '0.0.0',
            'base_view_module_id'  => 0,
            'is_subview'           => 0,
            'repo'                 => $viewFile['repo'] ?? '',
            'settings'             => isset($viewFile['settings']) ? $helper->encode($viewFile['settings']) : $helper->encode([]),
            'dependencies'         => isset($viewFile['dependencies']) ? $helper->encode($viewFile['dependencies']) : $helper->encode([]),
            'apps'                 => $helper->encode(['1' => ['enabled' => true]]),
            'api_id'               => 1,
            'installed'            => 1,
            'updated_by'           => 0
        ];

        $viewSettings = [
            'view_id'   => 1,
            'domain_id' => 1,
            'app_id'    => 1,
            'settings'  => isset($viewFile['settings']) ? $helper->encode($viewFile['settings']) : $helper->encode([])
        ];

        if ($db) {
            $db->insertAsDict('modules_views', $view);

            $db->insertAsDict('modules_views_settings', $viewSettings);
        }

        if ($ff) {
            $viewsStore = $ff->store('modules_views');

            $viewsSettingsStore = $ff->store('modules_views_settings');

            $viewsStore->updateOrInsert($view);

            $viewsSettingsStore->updateOrInsert($viewSettings);
        }
    }
}