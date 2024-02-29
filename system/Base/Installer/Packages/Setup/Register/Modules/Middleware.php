<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

class Middleware
{
	public function register($db, $ff, $middlewareFile)
	{
		if ($middlewareFile['name'] === 'Auth') {
			$apps = $this->helper->encode(['1' => ['enabled' => true, 'sequence' => 1]]);
		} else if ($middlewareFile['name'] === 'Acl') {
			$apps = $this->helper->encode(['1' => ['enabled' => true, 'sequence' => 2]]);
		} else {
			$apps = $this->helper->encode(['1' => ['enabled' => false, 'sequence' => 0]]);
		}

		$middleware =
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
					$this->helper->encode($middlewareFile['settings']) :
					$this->helper->encode([]),
				'dependencies'		 	=>
					isset($middlewareFile['dependencies']) ?
					$this->helper->encode($middlewareFile['dependencies']) :
					$this->helper->encode([]),
				'apps'					=> $apps,
				'api_id'				=> 1,
				'installed'				=> 1,
				'files'					=>
					isset($middlewareFile['files']) ?
					$this->helper->encode($middlewareFile['files']) :
					$this->helper->encode([]),
				'updated_by'			=> 0
			];

		if ($db) {
			$db->insertAsDict('modules_middlewares', $middleware);
		}

		if ($ff) {
			$middlewareStore = $ff->store('modules_middlewares');

			$middlewareStore->updateOrInsert($middleware);
		}
	}
}