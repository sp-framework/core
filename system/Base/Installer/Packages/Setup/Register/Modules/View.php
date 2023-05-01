<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class View
{
	public function register($db, $viewFile)
	{
		return $db->insertAsDict(
			'modules_views',
			[
				'name' 					=> $viewFile['name'],
				'display_name' 			=> $viewFile['display_name'],
				'description' 			=> $viewFile['description'],
				'module_type' 			=> $viewFile['module_type'],
				'app_type' 				=> $viewFile['app_type'],
				'category'  			=> $viewFile['category'],
				'sub_category'  		=> $viewFile['sub_category'],
				'version'				=> $viewFile['version'],
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					Json::encode($viewFile['settings']) :
					null,
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					Json::encode($viewFile['dependencies']) :
					null,
				'apps'					=>
					Json::encode(['1'=>['enabled'=>true]]),
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($viewFile['files']) ?
					Json::encode($viewFile['files']) :
					Json::encode([]),
				'updated_by'			=> 0
			]
		);
	}
}