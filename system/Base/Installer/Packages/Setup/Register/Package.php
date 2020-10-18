<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Package
{
	public function register($db, $packageFile, $installedFiles, $newApplicationId)
	{
		return $db->insertAsDict(
			'packages',
			[
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['displayName'],
				'description' 			=> $packageFile['description'],
				'version'				=> $packageFile['version'],
				'path'					=> $packageFile['path'],
				'repo'					=> $packageFile['repo'],
				'settings'				=>
					isset($packageFile['settings']) ?
					json_encode($packageFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					json_encode($packageFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}
}