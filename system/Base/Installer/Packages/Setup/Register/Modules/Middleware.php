<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Helper\Json;

class Middleware
{
	public function register($db, $middlewareFile)
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
				'display_name' 			=> $middlewareFile['display_name'],
				'description' 			=> $middlewareFile['description'],
				'module_type'			=> $middlewareFile['module_type'],
				'app_type' 				=> $middlewareFile['app_type'],
				'category'  			=> $middlewareFile['category'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					Json::encode($middlewareFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($middlewareFile['dependencies']) ?
					Json::encode($middlewareFile['dependencies']) :
					null,
				'apps'					=> $apps,
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($middlewareFile['files']) ?
					Json::encode($middlewareFile['files']) :
					Json::encode([]),
				'updated_by'			=> 0
			]
		);
	}
}