<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use Phalcon\Db\Enum;

class App
{
	public function register($db, $ff, $helper)
	{
		$coreApp =
			[
				'name' 								=> 'Core',
				'route' 							=> 'core',
				'description' 						=> 'Core App',
				'app_type'       					=> 'core',
				'default_component_guests'  		=> 0,
				'default_component_users'   		=> 0,
				'errors_component'					=> 0,
				'can_login_role_ids'				=> $helper->encode(['1']),
				'acceptable_usernames'				=> $helper->encode(["email", "username"]),
				'ip_filter_default_action'			=> 'allow',
				'incorrect_login_attempt_block_ip'	=> 0,
				'auto_unblock_ip_minutes'			=> 0,
				'settings'							=> $helper->encode(["defaultDashboard" => 1])
			];

		if ($db) {
			$db->insertAsDict('service_provider_apps', $coreApp);
		}

		if ($ff) {
			$appStore = $ff->store('service_provider_apps');

			$appStore->updateOrInsert($coreApp);
		}
	}

	public function update($db, $ff)
	{
		if ($db) {
			$homeComponent =
				$db->fetchAll(
					"SELECT * FROM modules_components WHERE route LIKE :route",
					Enum::FETCH_ASSOC,
					[
						"route" => "home",
					]
				);

			$dashboardsComponent =
				$db->fetchAll(
					"SELECT * FROM modules_components WHERE route LIKE :route",
					Enum::FETCH_ASSOC,
					[
						"route" => "dashboards",
					]
				);

			$errorsComponent =
				$db->fetchAll(
					"SELECT * FROM modules_components WHERE route LIKE :route",
					Enum::FETCH_ASSOC,
					[
						"route" => "errors",
					]
				);

			$db->updateAsDict(
				'service_provider_apps',
				[
					'default_component_guests' 	=> $homeComponent[0]['id'],
					'default_component_users' 	=> $dashboardsComponent[0]['id'],
					'errors_component' 			=> $errorsComponent[0]['id']
				],
				"id = 1"
			);
		}

		if ($ff) {
			$modulesStore = $ff->store('modules_components');

			$homeComponent = $modulesStore->findOneBy(['route', '=', 'home']);
			$dashboardsComponent = $modulesStore->findOneBy(['route', '=', 'dashboards']);
			$errorsComponent = $modulesStore->findOneBy(['route', '=', 'errors']);

			$appStore = $ff->store('service_provider_apps');

			$app = $appStore->findById('1');

			$app['default_component_guests'] = $homeComponent['id'];
			$app['default_component_users'] = $dashboardsComponent['id'];
			$app['errors_component'] = $errorsComponent['id'];

			$appStore->updateOrInsert($app);
		}
	}
}