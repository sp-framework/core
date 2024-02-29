<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

class View
{
	public function register($db, $ff, $viewFile)
	{
		$view =
			[
				'name' 					=> $viewFile['name'],
				'display_name' 			=> $viewFile['display_name'],
				'description' 			=> $viewFile['description'],
				'module_type' 			=> $viewFile['module_type'],
				'app_type' 				=> $viewFile['app_type'],
				'category'  			=> $viewFile['category'],
				'version'				=> $viewFile['version'],
				'view_modules_version'	=> '0.0.0.0',
				'base_view_module_id'	=> 0,
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					$this->helper->encode($viewFile['settings']) :
					$this->helper->encode([]),
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					$this->helper->encode($viewFile['dependencies']) :
					$this->helper->encode([]),
				'apps'					=>
					$this->helper->encode(['1'=>['enabled'=>true]]),
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($viewFile['files']) ?
					$this->helper->encode($viewFile['files']) :
					$this->helper->encode([]),
				'updated_by'			=> 0
			];

		$viewSettings =
			[
				'view_id'				=> 1,
				'domain_id' 			=> 1,
				'app_id'	 			=> 1,
				'settings'				=>
					isset($viewFile['settings']) ?
					$this->helper->encode($viewFile['settings']) :
					$this->helper->encode([])
			];

		if ($db) {
			$db->insertAsDict('modules_views', $view);

			$db->insertAsDict('modules_views_settings', $viewSettings);
		}

		if ($ff) {
			$viewsStore = $ff->store('modules_views');
			$viewsSettingsStore = $ff->store('modules_views_settings');

			$viewsStore->updateOrInsert($view);
			$viewsSettingsStore->updateOrInsert($viewSettings);
		}
	}
}