<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Component
{
	public function register($db, $componentFile, $menuId)
	{
		$componentApp = ['1'=>['enabled'=>true]];

		if (isset($componentFile['settings']['needAuth']) &&
			$componentFile['settings']['needAuth'] !== true
		) {
			$componentApp['1']['needAuth'] = $componentFile['settings']['needAuth'];
		} else {
			$componentApp['1']['needAuth'] = true;
		}

		$insertComponent = $db->insertAsDict(
			'modules_components',
			[
				'name' 					=> $componentFile['name'],
				'route'					=> $componentFile['route'],
				'description' 			=> $componentFile['description'],
				'module_type' 			=> $componentFile['module_type'],
				'app_type' 				=> $componentFile['app_type'],
				'category'  			=> $componentFile['category'],
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
				'api_id'				=> 1,
				'installed'				=> 1,
				'apps'					=>
					Json::encode($componentApp),
				'files'					=>
					isset($componentFile['files']) ?
					Json::encode($componentFile['files']) :
					Json::encode([]),
				'settings'				=>
					isset($componentFile['settings']) ?
					Json::encode($componentFile['settings']) :
					null,
				'updated_by'			=> 0
			]
		);

		if ($insertComponent) {
			return $db->lastInsertId();
		} else {
			return null;
		}
	}
}