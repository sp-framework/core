<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\User
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\User;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profiles;

/**
 * Seeds initial superadmin user account, security credentials, and user environment settings.
 */
class Account
{
    /**
     * Registers default administrator account into accounts, security, and environment tables.
     *
     * @param mixed  $db       PDO database connection adapter.
     * @param mixed  $ff       FlatFile database manager.
     * @param string $email    Administrator email address.
     * @param string $password Bcrypt-hashed password.
     * @param mixed  $helper   Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, string $email, string $password, mixed $helper): void
    {
        $parts = explode('@', $email);
        $username = $parts[0] ?? 'admin';
        $domain = $parts[1] ?? 'example.com';

        $account = [
            'status'                 => '1',
            'email'                  => $email,
            'username'               => $username,
            'domain'                 => $domain,
            'profile_package_class'  => str_replace('\\', '_', Profiles::class),
            'profile_package_row_id' => 1
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

    /**
     * Seeds initial account password and role security row.
     *
     * @param mixed  $db       PDO database connection adapter.
     * @param mixed  $ff       FlatFile database manager.
     * @param string $password Bcrypt-hashed password.
     * @param mixed  $helper   Helpers service instance.
     *
     * @return void
     */
    protected function registerAccountSecurity(mixed $db, mixed $ff, string $password, mixed $helper): void
    {
        $security = [
            'account_id'      => 1,
            'password'        => $password,
            'role_id'         => 1,
            'override_role'   => 0,
            'permissions'     => method_exists($helper, 'encode') ? $helper->encode([]) : '[]',
            'password_set_on' => time()
        ];

        if ($db) {
            $db->insertAsDict('basepackages_users_accounts_security', $security);
        }

        if ($ff) {
            $securityStore = $ff->store('basepackages_users_accounts_security');

            $securityStore->updateOrInsert($security);
        }
    }

    /**
     * Seeds initial account environment settings row.
     *
     * @param mixed $db     PDO database connection adapter.
     * @param mixed $ff     FlatFile database manager.
     * @param mixed $helper Helpers service instance.
     *
     * @return void
     */
    protected function registerAccountEnv(mixed $db, mixed $ff, mixed $helper): void
    {
        $env = [
            'account_id' => 1,
            'params'     => method_exists($helper, 'encode') ? $helper->encode(['1' => []]) : '{"1":[]}'
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