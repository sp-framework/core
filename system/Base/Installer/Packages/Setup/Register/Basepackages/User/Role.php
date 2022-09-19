<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use Phalcon\Helper\Json;

class Role
{
    public function registerAdminRole($db)
    {
        $insertAdminRole = $db->insertAsDict(
            'basepackages_users_roles',
            [
                'name'              => 'System Administrators',
                'description'       => 'System Administrators Role',
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
                'permissions'       => Json::encode([])
            ]
        );

        $db->insertAsDict(
            'basepackages_users_roles',
            [
                'name'              => 'Guests',
                'description'       => 'Guests Role',
                'permissions'       => Json::encode([])
            ]
        );        
    }
}