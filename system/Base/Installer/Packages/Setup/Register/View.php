<?php

namespace System\Base\Installer\Packages\Setup\Register;

class View
{
	public function register($db, $viewFile, $installedFiles, $newApplicationId)
	{
		return $db->insertAsDict(
			'views',
			[
				'name' 					=> $viewFile['name'],
				'display_name' 			=> $viewFile['displayName'],
				'description' 			=> $viewFile['description'],
				'version'				=> $viewFile['version'],
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					json_encode($viewFile['settings']) :
					null,
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					json_encode($viewFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}
}