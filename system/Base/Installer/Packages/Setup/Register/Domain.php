<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Domain
{
	public function register($db, $request, $homeComponentId)
	{
		$request->setStrictHostCheck(true);

		// $settings =
		// 	[
		// 		"applications" 		=>
		// 			[
		// 				"1" 				=>
		// 					[
		// 						"allowed" 			=> true,
		// 						"defaultComponent" 	=> $homeComponentId,
		// 						"defaultViews" 		=> "1",
		// 						"errorComponent"	=> "5"
		// 					]
		// 			]
		// 	];

		$db->insertAsDict(
			'domains',
			[
				'name'   					=> $request->getHttpHost(),
				'description' 				=> '',
				"default_application_id"	=> 1,
				"allowed_application_ids"	=> Json::encode(["1" => true])
				// 'settings'			 		=> json_encode($settings)
			]
		);
	}
}