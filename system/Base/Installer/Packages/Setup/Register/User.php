<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class User
{
    public function register($db, $email, $password, $newApplicationId, $adminRoleId)
    {
        $permissions =
            Json::encode(
                [
                    'permissions'   => []
                ]
            );

        $insertAdmin = $db->insertAsDict(
            'users',
            [
                'email'                 => $email,
                'password'              => $password,
                'role_id'               => $adminRoleId,
                'override_role'         => 0,
                'permissions'           => $permissions,
                'can_login'             => Json::encode(['Admin' => true])
            ]
        );

        if ($insertAdmin) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}