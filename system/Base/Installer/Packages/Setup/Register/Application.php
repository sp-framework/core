<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Db\Enum;
use Phalcon\Helper\Json;

class Application
{
	public function register($db)
	{
		$insertApplication = $db->insertAsDict(
			'applications',
			[
				'name' 						=> 'Admin',
				'route' 					=> 'admin',
				'description' 				=> 'Ecom Admin App',
				'category'	    			=> 'ecom',
				'sub_category'  			=> 'admin',
				'default_component'			=> 0,
				'errors_component'			=> 0,
				'can_login_role_ids'		=> Json::encode(['1']),
			]
		);

		if ($insertApplication) {
			return $db->lastInsertId();
		} else {
			return null;
		}
	}

	public function update($db)
	{
		$homeComponent =
			$db->fetchAll(
				"SELECT * FROM components WHERE route LIKE :route",
				Enum::FETCH_ASSOC,
				[
					"route" => "home",
				]
			);

		$errorsComponent =
			$db->fetchAll(
				"SELECT * FROM components WHERE route LIKE :route",
				Enum::FETCH_ASSOC,
				[
					"route" => "errors",
				]
			);

		$db->updateAsDict(
			'applications',
			[
				'default_component' 	=> $homeComponent[0]['id'],
				'errors_component' 		=> $errorsComponent[0]['id']
			],
			"id = 1"
		);
	}
}