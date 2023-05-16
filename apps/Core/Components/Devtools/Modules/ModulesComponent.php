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
			} else {
				$modules[$modulesType]['childs'] = [];
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

				if ($type === 'components') {
					$this->view->menuBaseStructure = $this->basepackages->menus->getMenusForAppType($module['app_type']);
				}
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
			$this->view->moduleMenu = Json::encode([]);

			if ($this->getData()['id'] != 0) {
				if ($type !== 'core') {
					$module = [];

					$module['module_details'] = $this->modules->{$type}->getById($this->getData()['id']);

					if ($module['module_details']['module_type'] === 'components') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Components/';
						$this->view->moduleMenu = $module['module_details']['menu'];
					} else if ($module['module_details']['module_type'] === 'packages') {
						if ($module['module_details']['app_type'] === 'core' &&
							($module['module_details']['category'] === 'basepackages' ||
							 $module['module_details']['category'] === 'providers')
						) {
							if ($module['module_details']['category'] === 'basepackages') {
								$moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Basepackages/';
							} else if ($module['module_details']['category'] === 'providers') {
								$moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/';
							}
						} else {
							$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Packages/';
						}
					} else if ($module['module_details']['module_type'] === 'middlewares') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Middlewares/';
					} else if ($module['module_details']['module_type'] === 'views') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Views/';
					}

					if ($module['module_details']['module_type'] === 'packages' &&
						($module['module_details']['category'] === 'basepackages' ||
						 $module['module_details']['category'] === 'providers')
					) {
						$jsonFile =
							$moduleLocation .
							ucfirst($module['module_details']['name']) . '/' .
							substr($module['module_details']['module_type'], 0, -1) . '.json';
					} else {
						if ($module['module_details']['module_type'] === 'components') {
							$routeArr = explode('/', $module['module_details']['route']);

							foreach ($routeArr as &$path) {
								$path = ucfirst($path);
							}

							$routePath = implode('/', $routeArr) . '/Install/';
						} else if ($module['module_details']['module_type'] === 'middlewares') {
							$routePath = $module['module_details']['name'] . '/Install/';
						} else if ($module['module_details']['module_type'] === 'packages') {
							$pathArr = preg_split('/(?=[A-Z])/', $module['module_details']['name'], -1, PREG_SPLIT_NO_EMPTY);

							$routePath = implode('/', $pathArr) . '/Install/';
						} else if ($module['module_details']['module_type'] === 'views') {
							$routePath = $module['module_details']['name'] . '/';
						}

						$jsonFile =
							$moduleLocation .
							$routePath .
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
					$this->view->moduleSettings = $module['settings'] = Json::encode($module['settings']);
				}
				if (is_array($module['dependencies'])) {
					$this->view->moduleDependencies = $module['dependencies'] = Json::encode($module['dependencies']);
				}

				$this->view->module = $module;
			}
		} else {
			$this->view->pick('modules/list');
		}
	}

	public function addAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->modulesPackage->addModule($this->postData());

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->modulesPackage->updateModule($this->postData());

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	// public function removeAction()
	// {
	// 	if ($this->request->isPost()) {
	// 		if (!$this->checkCSRF()) {
	// 			return;
	// 		}

	// 		$this->modulesPackage->removeModule($this->postData());

	// 		$this->addResponse(
	// 			$this->modulesPackage->packagesData->responseMessage,
	// 			$this->modulesPackage->packagesData->responseCode
	// 		);
	// 	} else {
	// 		$this->addResponse('Method Not Allowed', 1);
	// 	}
	// }

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

			$this->basepackages->utils->formatJson($this->postData());

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
			}
		}

		return $modulesArr;
	}

	public function getAppTypeMenusAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if (isset($this->postData()['app_type'])) {
				$this->addResponse(
					'Menu structure for app_type generated', 0,
					$this->modules->manager->packagesData->responseData =
						[
							'menus_html' =>
								$this->generateTree(
									$this->basepackages->menus->getMenusForAppType(
										$this->postData()['app_type']
									)
								)
						]
				);
			} else {
				$this->addResponse('Please provide module type and module id', 1);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	private function generateTree($menusTree)
	{
		return $this->adminltetags->useTag(
			'tree',
			[
				'treeMode'      => 'jstree',
				'treeData'      => $menusTree,
				'groupIcon' 	=> '{"icon" : "fas fa-fw fa-circle-dot text-sm"}',
				'itemIcon' 		=> '{"icon" : "fas fa-fw fa-circle-dot text-sm"}'
			]
		);
	}
}
