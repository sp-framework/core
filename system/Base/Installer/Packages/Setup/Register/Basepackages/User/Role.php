<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

class Role
{
    protected $helper;

    public function registerCoreRole($db, $ff, $helper)
    {
        $this->addRoles($db, $ff, $helper,
            [
                [
                    'name'              => 'System Administrators',
                    'description'       => 'System Administrators Role',
                    'type'              => 0,
                    'permissions'       => $helper->encode([])
                ]
            ]
        );
    }

    public function registerAdditionalRoles($db, $ff, $helper)
    {
        $this->addRoles($db, $ff, $helper,
            [
                [
                    'name'              => 'Super Users',
                    'description'       => 'Super Users Role',
                    'type'              => 1,
                    'permissions'       => $helper->encode([])
                ],
                [
                    'name'              => 'Registered Users',
                    'description'       => 'Registered Users Role',
                    'type'              => 1,
                    'permissions'       => $helper->encode([])
                ],
                [
                    'name'              => 'Guests',
                    'description'       => 'Guests Role',
                    'type'              => 1,
                    'permissions'       => $helper->encode([])
                ]
            ]
        );
    }

    protected function addRoles($db, $ff, $helper, $roles)
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