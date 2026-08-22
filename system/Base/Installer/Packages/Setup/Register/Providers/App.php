<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Providers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use Phalcon\Db\Enum;

/**
 * Seeds default Core Application provider record and updates default component bindings.
 */
class App
{
    /**
     * Registers default Core application entry.
     *
     * @param mixed $db     PDO database connection adapter.
     * @param mixed $ff     FlatFile database manager.
     * @param mixed $helper Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, mixed $helper): void
    {
        $coreApp = [
            'name'                     => 'Core',
            'route'                    => 'core',
            'description'              => 'Core App',
            'app_type'                 => 'core',
            'default_component_guests' => 0,
            'default_component_users'  => 0,
            'errors_component'         => 0,
            'can_login_role_ids'       => $helper->encode(['1']),
            'acceptable_usernames'     => $helper->encode(['email', 'username']),
            'settings'                 => $helper->encode(['defaultDashboard' => 1])
        ];

        if ($db) {
            $db->insertAsDict('service_provider_apps', $coreApp);
        }

        if ($ff) {
            $appStore = $ff->store('service_provider_apps');

            $appStore->updateOrInsert($coreApp);
        }
    }

    /**
     * Updates default and error component bindings for Core application.
     *
     * @param mixed $db PDO database connection adapter.
     * @param mixed $ff FlatFile database manager.
     *
     * @return void
     */
    public function update(mixed $db, mixed $ff): void
    {
        if ($db) {
            $homeComponent = $db->fetchAll(
                'SELECT * FROM modules_components WHERE route LIKE :route',
                Enum::FETCH_ASSOC,
                ['route' => 'home']
            );

            $dashboardsComponent = $db->fetchAll(
                'SELECT * FROM modules_components WHERE route LIKE :route',
                Enum::FETCH_ASSOC,
                ['route' => 'dashboards']
            );

            $errorsComponent = $db->fetchAll(
                'SELECT * FROM modules_components WHERE route LIKE :route',
                Enum::FETCH_ASSOC,
                ['route' => 'errors']
            );

            $guestId = $homeComponent[0]['id'] ?? 0;
            $userId = $dashboardsComponent[0]['id'] ?? 0;
            $errorId = $errorsComponent[0]['id'] ?? 0;

            $db->updateAsDict(
                'service_provider_apps',
                [
                    'default_component_guests' => $guestId,
                    'default_component_users'  => $userId,
                    'errors_component'         => $errorId
                ],
                'id = 1'
            );
        }

        if ($ff) {
            $modulesStore = $ff->store('modules_components');

            $homeComponent = $modulesStore?->findOneBy(['route', '=', 'home']);
            $dashboardsComponent = $modulesStore?->findOneBy(['route', '=', 'dashboards']);
            $errorsComponent = $modulesStore?->findOneBy(['route', '=', 'errors']);

            $appStore = $ff->store('service_provider_apps');
            $app = $appStore?->findById('1');

            if ($app && is_array($app)) {
                $app['default_component_guests'] = $homeComponent['id'] ?? 0;
                $app['default_component_users'] = $dashboardsComponent['id'] ?? 0;
                $app['errors_component'] = $errorsComponent['id'] ?? 0;

                $appStore->updateOrInsert($app);
            }
        }
    }
}