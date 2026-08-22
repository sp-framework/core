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

use Exception;
use Phalcon\Db\Enum;

/**
 * Seeds and updates application components metadata in modules_components.
 */
class Component
{
    /**
     * Registers component definition into database and FlatFile stores.
     *
     * @param mixed                $db            PDO database connection adapter.
     * @param mixed                $ff            FlatFile database manager.
     * @param array<string, mixed> $componentFile Component metadata array.
     * @param int|null             $menuId        Associated Menu ID.
     * @param mixed                $helper        Helpers service instance.
     *
     * @throws Exception If generated IDs between DB and FF mismatch.
     *
     * @return int|null Component ID.
     */
    public function register(mixed $db, mixed $ff, array $componentFile, ?int $menuId, mixed $helper): ?int
    {
        $componentApp = ['1' => ['enabled' => true]];

        if (isset($componentFile['settings']['needAuth']) && $componentFile['settings']['needAuth'] !== true) {
            $componentApp['1']['needAuth'] = $componentFile['settings']['needAuth'];
        } else {
            $componentApp['1']['needAuth'] = true;
        }

        $component = [
            'name'         => $componentFile['name'] ?? '',
            'route'        => $componentFile['route'] ?? '',
            'description'  => $componentFile['description'] ?? '',
            'module_type'  => $componentFile['module_type'] ?? 'components',
            'app_type'     => $componentFile['app_type'] ?? 'core',
            'category'     => $componentFile['category'] ?? '',
            'version'      => $componentFile['version'] ?? '1.0.0',
            'class'        => $componentFile['class'] ?? '',
            'repo'         => $componentFile['repo'] ?? '',
            'dependencies' => isset($componentFile['dependencies']) ? $helper->encode($componentFile['dependencies']) : $helper->encode([]),
            'menu'         => isset($componentFile['menu']) ? $helper->encode($componentFile['menu']) : false,
            'menu_id'      => $menuId,
            'api_id'       => 1,
            'installed'    => 1,
            'apps'         => $helper->encode($componentApp),
            'settings'     => isset($componentFile['settings']) ? $helper->encode($componentFile['settings']) : $helper->encode([]),
            'widgets'      => isset($componentFile['widgets']) ? $helper->encode($componentFile['widgets']) : $helper->encode([]),
            'updated_by'   => 0
        ];

        $dbComponentId = null;
        $ffComponentId = null;

        if ($db) {
            $db->insertAsDict('modules_components', $component);
            $dbComponentId = (int) $db->lastInsertId();
        }

        if ($ff) {
            $componentStore = $ff->store('modules_components');

            $componentStore->updateOrInsert($component);

            $ffComponentId = (int) $componentStore->getLastInsertedId();
        }

        if ($dbComponentId !== null && $ffComponentId !== null) {
            if ($dbComponentId === $ffComponentId) {
                return $dbComponentId;
            }

            throw new Exception('Component ids dont match for db and ff');
        } elseif ($dbComponentId !== null) {
            return $dbComponentId;
        } elseif ($ffComponentId !== null) {
            return $ffComponentId;
        }

        return null;
    }

    /**
     * Updates menu binding for a registered component.
     *
     * @param mixed                $db            PDO database connection adapter.
     * @param mixed                $ff            FlatFile database manager.
     * @param array<string, mixed> $componentFile Component metadata.
     * @param int|null             $menuId        Menu ID.
     *
     * @return void
     */
    public function update(mixed $db, mixed $ff, array $componentFile, ?int $menuId): void
    {
        $class = $componentFile['class'] ?? '';

        if ($db) {
            $component = $db->fetchAll(
                'SELECT * FROM modules_components WHERE class = :class',
                Enum::FETCH_ASSOC,
                ['class' => $class]
            );

            if (isset($component[0]['id'])) {
                $db->updateAsDict(
                    'modules_components',
                    ['menu_id' => $menuId],
                    'id = ' . (int) $component[0]['id']
                );
            }
        }

        if ($ff) {
            $componentStore = $ff->store('modules_components');
            $component = $componentStore?->findOneBy(['class', '=', $class]);

            if ($component && is_array($component)) {
                $component['menu_id'] = $menuId;
                $componentStore->updateOrInsert($component);
            }
        }
    }
}