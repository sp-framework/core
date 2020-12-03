<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Middleware
{
	public function register($db, $middlewareFile, $installedFiles)
	{
		return $db->insertAsDict(
			'middlewares',
			[
				'name' 					=> $middlewareFile['name'],
				'display_name' 			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					Json::encode($middlewareFile['settings']) :
					null,
				'applications'			=>
					Json::encode(['1'=>['installed'=>true,'sequence'=>0,'enabled'=>false]]),
				'files'					=> Json::encode($installedFiles)
			]
		);
	}
}