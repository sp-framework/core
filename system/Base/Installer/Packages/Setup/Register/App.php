<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Db\Enum;
use Phalcon\Helper\Json;

class App
{
	public function register($db)
	{
		$insertApp = $db->insertAsDict(
			'apps',
			[
				'name' 						=> 'Admin',
				'route' 					=> 'admin',
				'description' 				=> 'Admin App',
				'app_type'       			=> 'dash',
				'default_component'			=> 0,
				'errors_component'			=> 0,
				'can_login_role_ids'		=> Json::encode(['1']),
				'acceptable_usernames'		=> Json::encode(["email", "username"]),
				'ip_filter_default_action'	=> 0,
				'settings'					=> Json::encode(["defaultDashboard" => 1])
			]
		);

		if ($insertApp) {
			return $db->lastInsertId();
		} else {
			return null;
		}
	}

	public function update($db)
	{
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
			'apps',
			[
				'default_component' 	=> $dashboardsComponent[0]['id'],
				'errors_component' 		=> $errorsComponent[0]['id']
			],
			"id = 1"
		);
	}
}