<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use Phalcon\Helper\Json;

class Role
{
    public function registerCoreRole($db)
    {
        $insertAdminRole = $db->insertAsDict(
            'basepackages_users_roles',
            [
                'name'              => 'System Administrators',
                'description'       => 'System Administrators Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ]
        );

        if ($insertAdminRole) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }

    public function registerRegisteredUserAndGuestRoles($db)
    {
        $db->insertAsDict(
            'basepackages_users_roles',
            [
                'name'              => 'Registered Users',
                'description'       => 'Registered Users Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ]
        );

        $db->insertAsDict(
            'basepackages_users_roles',
            [
                'name'              => 'Guests',
                'description'       => 'Guests Role',
                'type'              => 0,
                'permissions'       => Json::encode([])
            ]
        );        
    }
}