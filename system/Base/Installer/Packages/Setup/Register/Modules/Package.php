<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

class Package
{
	public function register($db, $ff, $packageFile, $helper)
	{
		$package =
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
					$helper->encode($packageFile['settings']) :
					$helper->encode([]),
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					$helper->encode($packageFile['dependencies']) :
					$helper->encode([]),
				'apps'					=>
					$helper->encode(['1'=>['enabled'=>true]]),
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($packageFile['files']) ?
					$helper->encode($packageFile['files']) :
					$helper->encode([]),
				'updated_by'			=> 0
			];

		if ($db) {
			$db->insertAsDict('modules_packages', $package);
		}

		if ($ff) {
			$packageStore = $ff->store('modules_packages');

			$packageStore->updateOrInsert($package);
		}
	}
}