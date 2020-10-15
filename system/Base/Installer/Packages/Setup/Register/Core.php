<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Core
{
	public function register($installedFiles, $coreFile, $db, $postData)
	{
		if (isset($coreFile['settings'])) {
			$coreFile['settings']['db'] =
				[
					"host" 		=> $postData['host'],
					"dbname" 	=> $postData['database_name'],
					"username" 	=> $postData['username'],
					"password" 	=> $postData['password'],
					"port" 		=> $postData['port']
				];
		}

		$db->insertAsDict(
			'core',
			[
				'name' 					=> $coreFile['name'],
				'display_name'			=> $coreFile['displayName'],
				'description' 			=> $coreFile['description'],
				'version'	 			=> $coreFile['version'],
				'repo'					=> $coreFile['repo'],
				'installed'				=> 1,
				'settings'			 	=>
					isset($coreFile['settings']) ?
					json_encode($coreFile['settings']) :
					null,
				'files'					=> json_encode($installedFiles)
			]
		);
	}
}