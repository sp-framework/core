<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Package
{
	public function register($db, $packageFile, $installedFiles)
	{
		return $db->insertAsDict(
			'packages',
			[
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['displayName'],
				'description' 			=> $packageFile['description'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'settings'				=>
					isset($packageFile['settings']) ?
					Json::encode($packageFile['settings']) :
					null,
				'applications'			=>
					Json::encode(['1'=>['installed'=>true]]),
				'files'					=> Json::encode($installedFiles)
			]
		);
	}
}