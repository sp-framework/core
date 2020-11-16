<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Role
{
    public function register($db)
    {
        $insertRootRole = $db->insertAsDict(
            'roles',
            [
                'name'              => '1011',
                'description'       => 'Beep Boop Beep Beep',
                'parent_id'         => '0',
                'permissions'       => Json::encode([])
            ]
        );

        if ($insertRootRole) {
            $insertAdminRole = $db->insertAsDict(
                'roles',
                [
                    'name'              => 'System Administrators',
                    'description'       => 'System Administrators Role',
                    'parent_id'         => $db->lastInsertId(),
                    'permissions'       => Json::encode([])
                ]
            );

            if ($insertAdminRole) {
                return $db->lastInsertId();
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}