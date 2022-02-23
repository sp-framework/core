<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Package
{
	public function register($db, $packageFile, $installedFiles)
	{
		return $db->insertAsDict(
			'modules_packages',
			[
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['display_name'],
				'description' 			=> $packageFile['description'],
				'app_type'		 		=> $packageFile['app_type'],
				'category'				=> $packageFile['category'],
				'sub_category'			=> $packageFile['sub_category'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'class'					=> $packageFile['class'],
				'settings'				=>
					isset($packageFile['settings']) ?
					Json::encode($packageFile['settings']) :
					null,
				'apps'					=>
					Json::encode(['1'=>['enabled'=>true]]),
				'installed'				=> 1,
				'files'					=> Json::encode($installedFiles),
				'updated_by'			=> 0
			]
		);
	}
}