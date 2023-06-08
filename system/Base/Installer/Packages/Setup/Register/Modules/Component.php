<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Component
{
	public function register($db, $ff, $componentFile, $menuId)
	{
		$componentApp = ['1' => ['enabled'=>true]];

		if (isset($componentFile['settings']['needAuth']) &&
			$componentFile['settings']['needAuth'] !== true
		) {
			$componentApp['1']['needAuth'] = $componentFile['settings']['needAuth'];
		} else {
			$componentApp['1']['needAuth'] = true;
		}

		$component =
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
					Json::encode([]),
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
					Json::encode([]),
				'widgets'				=>
					isset($componentFile['widgets']) ?
					Json::encode($componentFile['widgets']) :
					Json::encode([]),
				'updated_by'			=> 0
			];

		if ($db) {
			$db->insertAsDict('modules_components', $component);

			$dbComponentId = (int) $db->lastInsertId();
		}

		if ($ff) {
			$componentStore = $ff->store('modules_components');

			$componentStore->updateOrInsert($component);

			$ffComponentId = (int) $componentStore->getLastInsertedId();
		}

		if (isset($dbComponentId) && isset($ffComponentId)) {
			if ($dbComponentId == $ffComponentId) {
				return $dbComponentId;
			}

			throw new \Exception('Component ids dont match for db and ff');
		} else if (isset($dbComponentId) && !isset($ffComponentId)) {
			return $dbComponentId;
		} else if (!isset($dbComponentId) && isset($ffComponentId)) {
			return $ffComponentId;
		}

		return null;
	}
}