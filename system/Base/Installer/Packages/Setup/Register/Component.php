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
				'category'  			=> $componentFile['category'],
				'sub_category'  		=> $componentFile['sub_category'],
				'version'				=> $componentFile['version'],
				'class'					=> $componentFile['class'],
				'repo'					=> $componentFile['repo'],
				'settings'				=>
					isset($componentFile['settings']) ?
					Json::encode($componentFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					Json::encode($componentFile['dependencies']) :
					null,
				'menu'		 			=>
					isset($componentFile['menu']) ?
					Json::encode($componentFile['menu']) :
					false,
				'applications'			=>
					Json::encode(['1'=>['installed'=>true,'menu_id'=>$menuId]]),
				'files'					=> Json::encode($installedFiles)
			]
		);
	}
}