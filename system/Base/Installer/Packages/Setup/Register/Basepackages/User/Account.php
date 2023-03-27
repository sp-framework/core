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
                'status'                => '1',
                'email'                 => $email,
                'username'              => explode('@', $email)[0],
                'domain'                => explode('@', $email)[1],
                'package_name'          => 'profiles',
                'package_row_id'        => '1'
            ]
        );

        if ($insertAdmin) {
            $this->registerAccountSecurity($db->lastInsertId(), $db, $password, $adminRoleId);

            return $db->lastInsertId();
        } else {
            return null;
        }
    }

    protected function registerAccountSecurity($id, $db, $password, $adminRoleId)
    {
        $insertAdmin = $db->insertAsDict(
            'basepackages_users_accounts_security',
            [
                'account_id'            => $id,
                'password'              => $password,
                'role_id'               => $adminRoleId,
                'override_role'         => 0,
                'permissions'           => Json::encode([])
            ]
        );
    }
}