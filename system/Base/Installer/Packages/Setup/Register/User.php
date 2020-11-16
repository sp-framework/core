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
                    'admin' =>
                        [
                            'can_login'     => true,
                            'gids'          => [$adminRoleId]
                        ]
                ]
            );

        $insertAdmin = $db->insertAsDict(
            'users',
            [
                'email'                 => $email,
                'password'              => $password,
                'permissions'             => $permissions
            ]
        );

        if ($insertAdmin) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}