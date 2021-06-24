<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Domain
{
	public function register($db, $request)
	{
		$request->setStrictHostCheck(true);

		$apps =
		[
			'1' =>
			[
				'allowed'			=> true,
				'view'				=> '1',
				'email_service'		=> 0,
				'storage'			=> 0,
				'publicStorage'		=> 1,
				'privateStorage'	=> 2
			]
		];

		$db->insertAsDict(
			'domains',
			[
				'name'   							=> $request->getHttpHost(),
				'description' 						=> '',
				"default_app_id"					=> 1,
				"exclusive_to_default_app"			=> 0,
				"apps"			    				=> Json::encode($apps),
				'settings'			 				=> Json::encode([])
			]
		);
	}
}