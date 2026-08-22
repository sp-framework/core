<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\User
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

/**
 * Seeds default System Administrators, Super Users, Registered Users, and Guests roles.
 */
class Role
{
    /**
     * Registers default System Administrators role.
     *
     * @param mixed $db     PDO database connection adapter.
     * @param mixed $ff     FlatFile database manager.
     * @param mixed $helper Helpers service instance.
     *
     * @return void
     */
    public function registerCoreRole(mixed $db, mixed $ff, mixed $helper): void
    {
        $this->addRoles(
            $db,
            $ff,
            $helper,
            [
                [
                    'name'        => 'System Administrators',
                    'description' => 'System Administrators Role',
                    'type'        => 0,
                    'permissions' => method_exists($helper, 'encode') ? $helper->encode([]) : '[]'
                ]
            ]
        );
    }

    /**
     * Registers standard auxiliary roles (Super Users, Registered Users, Guests).
     *
     * @param mixed $db     PDO database connection adapter.
     * @param mixed $ff     FlatFile database manager.
     * @param mixed $helper Helpers service instance.
     *
     * @return void
     */
    public function registerAdditionalRoles(mixed $db, mixed $ff, mixed $helper): void
    {
        $this->addRoles(
            $db,
            $ff,
            $helper,
            [
                [
                    'name'        => 'Super Users',
                    'description' => 'Super Users Role',
                    'type'        => 1,
                    'permissions' => method_exists($helper, 'encode') ? $helper->encode([]) : '[]'
                ],
                [
                    'name'        => 'Registered Users',
                    'description' => 'Registered Users Role',
                    'type'        => 1,
                    'permissions' => method_exists($helper, 'encode') ? $helper->encode([]) : '[]'
                ],
                [
                    'name'        => 'Guests',
                    'description' => 'Guests Role',
                    'type'        => 1,
                    'permissions' => method_exists($helper, 'encode') ? $helper->encode([]) : '[]'
                ]
            ]
        );
    }

    /**
     * Persists role definitions into basepackages_users_roles table and store.
     *
     * @param mixed                $db     PDO database connection adapter.
     * @param mixed                $ff     FlatFile database manager.
     * @param mixed                $helper Helpers service instance.
     * @param array<int, mixed>    $roles  Roles list array.
     *
     * @return void
     */
    protected function addRoles(mixed $db, mixed $ff, mixed $helper, array $roles): void
    {
        foreach ($roles as $role) {
            if ($db) {
                $db->insertAsDict('basepackages_users_roles', $role);
            }

            if ($ff) {
                $roleStore = $ff->store('basepackages_users_roles');

                $roleStore->updateOrInsert($role);
            }
        }
    }
}