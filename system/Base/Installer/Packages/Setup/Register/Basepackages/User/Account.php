<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use Phalcon\Helper\Json;

class Account
{
    public function register($db, $email, $password, $adminRoleId)
    {
        $insertAdmin = $db->insertAsDict(
            'basepackages_users_accounts',
            [
                'email'                 => $email,
                'domain'                => explode('@', $email)[1],
                'password'              => $password,
                'role_id'               => $adminRoleId,
                'override_role'         => 0,
                'permissions'           => Json::encode([])
            ]
        );

        if ($insertAdmin) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}