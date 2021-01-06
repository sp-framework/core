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
				'app_type'		 		=> $packageFile['app_type'],
				'category'				=> $packageFile['category'],
				'sub_category'			=> $packageFile['sub_category'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'settings'				=>
					isset($packageFile['settings']) ?
					Json::encode($packageFile['settings']) :
					null,
				'applications'			=>
					Json::encode(['1'=>['enabled'=>true]]),
				'installed'				=> 1,
				'files'					=> Json::encode($installedFiles),
				'updated_by'			=> 0
			]
		);
	}
}