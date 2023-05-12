<?php

namespace Apps\Core\Components\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\DevtoolsModules;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	protected $modulesPackage;

	public function initialize()
	{
		$this->modulesPackage = $this->usePackage(DevtoolsModules::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['includecoremodules'])) {
			$this->view->includecoremodules = true;
		}

		$appTypesArr = $this->apps->types->types;
		$appTypes = [];

		foreach ($appTypesArr as $key => $value) {
			$appTypes[$value['app_type']]['id'] = $value['app_type'];
			$appTypes[$value['app_type']]['name'] = $value['name'];
		}

		$this->view->appTypes = $appTypes;

		$modules = [];

		try {
			$module = $this->modulesPackage->validateJson(
				[
					'json' => $this->localContent->read('system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json'),
					'returnJson' => 'array'
				]
			);

			$module['module_details'] = $this->modules->packages->getPackageByName('core');
			$module['id'] = $module['module_details']['id'];
		} catch (\throwable $e) {
			throw new \Exception($e->getMessage());
		}

		$modules['core']['value'] = 'Core';
		$modules['core']['childs'][1] = $module;

		$modulesTypeArr = ['components', 'packages', 'middlewares', 'views'];

		foreach ($modulesTypeArr as $modulesType) {
			$modulesArr = $this->processModulesArr(msort($this->modules->{$modulesType}->{$modulesType}, 'name'));
			${$modulesType . 'CategoryArr'} = $modulesArr['categoryArr'];
			if (count($modulesArr['modules']) > 0) {
				$modules[$modulesType]['value'] = ucfirst($modulesType);
				$modules[$modulesType]['childs'] = $modulesArr['modules'];
			}
		}

		$this->view->modules = $modules;

		$modulesJson = [];

		foreach ($modules as $moduleKey => $moduleJson) {
			foreach ($moduleJson['childs'] as $childKey => $child) {
				$modulesJson[$moduleKey][$child['id']] =
					[
						'name' 		=> $child['name'],
						'version' 	=> $child['version'],
						'repo' 		=> $child['repo'],
					];
			}
		}

		$this->view->modulesJson = Json::encode($modulesJson);

		if (isset($this->getData()['id']) &&
			isset($this->getData()['module']) &&
			isset($this->getData()['type'])
		) {
			$type = strtolower($this->getData()['type']);

			if ($type !== 'core') {
				$this->view->categoryArr = ${$type . 'CategoryArr'};
			} else {
				$this->view->categoryArr = ['core' => ['id' => 'providers', 'name' => 'Providers']];
			}

			$this->view->type = $type;
			$this->view->module = null;

			$apisArr = $this->basepackages->api->init()->getAll()->api;
			if (count($apisArr) > 0) {
				$apis[0]['id'] = 0;
				$apis[0]['name'] = 'Local Modules';
				$apis[0]['data']['url'] = 'https://.../';

				foreach ($apisArr as $api) {
					if ($api['category'] === 'repos') {
						$useApi = $this->basepackages->api->useApi($api['id'], true);
						$apiConfig = $useApi->getApiConfig();

						$apis[$api['id']]['id'] = $apiConfig['id'];
						$apis[$api['id']]['name'] = $apiConfig['name'];
						$apis[$api['id']]['data']['url'] = $apiConfig['repo_url'];
					}
				}
			}

			$this->view->apis = $apis;
			$this->view->moduleTypes = $this->modulesPackage->getModuleTypes();
			$this->view->moduleSettings = $this->modulesPackage->getDefaultSettings($type);
			$this->view->moduleDependencies = $this->modulesPackage->getDefaultDependencies();

			if ($this->getData()['id'] != 0) {
				if ($type !== 'core') {
					$module = [];

					$module['module_details'] = $this->modules->{$type}->getById($this->getData()['id']);

					if ($module['module_details']['module_type'] === 'components') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Components/';
					} else if ($module['module_details']['module_type'] === 'packages') {
						if ($module['module_details']['app_type'] === 'core' &&
							$module['module_details']['category'] === 'basepackages'
						) {
							$moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/';
						} else {
							$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Packages/';
						}
					} else if ($module['module_details']['module_type'] === 'middlewares') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Middlewares/';
					} else if ($module['module_details']['module_type'] === 'views') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Views/';
					}

					if ($module['module_details']['module_type'] === 'packages' &&
						$module['module_details']['category'] === 'basepackages'
					) {
						$jsonFile =
							$moduleLocation .
							ucfirst($module['module_details']['category']) . '/' .
							ucfirst($module['module_details']['name']) . '/' .
							substr($module['module_details']['module_type'], 0, -1) . '.json';
					} else {
						if ($module['module_details']['module_type'] === 'components') {
							$routeArr = explode('/', $module['module_details']['route']);

							foreach ($routeArr as &$path) {
								$path = ucfirst($path);
							}

							$routePath = implode('/', $routeArr);
						} else {
							$pathArr = preg_split('/(?=[A-Z])/', $module['module_details']['name'], -1, PREG_SPLIT_NO_EMPTY);

							$routePath = implode('/', $pathArr);
						}

						$jsonFile =
							$moduleLocation .
							$routePath . '/' .
							'Install/' .
							substr($module['module_details']['module_type'], 0, -1) . '.json';
					}
					try {
						$module = array_merge($module, $this->modulesPackage->validateJson(
							[
								'json' 			=> $this->localContent->read($jsonFile),
								'returnJson' 	=> 'array'
							]
						));

						$module['id'] = $module['module_details']['id'];
					} catch (\throwable $e) {
						throw new \Exception($e->getMessage());
					}
				}

				if (is_array($module['settings'])) {
					$this->view->moduleSettings = Json::encode($module['settings']);
					$module['settings'] = $this->modulesPackage->formatJson(['json' => $module['settings']]);
				}
				if (is_array($module['dependencies'])) {
					$this->view->moduleDependencies = Json::encode($module['dependencies']);
					$module['dependencies'] = $this->modulesPackage->formatJson(['json' => $module['dependencies']]);
				}

				$this->view->module = $module;

				// $coreJson['module_details'] = $this->modules->packages->getPackageByName('core');

				// if ($type === 'core') {
				// } else if ($type === 'components') {
				// 	$component = $this->modules->components->getById($this->getData()['id']);

				// 	$component['dependencies'] = Json::decode($component['dependencies'], true);
				// 	$component['dependencies'] = Json::encode($component['dependencies'], JSON_UNESCAPED_SLASHES);

				// 	$this->view->module = $component;
				// } else if ($type === 'packages') {
				// 	$package = $this->modules->packages->getById($this->getData()['id']);

				// 	$this->view->module = $package;

				// } else if ($type === 'middlewares') {
				// 	$middleware = $this->modules->middlewares->getById($this->getData()['id']);

				// 	$this->view->module = $middleware;
				// } else if ($type === 'views') {
				// 	$view = $this->modules->views->getById($this->getData()['id']);

				// 	$view['dependencies'] = Json::decode($view['dependencies'], true);
				// 	$view['dependencies'] = Json::encode($view['dependencies'], JSON_UNESCAPED_SLASHES);

				// 	$this->view->module = $view;
				// }
			}
		} else {
			$this->view->pick('modules/list');
		}
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}
			$this->modulesPackage->updateModules($this->postData());

			$this->addResponse($this->modulesPackage->packagesData->responseMessage, $this->modulesPackage->packagesData->responseCode);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function validateJsonAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}
			$this->modulesPackage->updateModules($this->postData());

			$this->addResponse($this->modulesPackage->packagesData->responseMessage, $this->modulesPackage->packagesData->responseCode);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function formatJsonAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->modulesPackage->formatJson($this->postData());


			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode,
				$this->modulesPackage->packagesData->responseData
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	protected function processModulesArr($modulesArr)
	{
		$modulesArr['modules'] = $modulesArr;
		$modulesArr['categoryArr'] = [];

		foreach ($modulesArr['modules'] as $key => &$module) {
			if (!isset($modulesArr['categoryArr'][$module['category']])) {
				$modulesArr['categoryArr'][$module['category']]['id'] = $module['category'];
				$modulesArr['categoryArr'][$module['category']]['name'] = ucfirst($module['category']);
			}

			if (!isset($this->getData()['includecoremodules'])) {
				if ($module['app_type'] === 'core') {
					unset($modulesArr['modules'][$key]);
				}
			} else {
				$module['name'] = ucfirst($module['app_type']) . ' : ' . $module['name'];
			}
		}

		return $modulesArr;
	}
}
