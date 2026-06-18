<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profiles;

class Account
{
    public function register($db, $ff, $email, $password, $helper)
    {
        $account =
            [
                'status'                    => '1',
                'email'                     => $email,
                'username'                  => explode('@', $email)[0],
                'domain'                    => explode('@', $email)[1],
                'profile_package_class'     => str_replace('\\', '_', Profiles::class),
                'profile_package_row_id'    => 1
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_accounts', $account);
        }

        if ($ff) {
            $accountStore = $ff->store('basepackages_users_accounts');

            $accountStore->updateOrInsert($account);
        }

        $this->registerAccountSecurity($db, $ff, $password, $helper);

        $this->registerAccountEnv($db, $ff, $helper);
    }

    protected function registerAccountSecurity($db, $ff, $password, $helper)
    {
        $security =
            [
                'account_id'            => 1,
                'password'              => $password,
                'role_id'               => 1,
                'override_role'         => 0,
                'permissions'           => $helper->encode([]),
                'password_set_on'       => time()
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_accounts_security', $security);
        }

        if ($ff) {
            $securityStore = $ff->store('basepackages_users_accounts_security');

            $securityStore->updateOrInsert($security);
        }
    }

    protected function registerAccountEnv($db, $ff, $helper)
    {
        $env =
            [
                'account_id'            => 1,
                'params'                => $helper->encode(['1' => []])
            ];

        if ($db) {
            $db->insertAsDict('basepackages_users_accounts_env', $env);
        }

        if ($ff) {
            $envStore = $ff->store('basepackages_users_accounts_env');

            $envStore->updateOrInsert($env);
        }
    }
}