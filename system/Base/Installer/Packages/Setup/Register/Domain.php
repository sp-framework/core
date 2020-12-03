<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Domain
{
	public function register($db, $request)
	{
		$request->setStrictHostCheck(true);

		$db->insertAsDict(
			'domains',
			[
				'name'   					=> $request->getHttpHost(),
				'description' 				=> '',
				"default_application_id"	=> 1,
				"allowed_applications"	    => Json::encode(["1" => true]),
				'settings'			 		=> json_encode([])
			]
		);
	}
}