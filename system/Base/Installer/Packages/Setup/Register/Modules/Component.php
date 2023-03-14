<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Component
{
	public function register($db, $componentFile, $installedFiles, $menuId)
	{
		$componentApp = ['1'=>['enabled'=>true]];

		if (isset($componentFile['settings']['needAuth'])) {
			if ($componentFile['settings']['needAuth'] === 'mandatory') {
				$componentApp['1']['needAuth'] = 'mandatory';
			} else if ($componentFile['settings']['needAuth'] === 'disabled') {
				$componentApp['1']['needAuth'] = 'disabled';
			}
		} else {
			$componentApp['1']['needAuth'] = true;
		}

		$db->insertAsDict(
			'modules_components',
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
				'apps'					=>
					Json::encode($componentApp),
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