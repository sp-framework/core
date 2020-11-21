<?php

namespace Applications\Admin\Packages\Barebone;

use System\Base\BasePackage;

class Register extends BasePackage
{
	protected $installedFiles;

	protected $postData;

	public function registerModule($type, $newApplicationId, $installedFiles, $names, $postData)
	{
		$this->installedFiles = $installedFiles;

		$this->postData = $postData;

		if ($type === 'applications') {

			$file = 'applications/' . $names['applicationName'] . '/application.json';

			return
				$this->registerBareboneApplication(
					json_decode(
						$this->localContent->read($file),
						true
					)
				);

		} else if ($type === 'components') {

			$file =
				'applications/' . $names['applicationName'] .
				'/Components/' . $names['componentName'] . '/Install/component.json';

			return
				$this->registerBareboneComponent(
					json_decode(
						$this->localContent->read($file), true
					),
					$newApplicationId
				);

		} else if ($type === 'packages') {

			$file =
				'applications/' . $names['applicationName'] .
				'/Packages/' . $names['packageName'] . '/Install/package.json';

			return $this->registerBarebonePackage(
				json_decode(
					$this->localContent->read($file), true
				),
				$newApplicationId
			);

		} else if ($type === 'middlewares') {

			$file =
				'applications/' . $names['applicationName'] .
				'/Middlewares/' . $names['middlewareName'] . '/Install/middleware.json';

			return $this->registerBareboneMiddleware(
				json_decode(
					$this->localContent->read($file), true
				),
				$newApplicationId
			);

		} else if ($type === 'views') {

			$file = 'applications/' . $names['applicationName'] . '/Views/' . $names['viewName'] . '/view.json';

			return $this->registerBareboneView(
				json_decode(
					$this->localContent->read($file), true
				),
				$newApplicationId
			);
		}
	}

	protected function registerBareboneApplication(array $applicationFile)
	{
		$application = $this->modules->applications->add(
			[
				// 'id'					=> '',
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
				// 'is_default'			=> $this->postData['default'] === 'true' ? 1 :0,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles),
				'mode'					=> $this->config->debug === true ? 1 : 0
			]
		);

		if ($application) {
			return $this->modules->applications->packagesData->last;
		} else {
			return false;
		}
	}

	protected function registerBareboneComponent(array $componentFile, $newApplicationId)
	{
		$component = $this->modules->components->add(
			[
				// 'id'					=> '',
				'route' 				=> $componentFile['route'],
				'name' 					=> $componentFile['name'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'path'					=> $componentFile['path'],
				'class'					=> $componentFile['class'],
				'repo'					=> $componentFile['repo'],
				'type'					=> $componentFile['type'],
				'settings'			 	=>
					isset($componentFile['settings']) ?
					json_encode($componentFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					json_encode($componentFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles)
			]
		);

		if ($component) {
			return $this->modules->components->packagesData->last;
		} else {
			return false;
		}
	}

	protected function registerBarebonePackage(array $packageFile, $newApplicationId)
	{
		$package = $this->modules->packages->add(
			[
				// 'id'					=> '',
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['displayName'],
				'description' 			=> $packageFile['description'],
				'version'				=> $packageFile['version'],
				'repo'					=> $packageFile['repo'],
				'path'					=> $packageFile['path'],
				'settings'			 	=>
					isset($packageFile['settings']) ?
					json_encode($packageFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					json_encode($packageFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles)
			]
		);

		if ($package) {
			return $this->modules->packages->packagesData->last;
		} else {
			return false;
		}
	}

	protected function registerBareboneMiddleware(array $middlewareFile, $newApplicationId)
	{
		$middleware = $this->modules->middlewares->add(
			[
				// 'id'					=> '',
				'name' 					=> $middlewareFile['name'],
				'display_name'			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'version'				=> $middlewareFile['version'],
				'repo'					=> $middlewareFile['repo'],
				'path'					=> $middlewareFile['path'],
				'class'					=> $middlewareFile['class'],
				'settings'			 	=>
					isset($middlewareFile['settings']) ?
					json_encode($middlewareFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($middlewareFile['dependencies']) ?
					json_encode($middlewareFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'sequence'				=> 0,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles),
				'enabled'				=> 1
			]
		);

		if ($middleware) {
			return $this->modules->middlewares->packagesData->last;
		} else {
			return false;
		}
	}

	protected function registerBareboneView(array $viewFile, $newApplicationId)
	{
		$view = $this->modules->views->add(
			[
				'id'					=> '',
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
				'files'					=> json_encode($this->installedFiles)
			]
		);

		if ($view) {
			return $this->modules->views->packagesData->last;
		} else {
			return false;
		}
	}
}