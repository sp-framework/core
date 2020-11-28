<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Component
{
	public function register($db, $componentFile, $installedFiles, $newApplicationId, $menuId)
	{
		$db->insertAsDict(
			'components',
			[
				'name' 					=> $componentFile['name'],
				'route'					=> $componentFile['route'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'class'					=> $componentFile['class'],
				'repo'					=> $componentFile['repo'],
				'settings'				=>
					isset($componentFile['settings']) ?
					json_encode($componentFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					json_encode($componentFile['dependencies']) :
					null,
				'menu_id'		 		=> $menuId,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}
}