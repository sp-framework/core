<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Component
{
	public function register($db, $componentFile, $installedFiles, $menuId)
	{
		$db->insertAsDict(
			'components',
			[
				'name' 					=> $componentFile['name'],
				'route'					=> $componentFile['route'],
				'description' 			=> $componentFile['description'],
				'app_type' 				=> $componentFile['app_type'],
				'category'  			=> $componentFile['category'],
				'sub_category'  		=> $componentFile['sub_category'],
				'version'				=> $componentFile['version'],
				'class'					=> $componentFile['class'],
				'repo'					=> $componentFile['repo'],
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					Json::encode($componentFile['dependencies']) :
					null,
				'menu'		 			=>
					isset($componentFile['menu']) ?
					Json::encode($componentFile['menu']) :
					false,
				'menu_id'				=> $menuId,
				'installed'				=> 1,
				'applications'			=>
					Json::encode(['1'=>['enabled'=>true]]),
				'files'					=> Json::encode($installedFiles),
				'settings'				=>
					isset($componentFile['settings']) ?
					Json::encode($componentFile['settings']) :
					null,
				'updated_by'			=> 0
			]
		);
	}
}