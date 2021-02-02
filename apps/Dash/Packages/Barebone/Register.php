<?php

namespace Apps\Ecom\Admin\Packages\Barebone;

use System\Base\BasePackage;

class Register extends BasePackage
{
	protected $installedFiles;

	protected $postData;

	public function registerModule($type, $newAppId, $installedFiles, $names, $postData)
	{
		$this->installedFiles = $installedFiles;

		$this->postData = $postData;

		if ($type === 'apps') {

			$file = 'apps/' . $names['appName'] . '/app.json';

			return
				$this->registerBareboneApp(
					json_decode(
						$this->localContent->read($file),
						true
					)
				);

		} else if ($type === 'components') {

			$file =
				'apps/' . $names['appName'] .
				'/Components/' . $names['componentName'] . '/Install/component.json';

			return
				$this->registerBareboneComponent(
					json_decode(
						$this->localContent->read($file), true
					),
					$newAppId
				);

		} else if ($type === 'packages') {

			$file =
				'apps/' . $names['appName'] .
				'/Packages/' . $names['packageName'] . '/Install/package.json';

			return $this->registerBarebonePackage(
				json_decode(
					$this->localContent->read($file), true
				),
				$newAppId
			);

		} else if ($type === 'middlewares') {

			$file =
				'apps/' . $names['appName'] .
				'/Middlewares/' . $names['middlewareName'] . '/Install/middleware.json';

			return $this->registerBareboneMiddleware(
				json_decode(
					$this->localContent->read($file), true
				),
				$newAppId
			);

		} else if ($type === 'views') {

			$file = 'apps/' . $names['appName'] . '/Views/' . $names['viewName'] . '/view.json';

			return $this->registerBareboneView(
				json_decode(
					$this->localContent->read($file), true
				),
				$newAppId
			);
		}
	}

	protected function registerBareboneApp(array $appFile)
	{
		$app = $this->apps->add(
			[
				'route' 				=> $appFile['route'],
				'name' 					=> $appFile['name'],
				'display_name' 			=> $appFile['displayName'],
				'description' 			=> $appFile['description'],
				'version'				=> $appFile['version'],
				'repo'					=> $appFile['repo'],
				'settings'			 	=>
					isset($appFile['settings']) ?
					json_encode($appFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($appFile['dependencies']) ?
					json_encode($appFile['dependencies']) :
					null,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles),
				'mode'					=> $this->config->debug === true ? 1 : 0
			]
		);

		if ($app) {
			return $this->apps->packagesData->last;
		} else {
			return false;
		}
	}

	protected function registerBareboneComponent(array $componentFile, $newAppId)
	{
		$component = $this->modules->components->add(
			[
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
				'app_id'		=> $newAppId,
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

	protected function registerBarebonePackage(array $packageFile, $newAppId)
	{
		$package = $this->modules->packages->add(
			[
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
				'app_id'		=> $newAppId,
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

	protected function registerBareboneMiddleware(array $middlewareFile, $newAppId)
	{
		$middleware = $this->modules->middlewares->add(
			[
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
				'app_id'		=> $newAppId,
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

	protected function registerBareboneView(array $viewFile, $newAppId)
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
				'app_id'		=> $newAppId,
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