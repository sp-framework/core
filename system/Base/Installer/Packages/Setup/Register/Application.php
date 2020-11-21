<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Application
{
	public function register($db, $applicationFile, $installedFiles, $mode)
	{
		$insertApplication = $db->insertAsDict(
			'applications',
			[
				'route' 				=> $applicationFile['route'],
				'name' 					=> $applicationFile['name'],
				'display_name' 			=> $applicationFile['displayName'],
				'description' 			=> $applicationFile['description'],
				'version'				=> $applicationFile['version'],
				'repo'					=> $applicationFile['repo'],
				'settings'			 	=>
					isset($applicationFile['settings']) ?
					json_encode($applicationFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($applicationFile['dependencies']) ?
					json_encode($applicationFile['dependencies']) :
					null,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles),
				'mode'					=> $mode === 'true' ? 0 : 1
			]
		);

		if ($insertApplication) {
			return $db->lastInsertId();
		} else {
			return null;
		}
	}
}