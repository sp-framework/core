<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Domain
{
	public function register($db, $request)
	{
		$request->setStrictHostCheck(true);

		$settings =
		[
			"defaultApplication"	=> "Admin"
		];

		$db->insertAsDict(
			'domains',
			[
				'domain' 				=> $request->getHttpHost(),
				'description' 			=> '',
				'settings'			 	=> json_encode($settings)
			]
		);
	}
}