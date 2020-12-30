<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Core
{
	public function register($installedFiles, $baseConfig, $db)
	{
		// if (isset($baseConfig['settings'])) {
		// 	$baseConfig['settings']['db'] =
		// 		[
		// 			"host" 		=> $postData['host'],
		// 			"dbname" 	=> $postData['database_name'],
		// 			"username" 	=> $postData['username'],
		// 			"password" 	=> $postData['password'],
		// 			"port" 		=> $postData['port']
		// 		];
		// }

		$db->insertAsDict(
			'core',
			[
				'name' 					=> $baseConfig['name'],
				'display_name'			=> $baseConfig['displayName'],
				'description' 			=> $baseConfig['description'],
				'version'	 			=> $baseConfig['version'],
				'repo'					=> $baseConfig['repo'],
				'settings'			 	=>
					isset($baseConfig['settings']) ?
					json_encode($baseConfig['settings']) :
					null,
				'files'					=> json_encode($installedFiles),
				'updated_by'			=> 0
			]
		);
	}
}