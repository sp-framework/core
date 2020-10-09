<?php

namespace Packages\Admin\Modules;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Model\Applications;

class BarebonePackage extends BasePackage
{
	protected $postData;

	protected $applicationName;

	protected $applicationDescription;

	protected $defaultApplication;

	public function runProcess($postData)
	{
		$this->fileSystem = new Filesystem(new Local(base_path('/')));

		$this->postData = $postData;

		if ($this->postData['task'] === 'all') {

			$this->taskAll();
		} else if ($this->postData['task'] === 'component') {

			$this->taskComponent();
		} else if ($this->postData['task'] === 'package') {

			$this->taskPackage();
		} else if ($this->postData['task'] === 'middleware') {

			$this->taskMiddleware();
		} else if ($this->postData['task'] === 'view') {

			$this->taskView();
		}

		return $this->packagesData;
	}

	protected function taskAll()
	{
		if ($this->applications->getAll(['name' => ucfirst($this->postData['applicationName'])])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Application ' . ucfirst($this->postData['applicationName']) . ' already exists. Please choose another name.' ;

			return $this->packagesData;
		}

		if (!ctype_alpha($this->postData['applicationName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Application name cannot have spaces, special characters or numbers';

			return $this->packagesData;
		} else {

			if ($this->postData['default'] === 'true' &&
				$this->postData['force'] !== '1'
			   ) {

				if ($this->checkDefaultApplication()) {

					$this->packagesData->responseCode = 2;

					$this->packagesData->responseMessage =
						$this->packagesData->defaultApplication->get('name') .
						' application is already set to default. Make application ' .
						$this->postData['applicationName'] .
						' as default?';


					return $this->packagesData;
				}
			}

			if ($this->postData['default'] === 'true' ||
				$this->postData['force'] === '1'
				) {
				$this->removeApplicationDefaultFlag();
			}

			$this->applicationName =
				ucfirst(strtolower($this->postData['applicationName']));

			$this->componentName = 'Hw';

			$this->packageName = 'Hw';

			$this->middlewareName = 'Hw';

			$this->viewName = 'Default';

			$this->copyModuleStructure('applications');

			$this->modifyModuleFiles('applications');

			$newApplication = $this->registerModule('applications', null);

			if ($newApplication) {
				$this->copyModuleStructure('components');

				$this->modifyModuleFiles('components');

				$this->registerModule('components', $newApplication['id']);

				$this->copyModuleStructure('packages');

				$this->modifyModuleFiles('packages');

				$this->registerModule('packages', $newApplication['id']);

				$this->copyModuleStructure('middlewares');

				$this->modifyModuleFiles('middlewares');

				$this->registerModule('middlewares', $newApplication['id']);

				$this->copyModuleStructure('views');

				$this->modifyModuleFiles('views');

				$this->registerModule('views', $newApplication['id']);

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone application ' .
					$this->applicationName .
					' & its dependencies installed.';

				$this->packagesData->bareboneModule = $newApplication;

				return $this->packagesData;
			}
		}
	}

	protected function taskComponent()
	{
		if ($this->components->getAll(
					[
						'name' 				=> ucfirst($this->postData['componentName']),
						'application_id' 	=> $this->postData['application_id']
					]
				)
		   ) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Component ' . ucfirst($this->postData['componentName']) . ' already exists for this application.' .
				' Please choose another name.' ;

			return $this->packagesData;
		}

		if (!ctype_alpha($this->postData['componentName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Component name cannot have spaces, special characters or numbers';

			return $this->packagesData;
		} else {

			$this->applicationName =
				$this->applications->getAll(
					[
						'id' => $this->postData['application_id']
					]
				)[0]->get('name');

			$this->componentName =
				ucfirst(strtolower($this->postData['componentName']));

			$this->copyModuleStructure('components');

			$this->modifyModuleFiles('components');

			$newComponent =
				$this->registerModule('components', $this->postData['application_id']);

			if ($newComponent) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone component ' . $this->componentName . ' installed.';

				$this->packagesData->bareboneModule = $newComponent;

				return $this->packagesData;
			}
		}
	}

	protected function taskPackage()
	{
		if ($this->packages->getAll(
					[
						'name' 				=> ucfirst($this->postData['packageName']),
						'application_id' 	=> $this->postData['application_id']
					]
				)
		   ) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package ' . ucfirst($this->postData['packageName']) . ' already exists for this application.' .
				' Please choose another name.' ;

			return $this->packagesData;
		}

		if (!ctype_alpha($this->postData['packageName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package name cannot have spaces, special characters or numbers';

			return $this->packagesData;
		} else {

			$this->applicationName =
				$this->applications->getAll(
					[
						'id' => $this->postData['application_id']
					]
				)[0]->get('name');

			$this->packageName =
				ucfirst(strtolower($this->postData['packageName']));

			$this->copyModuleStructure('packages');

			$this->modifyModuleFiles('packages');

			$newPackage =
				$this->registerModule('packages', $this->postData['application_id']);

			if ($newPackage) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone package ' . $this->packageName . ' installed.';

				$this->packagesData->bareboneModule = $newPackage;

				return $this->packagesData;
			}
		}
	}

	protected function taskMiddleware()
	{
		if ($this->middlewares->getAll(
					[
						'name' 				=> ucfirst($this->postData['middlewareName']),
						'application_id' 	=> $this->postData['application_id']
					]
				)
		   ) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Middleware ' . ucfirst($this->postData['middlewareName']) . ' already exists for this application.' .
				' Please choose another name.' ;

			return $this->packagesData;
		}

		if (!ctype_alpha($this->postData['middlewareName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Middleware name cannot have spaces, special characters or numbers';

			return $this->packagesData;
		} else {

			$this->applicationName =
				$this->applications->getAll(
					[
						'id' => $this->postData['application_id']
					]
				)[0]->get('name');

			$this->middlewareName =
				ucfirst(strtolower($this->postData['middlewareName']));

			$this->copyModuleStructure('middlewares');

			$this->modifyModuleFiles('middlewares');

			$newMiddleware =
				$this->registerModule('middlewares', $this->postData['application_id']);

			if ($newMiddleware) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone middleware ' . $this->middlewareName . ' installed.';

				$this->packagesData->bareboneModule = $newMiddleware;

				return $this->packagesData;
			}
		}
	}

	protected function taskView()
	{
		if (isset($this->postData['viewName'])) {
			if ($this->views->getAll(
						[
							'name' 				=> ucfirst($this->postData['viewName']),
							'application_id' 	=> $this->postData['application_id']
						]
					)
			   ) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'View ' . ucfirst($this->postData['viewName']) . ' already exists for this application.' .
					' Please choose another name.' ;

				return $this->packagesData;
			}

			if (!ctype_alpha($this->postData['viewName'])) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'View name cannot have spaces, special characters or numbers';

				return $this->packagesData;
			} else {

				$this->applicationName =
					$this->applications->getAll(
						[
							'id' => $this->postData['application_id']
						]
					)[0]->get('name');


				$this->viewName =
						ucfirst(strtolower($this->postData['viewName']));

				$this->copyModuleStructure('views');

				$this->modifyModuleFiles('views');

				$newView =
					$this->registerModule('views', $this->postData['application_id']);

				if ($newView) {

					$this->packagesData->responseCode = 0;

					$this->packagesData->responseMessage =
						'Barebone view ' . $this->viewName . ' installed.';

					$this->packagesData->bareboneModule = $newView;

					return $this->packagesData;
				}
			}
		} else if (isset($this->postData['view_id']) &&
				   isset($this->postData['component_id'])
				  ) {
			$applicationName =
					$this->applications->getAll(
						[
							'id' => $this->postData['application_id']
						]
					)[0]->get('name');

			$viewName =
				$this->views->getAll(
						[
							'id' => $this->postData['view_id']
						]
					)[0]->get('name');

			$componentName =
				strtolower($this->components->getAll(
						[
							'id' => $this->postData['component_id']
						]
					)[0]->get('name'));

			if (!$this->fileSystem
					  ->has(
						'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName
					  )
			   ) {
				$this->fileSystem
					 ->createDir(
						'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName
					 );
			}

			if (!$this->fileSystem
					  ->has(
						'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName . '/view.html'
					  ) ||
				(isset($this->postData['force']) && $this->postData['force'] === '1')
			   ) {
				$this->fileSystem
					 ->put(
						'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName . '/view.html',
						$applicationName . ' ' . $viewName . ' ' . $componentName . ' view'
					 );
			} else {
				$this->packagesData->responseCode = 2;

				$this->packagesData->responseMessage =
					'view.html file already exisit at location ' .
					'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName .
					'. Overwrite?';

				return $this->packagesData;
			}

			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage =
				'New view.html file at location ' .
				'views/' . $applicationName . '/' . $viewName . '/html/' . $componentName .
				' added.';

			$this->packagesData->bareboneModule = [];

			return $this->packagesData;
		}

	}
	protected function copyModuleStructure($type)
	{
		$typeContents =
			$this->fileSystem->listContents(
				'packages/Admin/Modules/Barebone/' . $type . '/',
				true
			);

		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		foreach ($typeContents as $contentKey => $typeContent) {
			if ($this->postData['task'] === 'component') {
				if (strpos($typeContent['basename'], 'Hw') !== false) {
					$typeContent['basename'] =
						str_replace('Hw', $this->componentName, $typeContent['basename']);
				}
				if (strpos($typeContent['dirname'], 'Hw') !== false) {
					$typeContent['dirname'] =
						str_replace('Hw', $this->componentName, $typeContent['dirname']);
				}
			}

			if ($this->postData['task'] === 'package') {
				if (strpos($typeContent['basename'], 'Hw') !== false) {
					$typeContent['basename'] =
						str_replace('Hw', $this->packageName, $typeContent['basename']);
				}
				if (strpos($typeContent['dirname'], 'Hw') !== false) {
					$typeContent['dirname'] =
						str_replace('Hw', $this->packageName, $typeContent['dirname']);
				}
			}

			if ($this->postData['task'] === 'middleware') {
				if (strpos($typeContent['basename'], 'Hw') !== false) {
					$typeContent['basename'] =
						str_replace('Hw', $this->middlewareName, $typeContent['basename']);
				}
				if (strpos($typeContent['dirname'], 'Hw') !== false) {
					$typeContent['dirname'] =
						str_replace('Hw', $this->middlewareName, $typeContent['dirname']);
				}
			}

			if ($this->postData['task'] === 'view') {
				if (strpos($typeContent['dirname'], 'Default') !== false) {
					$typeContent['dirname'] =
						str_replace('Default', $this->viewName, $typeContent['dirname']);
				}
			}

			$destDir =
				str_replace('Barebone', $this->applicationName,
					str_replace(
						'packages/Admin/Modules/Barebone/' . $type,
						$type,
						$typeContent['dirname']
					)
				);

			if ($typeContent['basename'] === 'Barebone') {
				$typeContent['basename'] = $this->applicationName;
			}

			// var_dump($typeContent['basename'], $destDir);

			if ($typeContent['type'] === 'dir') {

				$this->fileSystem->createDir($destDir . '/' . $typeContent['basename']);

				array_push($installedFiles['dir'], $destDir . '/' . $typeContent['basename']);
			} else if ($typeContent['type'] === 'file') {

				$this->fileSystem->copy($typeContent['path'], $destDir . '/' . $typeContent['basename']);

				array_push($installedFiles['files'], $destDir . '/' . $typeContent['basename']);
			}
		}

		$this->installedFiles = $installedFiles;

		// if ($type === 'components' || $type === 'packages' || $type === 'middlewares') {
		// 	if ($type === 'components') {
		// 		$this->fileSystem->put(
		// 			$type . '/'. $this->applicationName . '/Install/' . $this->componentName . '/files.info', json_encode($installedFiles)
		// 		);
		// 	} else if ($type === 'packages') {
		// 		$this->fileSystem->put(
		// 			$type . '/'. $this->applicationName . '/Install/' . $this->packageName . '/files.info', json_encode($installedFiles)
		// 		);
		// 	} else if ($type === 'middlewares') {
		// 		$this->fileSystem->put(
		// 			$type . '/'. $this->applicationName . '/Install/' . $this->middlewareName . '/files.info', json_encode($installedFiles)
		// 		);
		// 	}
		// } else {
		// 	$this->fileSystem->put($type . '/'. $this->applicationName . '/files.info', json_encode($installedFiles));
		// }
	}

	protected function modifyModuleFiles($type)
	{
		if ($type === 'applications') {
			$applicationFile = $this->fileSystem->read('applications/' . $this->applicationName . '/application.json');

			if ($applicationFile) {
				$applicationFile = str_replace('Barebone', $this->applicationName, $applicationFile);
				$this->fileSystem->put('applications/' . $this->applicationName . '/application.json', $applicationFile);
			} else {
				//
			}
		} else if ($type === 'components') {
			$componentFile = $this->fileSystem->read('components/' . $this->applicationName . '/' . $this->componentName . '.php');

			if ($componentFile) {
				$componentFile = str_replace('Barebone', $this->applicationName, $componentFile);

				if ($this->postData['task'] === 'component') {
					$componentFile = str_replace('Hw', $this->componentName, $componentFile);
				}

				$this->fileSystem->put('components/' . $this->applicationName . '/' . $this->componentName . '.php', $componentFile);
			} else {
				//Error
			}

			$componentFile =
				$this->fileSystem->read('components/' . $this->applicationName . '/Install/' . $this->componentName . '/component.json');

			if ($componentFile) {

				$componentFile = str_replace('Barebone', $this->applicationName, $componentFile);

				if ($this->postData['task'] === 'component') {
					$componentFile = str_replace('Hw', $this->componentName, $componentFile);
					$componentFile = str_replace('Hello World', $this->componentName, $componentFile);
				}

				$this->fileSystem
					->put('components/' . $this->applicationName . '/Install/' . $this->componentName . '/component.json', $componentFile);

			} else {
				//Error
			}

		} else if ($type === 'packages') {
			$packageFile = $this->fileSystem->read('packages/' . $this->applicationName . '/' . $this->packageName . '.php');

			if ($packageFile) {
				$packageFile = str_replace('Barebone', $this->applicationName, $packageFile);

				if ($this->postData['task'] === 'package') {
					$packageFile = str_replace('Hw', $this->packageName, $packageFile);
				}

				$this->fileSystem->put('packages/' . $this->applicationName . '/' . $this->packageName . '.php', $packageFile);
			} else {
				//Error
			}

			$packageFile =
				$this->fileSystem->read('packages/' . $this->applicationName . '/Install/' . $this->packageName . '/package.json');

			if ($packageFile) {

				$packageFile = str_replace('Barebone', $this->applicationName, $packageFile);

				if ($this->postData['task'] === 'package') {
					$packageFile = str_replace('Hw', $this->packageName, $packageFile);
					$packageFile = str_replace('Hello World', $this->packageName, $packageFile);
				}

				$this->fileSystem
					->put('packages/' . $this->applicationName . '/Install/' . $this->packageName . '/package.json', $packageFile);

			} else {
				//Error
			}

		} else if ($type === 'middlewares') {
			$middlewareFile = $this->fileSystem->read('middlewares/' . $this->applicationName . '/' . $this->middlewareName . '.php');

			if ($middlewareFile) {
				$middlewareFile = str_replace('Barebone', $this->applicationName, $middlewareFile);

				if ($this->postData['task'] === 'middleware') {
					$middlewareFile = str_replace('Hw', $this->middlewareName, $middlewareFile);
				}

				$this->fileSystem->put('middlewares/' . $this->applicationName . '/' . $this->middlewareName . '.php', $middlewareFile);
			} else {
				//Error
			}

			$middlewareFile =
				$this->fileSystem->read('middlewares/' . $this->applicationName . '/Install/' . $this->middlewareName . '/middleware.json');

			if ($middlewareFile) {

				$middlewareFile = str_replace('Barebone', $this->applicationName, $middlewareFile);

				if ($this->postData['task'] === 'middleware') {
					$middlewareFile = str_replace('Hw', $this->middlewareName, $middlewareFile);
					$middlewareFile = str_replace('Hello World', $this->middlewareName, $middlewareFile);
				}

				$this->fileSystem
					->put('middlewares/' . $this->applicationName . '/Install/' . $this->middlewareName . '/middleware.json', $middlewareFile);

			} else {
				//Error
			}

		} else if ($type === 'views') {
			$viewFile = $this->fileSystem->read('views/' . $this->applicationName . '/' . $this->viewName . '/view.json');

			if ($viewFile) {
				$viewFile = str_replace('Barebone', $this->applicationName, $viewFile);

				if ($this->postData['task'] === 'view') {
					$viewFile = str_replace('Default', $this->viewName, $viewFile);
				}

				$this->fileSystem->put('views/' . $this->applicationName . '/' . $this->viewName . '/view.json', $viewFile);
			} else {
				//Error
			}
		}
	}

	protected function registerModule($type, $newApplicationId)
	{
		if ($type === 'applications') {

			return
				$this->registerBareboneApplication(
					json_decode(
						$this->fileSystem->read(
							'applications/' . $this->applicationName .
							'/application.json'
						), true
					)
				)->getAllArr();

		} else if ($type === 'components') {

			return
				$this->registerBareboneComponent(
					json_decode(
						$this->fileSystem->read(
							'components/' . $this->applicationName .
							'/Install/' . $this->componentName . '/component.json'
						), true
					),
					$newApplicationId
				);

		} else if ($type === 'packages') {

			return $this->registerBarebonePackage(
				json_decode(
					$this->fileSystem->read(
						'packages/' . $this->applicationName .
						'/Install/' . $this->packageName . '/package.json'
					), true
				),
				$newApplicationId
			);

		} else if ($type === 'middlewares') {

			return $this->registerBareboneMiddleware(
				json_decode(
					$this->fileSystem->read(
						'middlewares/' . $this->applicationName .
						'/Install/' . $this->middlewareName . '/middleware.json'
					), true
				),
				$newApplicationId
			);

		} else if ($type === 'views') {

			return $this->registerBareboneView(
				json_decode(
					$this->fileSystem->read(
						'views/' . $this->applicationName
						. '/' . $this->viewName . '/view.json'
					), true
				),
				$newApplicationId
			);
		}
	}

	protected function registerBareboneApplication(array $applicationFile)
	{
		return $this->applications->register(
			[
				'id'					=> '',
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
				'is_default'			=> $this->postData['default'] === 'true' ? 1 :0,
				'installed'				=> 1,
				'files'					=> json_encode($this->installedFiles),
				'mode'					=> $this->mode === 'true' ? 1 : 0
			]
		);
	}

	protected function registerBareboneComponent(array $componentFile, $newApplicationId)
	{
		return $this->components->register(
			[
				'id'					=> '',
				'name' 					=> $componentFile['name'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'path'					=> $componentFile['path'],
				'repo'					=> $componentFile['repo'],
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
	}

	protected function registerBarebonePackage(array $packageFile, $newApplicationId)
	{
		return $this->packages->register(
			[
				'id'					=> '',
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
	}

	protected function registerBareboneMiddleware(array $middlewareFile, $newApplicationId)
	{
		return $this->middlewares->register(
			[
				'id'					=> '',
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
	}

	protected function registerBareboneView(array $viewFile, $newApplicationId)
	{
		return $this->views->register(
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
	}

	protected function removeApplicationDefaultFlag()
	{
		$defaultApplication = $this->getDefaultApplication();

		if (count($defaultApplication) > 0) {
			$defaultApplication =
				$defaultApplication[0]->getAllArr();

			$defaultApplication['is_default'] = 0;

			$this->applications->update($defaultApplication);
		}
	}

	protected function checkDefaultApplication()
	{
		$defaultApplication = $this->getDefaultApplication();

		if (count($defaultApplication) > 0) {

			$this->packagesData->defaultApplication = $defaultApplication[0];

			return true;
		} else {
			return false;
		}
	}

	protected function getDefaultApplication()
	{
		if (!$this->defaultApplication) {

			$this->defaultApplication = $this->applications->getAll(["is_default" => 1]);

			return $this->defaultApplication;
		} else {

			return $this->defaultApplication;
		}
	}

	public function getApplicationComponentsViews($postData)
	{
		$this->packagesData->applicationComponents =
			['components' => getAllArr($this->components->getAll(["application_id" => $postData["application_id"]]))];

		$this->packagesData->applicationViews =
			['views' => getAllArr($this->views->getAll(["application_id" => $postData["application_id"]]))];

		if ($this->packagesData->applicationComponents && $this->packagesData->applicationViews) {
			$this->packagesData->responseCode = 0;
		}

		return $this->packagesData;
	}
}