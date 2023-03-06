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
				'ip_filter_default_action'	=> 0,
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
		$homeComponent =
			$db->fetchAll(
				"SELECT * FROM modules_components WHERE route LIKE :route",
				Enum::FETCH_ASSOC,
				[
					"route" => "home",
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
				'default_component' 	=> $homeComponent[0]['id'],
				'errors_component' 		=> $errorsComponent[0]['id']
			],
			"id = 1"
		);
	}
}