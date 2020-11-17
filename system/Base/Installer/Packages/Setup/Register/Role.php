<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Role
{
    public function register($db)
    {
        $insertAdminRole = $db->insertAsDict(
            'roles',
            [
                'name'              => 'System Administrators',
                'description'       => 'System Administrators Role',
                'permissions'       => Json::encode([]),
                'users'             => Json::encode([1])
            ]
        );

        if ($insertAdminRole) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}