<?php

namespace System\Base\Installer\Packages\Setup\Register\User;

use Phalcon\Helper\Json;

class Account
{
    public function register($db, $email, $password, $adminRoleId)
    {
        $permissions =
            Json::encode(
                [
                    'permissions'   => []
                ]
            );

        $insertAdmin = $db->insertAsDict(
            'accounts',
            [
                'email'                 => $email,
                'password'              => $password,
                'role_id'               => $adminRoleId,
                'override_role'         => 0,
                'permissions'           => $permissions,
                'can_login'             => Json::encode(['admin' => true])
            ]
        );

        if ($insertAdmin) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}