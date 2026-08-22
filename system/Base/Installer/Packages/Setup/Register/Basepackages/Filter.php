<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Db\Enum;

/**
 * Seeds default data filter rules into basepackages_filters.
 */
class Filter
{
    /**
     * Registers default filter rule to exclude auto-generated filters.
     *
     * @param mixed $db PDO database connection adapter.
     * @param mixed $ff FlatFile database manager.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff): void
    {
        if ($db) {
            $filterComponent = $db->fetchAll(
                'SELECT * FROM modules_components WHERE route LIKE :route',
                Enum::FETCH_ASSOC,
                ['route' => 'system/filters']
            );

            $componentId = $filterComponent[0]['id'] ?? 1;

            $filter = [
                'name'           => 'Exclude Auto Generated Filters',
                'app_type'       => 'core',
                'component_id'   => $componentId,
                'conditions'     => '-|auto_generated|equals|0&',
                'filter_type'    => 0,
                'auto_generated' => 0,
                'is_default'     => 1,
                'account_id'     => 0,
                'archived'       => 0
            ];

            $db->insertAsDict('basepackages_filters', $filter);
        }

        if ($ff) {
            $componentsStore = $ff->store('modules_components');
            $filterStore = $ff->store('basepackages_filters');

            $filterComponent = $componentsStore?->findOneBy(['route', '=', 'system/filters']);
            $componentId = $filterComponent['id'] ?? 1;

            $filter = [
                'name'           => 'Exclude Auto Generated Filters',
                'app_type'       => 'core',
                'component_id'   => $componentId,
                'conditions'     => '-|auto_generated|equals|0&',
                'filter_type'    => 0,
                'auto_generated' => 0,
                'is_default'     => 1,
                'account_id'     => 0,
                'archived'       => 0
            ];

            $filterStore?->updateOrInsert($filter);
        }
    }
}