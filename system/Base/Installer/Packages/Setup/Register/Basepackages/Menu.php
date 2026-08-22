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

use Exception;

/**
 * Seeds navigation menu entries and sequencing into basepackages_menus.
 */
class Menu
{
    /**
     * Registers navigation menu item.
     *
     * @param mixed                $db                    PDO database connection adapter.
     * @param mixed                $ff                    FlatFile database manager.
     * @param array<string, mixed> $componentJsonFile     Component metadata array.
     * @param mixed                $helper                Helpers service instance.
     * @param mixed                $registeredComponentId Registered component ID.
     *
     * @throws Exception If generated menu IDs between DB and FF mismatch.
     *
     * @return int|null Registered Menu ID.
     */
    public function register(mixed $db, mixed $ff, array $componentJsonFile, mixed $helper, mixed $registeredComponentId): ?int
    {
        $menu = $componentJsonFile['menu'] ?? [];

        if (isset($menu['seq'])) {
            $sequence = (int) $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $menu = $this->addSequence($menu, $sequence);

        $menuToRegister = [
            'menu'         => $helper->encode($menu),
            'apps'         => $helper->encode(['1' => ['enabled' => true]]),
            'app_type'     => $componentJsonFile['app_type'] ?? 'core',
            'component_id' => $registeredComponentId,
            'route'        => $componentJsonFile['route'] ?? '',
            'sequence'     => $sequence
        ];

        $dbMenuId = null;
        $ffMenuId = null;

        if ($db) {
            $db->insertAsDict('basepackages_menus', $menuToRegister);
            $dbMenuId = (int) $db->lastInsertId();
        }

        if ($ff) {
            $menuStore = $ff->store('basepackages_menus');

            $menuStore->setValidateData(false);

            $menuStore->updateOrInsert($menuToRegister);

            $ffMenuId = (int) $menuStore->getLastInsertedId();
        }

        if ($dbMenuId !== null && $ffMenuId !== null) {
            if ($dbMenuId === $ffMenuId) {
                return $dbMenuId;
            }

            throw new Exception('Menu ids dont match for db and ff');
        } elseif ($dbMenuId !== null) {
            return $dbMenuId;
        } elseif ($ffMenuId !== null) {
            return $ffMenuId;
        }

        return null;
    }

    /**
     * Recursively injects sequence index into menu items hierarchy.
     *
     * @param array<string, mixed> $menu     Menu tree structure.
     * @param int                  $sequence Sequence weight.
     *
     * @return array<string, mixed>
     */
    protected function addSequence(array $menu, int $sequence): array
    {
        foreach ($menu as &$item) {
            if (is_array($item)) {
                $item = $this->addSequence($item, $sequence);
            }
        }

        return $menu;
    }
}