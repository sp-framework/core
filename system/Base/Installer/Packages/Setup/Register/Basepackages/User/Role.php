<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use Phalcon\Helper\Json;

class Role
{
    public function registerCoreRole($db, $ff)
    {
        $role =
            [
                'name'              => 'System Administrators',
                'description'       => 'System Administrators Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_roles', $role);
        }

        if ($ff) {
            $roleStore = $ff->store('basepackages_users_roles');

            $roleStore->updateOrInsert($role);
        }
    }

    public function registerRegisteredUserAndGuestRoles($db, $ff)
    {
        $registered =
            [
                'name'              => 'Registered Users',
                'description'       => 'Registered Users Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ];

        $guest =
            [
                'name'              => 'Guests',
                'description'       => 'Guests Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ];


        if ($db) {
            $db->insertAsDict('basepackages_users_roles', $registered);
            $db->insertAsDict('basepackages_users_roles', $guest);
        }

        if ($ff) {
            $roleStore = $ff->store('basepackages_users_roles');

            $roleStore->updateOrInsert($registered);
            $roleStore->updateOrInsert($guest);
        }
    }
}