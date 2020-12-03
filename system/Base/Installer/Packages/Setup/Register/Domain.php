<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Domain
{
	public function register($db, $request)
	{
		$request->setStrictHostCheck(true);

		$applications =
		[
			'1' =>
			[
				'allowed'			=> true,
				'view'				=> '1',
				'email_service'		=> 0
			]
		];

		$db->insertAsDict(
			'domains',
			[
				'name'   					=> $request->getHttpHost(),
				'description' 				=> '',
				"default_application_id"	=> 1,
				"applications"			    => Json::encode($applications),
				'settings'			 		=> Json::encode([])
			]
		);
	}
}