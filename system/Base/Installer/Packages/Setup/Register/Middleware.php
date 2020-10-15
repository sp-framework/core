<?php

namespace System\Base\Installer\Packages\Setup\Register;

class Middleware
{
	public function register($db, $middlewareFile, $installedFiles, $newApplicationId)
	{
		return $db->insertAsDict(
			'middlewares',
			[
				'name' 					=> $middlewareFile['name'],
				'display_name' 			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'path'					=> $middlewareFile['path'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					json_encode($middlewareFile['settings']) :
					null,
				'dependencies'			=>
					isset($middlewareFile['dependencies']) ?
					json_encode($middlewareFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'sequence'				=> 0,
				'enabled'				=> 0,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}
}