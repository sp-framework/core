<?php

namespace Apps\Core\Components\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\DevtoolsModules;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToListContents;
use League\Flysystem\UnableToRetrieveMetadata;
use System\Base\BaseComponent;
use z4kn4fein\SemVer\Version;

class ModulesComponent extends BaseComponent
{
	protected $modulesPackage;

	public function initialize()
	{
		$this->modulesPackage = $this->usePackage(DevtoolsModules::class);

		$this->setModuleSettings(true);

		$this->setModuleSettingsData([
				'apis' => $this->modulesPackage->getAvailableApis(true, false),
				'apiClients' => $this->modulesPackage->getAvailableApis(false, false)
			]
		);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['repo']) &&
			isset($this->getData()['id'])
		) {
			$this->modulesPackage->getRemoteModules($this->getData()['id']);

			$this->view->remoteModules = $this->modulesPackage->packagesData->responseData['remoteModules'] ?? [];
			$this->view->apiId = $this->modulesPackage->packagesData->responseData['api_id'] ?? 0;

			$this->view->pick('modules/repo');

			return;
		}

		$this->view->bundles = false;
		$this->view->bundlesjson = false;
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

		$modulesTypeArr = ['apptypes', 'components', 'packages', 'middlewares', 'views', 'bundles'];

		foreach ($modulesTypeArr as $modulesType) {
			if ($modulesType === 'bundles') {
				$modulesArr['modules'] = msort($this->modules->{$modulesType}->{$modulesType}, 'name');
			} else if ($modulesType === 'apptypes') {
				$modulesArr['modules'] = msort($this->apps->types->types, 'name');
				foreach ($modulesArr['modules'] as $typesModuleKey => $typesModule) {
					$modulesArr['modules'][$typesModuleKey]['data']['app_type'] = $typesModule['app_type'];

					if (!isset($this->getData()['includecoremodules'])) {
						if ($typesModule['app_type'] === 'core') {
							unset($modulesArr['modules'][$typesModuleKey]);
						}
					}
				}
			} else {
				$modulesArr = $this->processModulesArr(msort($this->modules->{$modulesType}->{$modulesType}, 'name'));
				${$modulesType . 'CategoryArr'} = $modulesArr['categoryArr'];
			}

			if ($modulesArr['modules'] && count($modulesArr['modules']) > 0) {
				if ($modulesType === 'views') {
					if (isset($this->view->subview)) {
						foreach ($modulesArr['modules'] as $moduleArrKey => $moduleArr) {
							if (array_key_exists('is_subview', $moduleArr) &&
								$moduleArr['is_subview'] == true
							) {
								continue;
							}
							$modules[$modulesType]['value'] = ucfirst($modulesType);
							$modules[$modulesType]['childs'][$moduleArrKey] = $moduleArr;
						}
					} else if (!isset($this->view->subview)) {
						$modules[$modulesType]['value'] = ucfirst($modulesType);
						$modules[$modulesType]['childs'] = $modulesArr['modules'];
					}
				} else {
					$modules[$modulesType]['value'] = ucfirst($modulesType);
					$modules[$modulesType]['childs'] = $modulesArr['modules'];
				}
			} else {
				$modules[$modulesType]['childs'] = [];
			}
		}

		if (isset($this->getData()['type'])) {
			// For all - core, apptype
			// For components - packages, middlewares, views (only subview), externals
			// For packages - middlewares, externals
			// For middlewares - packages, externals
			// For views (baseview) - packages (for any tag packages like adminltetags)
			// For views (sub) - views (only baseview)
			// For bundles - components, packages, middlewares, views, bundles, externals
			if ($this->getData()['type'] === 'components') {
				unset($modules['components']);
				unset($modules['bundles']);
				if (count($modules['views']['childs']) > 0) {
					foreach ($modules['views']['childs'] as $childKey => $child) {
						if ($child['is_subview'] != true) {
							unset($modules['views']['childs'][$childKey]);
						}
					}
				}
			} else if ($this->getData()['type'] === 'packages') {
				unset($modules['views']);
				unset($modules['bundles']);
				$this->view->packageSettingsModules = $modules;
				unset($modules['components']);
			} else if ($this->getData()['type'] === 'middlewares') {
				unset($modules['components']);
				unset($modules['middlewares']);
				unset($modules['views']);
				unset($modules['bundles']);
			} else if ($this->getData()['type'] === 'views') {
				unset($modules['components']);
				unset($modules['middlewares']);
				unset($modules['bundles']);
				if (!isset($this->view->subview)) {
					unset($modules['views']);
				}
			}
		} else if (isset($this->getData()['changes']) &&
			$this->getData()['changes'] == true
		) {
			unset($modules['core']);
			unset($modules['apptypes']);
			unset($modules['bundles']);

			$modifiedModules = [];
			foreach ($modules as $moduleType => &$modulesTypeArr) {
				if (isset($modulesTypeArr['childs']) && count($modulesTypeArr['childs']) > 0) {
					foreach ($modulesTypeArr['childs'] as $childKey => &$child) {
						$child = $this->modulesPackage->validateFilesHash($child);

						if ($child['repoExists'] && !$child['isModified'] && !$child['releasePending'] && $child['latestRelease']) {
							unset($modules[$moduleType]['childs'][$childKey]);

							continue;
						}

						if ($child['isModified'] && isset($child['modified_files'])) {
							$modifiedModules[$child['module_type']][$child['id']] = $child['modified_files'];
						}
					}
				}
			}

			$this->view->modules = $modules;

			$this->view->modifiedModules = $modifiedModules;

			$this->view->pick('modules/changes');

			return;
		}

		$this->view->modules = $modules;

		$modulesJson = [];

		foreach ($modules as $moduleKey => $moduleJson) {
			if ($moduleKey === 'bundles' &&
				!$this->view->bundles
			) {
				continue;
			}

			foreach ($moduleJson['childs'] as $childKey => $child) {
				$modulesJson[$moduleKey][$child['id']] =
					[
						'id' 		=> $child['id'],
						'name' 		=> $child['name'],
						'version' 	=> $child['version'] ?? null,
						'repo' 		=> $child['repo'] ?? null,
					];

				if ($moduleKey === 'views') {
					$modulesJson[$moduleKey][$child['id']] =
						array_merge($modulesJson[$moduleKey][$child['id']],
							[
								'base_view_module_id' 	=> $child['base_view_module_id'],
								'is_subview' 			=> $child['is_subview'] ?? false
							]
						);
				}
			}
		}

		$this->view->modulesJson = $this->helper->encode($modulesJson);

		$apis = $this->modulesPackage->getAvailableApis(false, true);

		if (isset($this->getData()['id']) &&
			isset($this->getData()['module']) &&
			isset($this->getData()['type'])
		) {
			$type = strtolower($this->getData()['type']);

			if ($type !== 'apptypes') {
				if ($type !== 'core' && $type !== 'bundles') {
					if (isset($modules['bundles'])) {
						unset($modules['bundles']);
					}
					$this->view->modules = $modules;

					$this->view->categoryArr = ${$type . 'CategoryArr'};

					if ($type === 'components') {
						$this->view->menuBaseStructure = $this->basepackages->menus->getMenusForAppType($module['app_type']);
					}
				} else {
					$this->view->categoryArr = ['core' => ['id' => 'providers', 'name' => 'Providers']];
				}
			} else if ($type === 'apptypes') {
				unset($apis[1]);//Remove core
			}
			$this->view->type = $type;
			$this->view->module = null;
			$this->view->apis = $apis;
			$this->view->moduleTypes = $this->modulesPackage->getModuleTypes();
			$this->view->moduleSettings = $this->modulesPackage->getDefaultSettings();
			$this->view->moduleFilters = $this->modulesPackage->getDefaultFilters();
			if (isset($this->view->subview)) {
				$this->view->moduleDependencies = $this->modulesPackage->getDefaultDependencies($type, true);
			} else {
				$this->view->moduleDependencies = $this->modulesPackage->getDefaultDependencies($type);
			}
			$this->view->moduleMenu = $this->helper->encode([]);
			$this->view->moduleWidgets = $this->helper->encode([]);

			if ($this->getData()['id'] != 0) {
				if ($type !== 'apptypes') {
					if ($type !== 'core') {
						$module = [];

						$module['module_details'] = $this->modules->{$type}->getById($this->getData()['id']);

						if ($module['module_details']['module_type'] !== 'bundles') {
							if ($module['module_details']['module_type'] === 'components') {
								$moduleLocation = 'apps/' . ucfirst($module['module_details']['app_type']) . '/Components/';
								if ($module['module_details']['menu']) {
									$this->view->moduleMenu = $this->helper->encode($module['module_details']['menu']);
									$this->view->menuBaseStructure = $this->basepackages->menus->getMenusForAppType($module['module_details']['app_type']);
								} else {
									$this->view->moduleMenu = false;
									$this->view->menuBaseStructure = [];
								}
							} else if ($module['module_details']['module_type'] === 'packages') {
								if ($module['module_details']['app_type'] === 'core' &&
									(str_starts_with($module['module_details']['category'], 'basepackages') ||
									 $module['module_details']['category'] === 'providers')
								) {
									if ($module['module_details']['category'] === 'basepackagesApis') {
										$moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Basepackages/Api/Apis/';
									} else if (str_starts_with($module['module_details']['category'], 'basepackages')) {
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
								(str_starts_with($module['module_details']['category'], 'basepackages') ||
								 $module['module_details']['category'] === 'providers')
							) {
								$pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['module_details']['name']), -1, PREG_SPLIT_NO_EMPTY);

								$routePath = implode('/', $pathArr) . '/';

								$jsonFile =
									$moduleLocation .
									$routePath .
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
									if ($module['module_details']['is_subview'] == true) {
										if ($module['module_details']['base_view_module_id'] != 0) {
											$baseView = $this->modules->views->getViewById($module['module_details']['base_view_module_id']);
										} else {
											$baseView['name'] = 'Default';
										}

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
								throw $e;
							}
						}
					}

					if (isset($module['widgets']) && is_array($module['widgets'])) {
						$this->view->moduleWidgets = $module['widgets'] = $this->helper->encode($module['widgets']);
					} else {
						$this->view->moduleWidgets = $module['widgets'] = $this->helper->encode([]);
					}
					if (isset($module['settings']) && is_array($module['settings'])) {
						$this->view->moduleSettings = $module['settings'] = $this->helper->encode($module['settings']);
					}
					if (isset($module['filters']) && is_array($module['filters'])) {
						$this->view->moduleFilters = $module['filters'] = $this->helper->encode($module['filters']);
					}
					if (isset($module['dependencies']) && is_array($module['dependencies'])) {
						$this->view->moduleDependencies = $module['dependencies'] = $this->helper->encode($module['dependencies']);
					}
				} else {
					$module = [];

					$module['module_details'] = $this->apps->types->getAppTypeById($this->getData()['id']);

					$jsonFile =
						'apps/' . ucfirst($module['module_details']['app_type']) . '/Install/type.json';

					try {
						$module = array_merge($module, $this->basepackages->utils->validateJson(
							[
								'json' 			=> $this->localContent->read($jsonFile),
								'returnJson' 	=> 'array'
							]
						));

						$module['module_type'] = 'apptypes';

						$module['id'] = $module['module_details']['id'];

					} catch (\throwable $e) {
						throw new \Exception($e->getMessage());
					}
				}

				if (isset($module['module_details']['module_type']) &&
					$module['module_details']['module_type'] === 'bundles'
				) {
					$moduleArr = [];

					$moduleArr = $module['module_details'];
					$moduleArr['module_details'] = $module['module_details'];

					$module = $moduleArr;
				}

				if ($this->view->newrelease) {
					$this->view->availableReleaseTypes = $this->modulesPackage->getAvailableReleaseTypes();

					$module['isPreRelease'] = false;
					$module['preRelease'] = false;
					$module['buildMeta'] = false;
					$module['isCustom'] = false;

					if ($module['version'] !== '0.0.0') {
						try {
							$parsedVersion = Version::parse($module['version']);

							$module['isPreRelease'] = $parsedVersion->isPreRelease();

							if ($module['isPreRelease']) {
								$preRelease = $parsedVersion->getPreRelease();

								if ($preRelease) {
									$preRelease = explode('.', $preRelease);
									if (count($preRelease) > 1) {
										unset($preRelease[$this->helper->lastKey($preRelease)]);
									}
									$module['preRelease'] = implode('.', $preRelease);
								}

								$buildMeta = $parsedVersion->getBuildMeta();
								if ($buildMeta) {
									$buildMeta = explode('.', $buildMeta);
									array_walk($buildMeta, function(&$meta) {
										if ((int) $meta !== 0) {
											try {
												$metaIsUnixTime = \Carbon\Carbon::parse((int) $meta);

												if ($metaIsUnixTime) {
													$meta = 'now';
												}
											} catch (\Exception $e) {
												// Do nothing
											}
										}
									});
									$module['buildMeta'] = implode('.', $buildMeta);
								}
							}
						} catch (\Exception $e) {
							$module['isCustom'] = true;
						}
					}
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
			$this->view->bundleModules = $this->modulesPackage->getDefaultDependencies('bundles');

			if ($this->getData()['id'] != 0) {
				$bundle = $this->modules->bundles->getById($this->getData()['id']);

				if (!$bundle) {
					return $this->throwIdNotFound();
				}

				if (is_array($bundle['bundle_modules'])) {
					$bundle['bundle_modules'] = $this->helper->encode($bundle['bundle_modules']);
				}
				$this->view->bundle = $bundle;
				$this->view->bundleModules = $bundle['bundle_modules'];

				if (isset($modules['bundles']['childs'])) {
					foreach ($modules['bundles']['childs'] as $childBundleKey => $childBundle) {
						if ($bundle['id'] === $childBundle['id']) {
							unset($modules['bundles']['childs'][$childBundleKey]);
						}
					}

					if (count($modules['bundles']['childs']) === 0) {
						unset($modules['bundles']);
					}
				}
			}

			$this->view->modules = $modules;

			$this->view->pick('modules/view');
		} else {
			unset($apis[0]);//Remove local
			unset($apis[1]);//Remove core
			$this->view->apis = $apis;

			$this->view->pick('modules/list');
		}
	}

	public function addAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->addModule($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode,
			$this->modulesPackage->packagesData->responseData ?? []
		);
	}

	public function updateAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->updateModule($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode,
			$this->modulesPackage->packagesData->responseData ?? []
		);
	}

	public function removeAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->removeModule($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function toggleVisibilityRepoAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->toggleVisibilityRepo($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function removeRepoAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->removeRepo($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function formatJsonAction()
	{
		$this->requestIsPost();

		$this->basepackages->utils->formatJson($this->postData());

		$this->addResponse(
			$this->basepackages->utils->packagesData->responseMessage,
			$this->basepackages->utils->packagesData->responseCode,
			$this->basepackages->utils->packagesData->responseData
		);
	}

	protected function processModulesArr($modulesArr)
	{
		$modulesArr['modules'] = $modulesArr;
		$modulesArr['categoryArr'] = [];

		foreach ($modulesArr['modules'] as $key => &$module) {
			$modulesArr['modules'][$key]['data']['app_type'] = $module['app_type'];

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
		$this->requestIsPost();

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
		$this->requestIsPost();

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
	}

	public function getMilestoneLabelIssuesAction()
	{
		$this->requestIsPost();

		if ($this->modulesPackage->getMilestoneLabelIssues($this->postData())) {
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
	}

	public function bumpVersionAction()
	{
		$this->requestIsPost();

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
			$this->modulesPackage->packagesData->responseCode,
			$this->modulesPackage->packagesData->responseData ?? []
		);
	}

	public function syncBranchesAction()
	{
		$this->requestIsPost();

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
	}

	public function syncMilestonesAction()
	{
		$this->requestIsPost();

		if ($this->modulesPackage->syncMilestones($this->postData())) {
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
	}

	public function generateReleaseAction()
	{
		$this->requestIsPost();

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
	}

	public function commitBundleJsonAction()
	{
		$this->requestIsPost();

		if ($this->modulesPackage->commitBundleJson($this->postData())) {
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
	}

	public function generateModuleClassAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->generateModuleClass($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);

		if ($this->modulesPackage->packagesData->responseData) {
			$this->view->responseData = $this->modulesPackage->packagesData->responseData;
		}
	}

	public function generateModuleRepoUrlAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->generateModuleRepoUrl($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);

		if ($this->modulesPackage->packagesData->responseData) {
			$this->view->responseData = $this->modulesPackage->packagesData->responseData;
		}
	}

	public function getDefaultDependenciesAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->getDefaultDependencies($this->postData()['type'], $this->postData()['is_subview']);

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);

		if ($this->modulesPackage->packagesData->responseData) {
			$this->view->responseData = $this->modulesPackage->packagesData->responseData;
		}
	}

	public function checkVersionAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->checkVersion($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function reCalculateFilesHashAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->reCalculateFilesHash($this->postData(), false, false, false, true);

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function getModifiedFilesHashAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->getModifiedFilesHash($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode
		);
	}

	public function getDiffAction()
	{
		$this->requestIsPost();

		$this->modulesPackage->getDiff($this->postData());

		$this->addResponse(
			$this->modulesPackage->packagesData->responseMessage,
			$this->modulesPackage->packagesData->responseCode,
			$this->modulesPackage->packagesData->responseData ?? [],
		);
	}
}