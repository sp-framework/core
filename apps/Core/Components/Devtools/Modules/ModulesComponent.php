<?php

namespace Apps\Core\Components\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\DevtoolsModules;

use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	protected $modulesPackage;

	public function initialize()
	{
		$this->modulesPackage = $this->usePackage(DevtoolsModules::class, true);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		$this->checkSettingsRoute();

		if (isset($this->getData()['bundles'])) {
			$this->view->bundles = true;

			if (isset($this->getData()['bundlesjson'])) {
				$this->view->bundlesjson = true;
			}
		}

		if (isset($this->getData()['includecoremodules'])) {
			$this->view->includecoremodules = true;
		}

		if (isset($this->getData()['clone'])) {
			$this->view->clone = true;
		}

		if (isset($this->getData()['newrelease'])) {
			$this->view->newrelease = true;
		}

		if (isset($this->getData()['subview'])) {
			$this->view->subview = true;
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
			$module = $this->basepackages->utils->validateJson(
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

		$modulesTypeArr = ['components', 'packages', 'middlewares', 'views', 'bundles'];

		foreach ($modulesTypeArr as $modulesType) {
			if ($modulesType === 'bundles') {
				$modulesArr['modules'] = msort($this->modules->{$modulesType}->{$modulesType}, 'name');
			} else {
				$modulesArr = $this->processModulesArr(msort($this->modules->{$modulesType}->{$modulesType}, 'name'));
				${$modulesType . 'CategoryArr'} = $modulesArr['categoryArr'];
			}

			if ($modulesArr['modules'] && count($modulesArr['modules']) > 0) {
				$modules[$modulesType]['value'] = ucfirst($modulesType);
				$modules[$modulesType]['childs'] = $modulesArr['modules'];
			} else {
				$modules[$modulesType]['childs'] = [];
			}
		}

		$this->view->modules = $modules;

		$modulesJson = [];

		foreach ($modules as $moduleKey => $moduleJson) {
			if ($moduleKey === 'bundles') {
				continue;
			}

			foreach ($moduleJson['childs'] as $childKey => $child) {
				$modulesJson[$moduleKey][$child['id']] =
					[
						'id' 		=> $child['id'],
						'name' 		=> $child['name'],
						'version' 	=> $child['version'],
						'repo' 		=> $child['repo'],
					];

				if ($moduleKey === 'views') {
					$modulesJson[$moduleKey][$child['id']] =
						array_merge($modulesJson[$moduleKey][$child['id']],
							[
								'base_view_module_id' => $child['base_view_module_id']
							]
						);
				}
			}
		}

		$this->view->modulesJson = $this->helper->encode($modulesJson);

		$apisArr = $this->basepackages->apiClientServices->init()->getAll()->apiClientServices;
		if (count($apisArr) > 0) {
			$apis[0]['id'] = 0;
			$apis[0]['name'] = 'Local Modules';
			$apis[0]['data']['url'] = 'https://.../';

			foreach ($apisArr as $api) {
				if ($api['category'] === 'repos') {
					$useApi = $this->basepackages->apiClientServices->useApi($api['id'], true);
					$apiConfig = $useApi->getApiConfig();

					$apis[$api['id']]['id'] = $apiConfig['id'];
					$apis[$api['id']]['name'] = $apiConfig['name'];
					$apis[$api['id']]['data']['url'] = $apiConfig['repo_url'];
				}
			}
		}

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

			$this->view->apis = $apis;
			$this->view->moduleTypes = $this->modulesPackage->getModuleTypes();
			$this->view->moduleSettings = $this->modulesPackage->getDefaultSettings();
			$this->view->moduleDependencies = $this->modulesPackage->getDefaultDependencies($type);
			$this->view->moduleMenu = $this->helper->encode([]);
			$this->view->moduleWidgets = $this->helper->encode([]);

			if ($this->getData()['id'] != 0) {
				if ($type !== 'core') {
					$module = [];

					$module['module_details'] = $this->modules->{$type}->getById($this->getData()['id']);

					if ($module['module_details']['module_type'] === 'components') {
						$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Components/';
						if ($module['module_details']['menu']) {
							$this->view->moduleMenu = $this->helper->encode($this->helper->decode($module['module_details']['menu'], true));
							$this->view->menuBaseStructure = $this->basepackages->menus->getMenusForAppType($module['module_details']['app_type']);
						} else {
							$this->view->moduleMenu = false;
							$this->view->menuBaseStructure = [];
						}
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
							if (!$module['module_details']['view_modules_version'] ||
								($module['module_details']['base_view_module_id'] && $module['module_details']['base_view_module_id'] != '0')
							) {
								$baseView = $this->modules->views->getViewById($module['module_details']['base_view_module_id']);

								$pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['module_details']['name']), -1, PREG_SPLIT_NO_EMPTY);

								if (count($pathArr) > 1) {
									foreach ($pathArr as &$path) {
										$path = strtolower($path);
									}
								} else {
									$pathArr[0] = strtolower($pathArr[0]);
								}

								$module['route'] = implode('/', $pathArr);

								$routePath = $baseView['name'] . '/html/' . $module['route'] . '/';
							} else {
								$routePath = $module['module_details']['name'] . '/';
							}
						}

						$jsonFile =
							$moduleLocation .
							$routePath .
							substr($module['module_details']['module_type'], 0, -1) . '.json';
					}

					try {
						$module = array_merge($module, $this->basepackages->utils->validateJson(
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

				if (isset($module['widgets']) && is_array($module['widgets'])) {
					$this->view->moduleWidgets = $module['widgets'] = $this->helper->encode($module['widgets']);
				} else {
					$this->view->moduleWidgets = $module['widgets'] = $this->helper->encode([]);
				}
				if (is_array($module['settings'])) {
					$this->view->moduleSettings = $module['settings'] = $this->helper->encode($module['settings']);
				}
				if (is_array($module['dependencies'])) {
					$this->view->moduleDependencies = $module['dependencies'] = $this->helper->encode($module['dependencies']);
				}

				$this->view->module = $module;
			}
		} else if (isset($this->getData()['id']) &&
				   isset($this->getData()['bundles'])
		) {
			$this->view->type = 'bundles';
			unset($apis[0]);//Remove local
			unset($apis[1]);//Remove core
			$this->view->apis = $apis;
			unset($appTypes['core']);//Remove core
			$this->view->appTypes = $appTypes;
			$this->view->bundleModules = $this->modulesPackage->getDefaultDependencies();

			if ($this->getData()['id'] != 0) {
				$bundle = $this->modules->bundles->getById($this->getData()['id']);

				if (!$bundle) {
					return $this->throwIdNotFound();
				}

				$this->view->bundle = $bundle;
				$this->view->bundleModules = $bundle['bundle_modules'];
			}

			if (isset($modules['bundles'])) {
				unset($modules['bundles']);
			}

			$this->view->modules = $modules;

			$this->view->pick('modules/view');
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
				$this->modulesPackage->packagesData->responseCode,
				$this->modulesPackage->packagesData->responseData
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

	// public function validateJsonAction()
	// {
	// 	if ($this->request->isPost()) {
	// 		if (!$this->checkCSRF()) {
	// 			return;
	// 		}
	// 		$this->modulesPackage->updateModules($this->postData());

	// 		$this->addResponse($this->modulesPackage->packagesData->responseMessage, $this->modulesPackage->packagesData->responseCode);
	// 	} else {
	// 		$this->addResponse('Method Not Allowed', 1);
	// 	}
	// }

	public function formatJsonAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->basepackages->utils->formatJson($this->postData());

			$this->addResponse(
				$this->basepackages->utils->packagesData->responseMessage,
				$this->basepackages->utils->packagesData->responseCode,
				$this->basepackages->utils->packagesData->responseData
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

	public function syncLabelsAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->syncLabels($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function getLabelIssuesAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->getLabelIssues($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function bumpVersionAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->bumpVersion($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function syncBranchesAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->syncBranches($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function generateReleaseAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->generateRelease($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function publishBundleJsonAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->modulesPackage->publishBundleJson($this->postData())) {
				$this->addResponse(
					$this->modulesPackage->packagesData->responseMessage,
					$this->modulesPackage->packagesData->responseCode,
					$this->modulesPackage->packagesData->responseData
				);

				return;
			}

			$this->addResponse(
				$this->modulesPackage->packagesData->responseMessage,
				$this->modulesPackage->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}
}