<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Component
{
	public function register($db, $componentFile, $installedFiles, $newApplicationId, $menuId)
	{
		$insertComponent =  $db->insertAsDict(
			'components',
			[
				'route'					=> $componentFile['route'],
				'name' 					=> $componentFile['name'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'class'					=> $componentFile['class'],
				'repo'					=> $componentFile['repo'],
				'type'					=> $componentFile['type'],//listing, crud, other
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

		if ($insertComponent) {
			if ($componentFile['route'] === 'home') {
				return $db->lastInsertId();
			}
		}
		return null;
	}
}