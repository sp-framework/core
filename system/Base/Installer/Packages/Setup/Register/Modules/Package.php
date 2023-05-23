<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Package
{
	public function register($db, $packageFile)
	{
		return $db->insertAsDict(
			'modules_packages',
			[
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['display_name'],
				'description' 			=> $packageFile['description'],
				'module_type'	 		=> $packageFile['module_type'],
				'app_type'		 		=> $packageFile['app_type'],
				'category'				=> $packageFile['category'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'class'					=> $packageFile['class'],
				'settings'				=>
					isset($packageFile['settings']) ?
					Json::encode($packageFile['settings']) :
					Json::encode([]),
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					Json::encode($packageFile['dependencies']) :
					Json::encode([]),
				'apps'					=>
					Json::encode(['1'=>['enabled'=>true]]),
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($packageFile['files']) ?
					Json::encode($packageFile['files']) :
					Json::encode([]),
				'updated_by'			=> 0
			]
		);
	}
}