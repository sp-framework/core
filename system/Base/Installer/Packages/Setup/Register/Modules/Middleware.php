<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Middleware
{
	public function register($db, $middlewareFile, $installedFiles)
	{
		if ($middlewareFile['name'] === 'Auth') {
			$apps = Json::encode(['1' => ['enabled' => true, 'sequence' => 1]]);
		} else if ($middlewareFile['name'] === 'Acl') {
			$apps = Json::encode(['1' => ['enabled' => true, 'sequence' => 2]]);
		} else {
			$apps = Json::encode(['1' => ['enabled' => false, 'sequence' => 0]]);
		}

		return $db->insertAsDict(
			'modules_middlewares',
			[
				'name' 					=> $middlewareFile['name'],
				'display_name' 			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'app_type' 				=> $middlewareFile['app_type'],
				'category'  			=> $middlewareFile['category'],
				'sub_category'  		=> $middlewareFile['sub_category'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					Json::encode($middlewareFile['settings']) :
					null,
				'apps'					=> $apps,
				'installed'				=> 1,
				'files'					=> Json::encode($installedFiles),
				'updated_by'			=> 0
			]
		);
	}
}