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
				'category'  			=> $middlewareFile['category'],
				'sub_category'  		=> $middlewareFile['sub_category'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					Json::encode($middlewareFile['settings']) :
					null,
				'applications'			=>
					Json::encode(['1' => ['enabled' => false, 'sequence' => 0]]),
				'installed'				=> 1,
				'files'					=> Json::encode($installedFiles),
				'updated_by'			=> 0
			]
		);
	}
}