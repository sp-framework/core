<?php

namespace Packages\Admin;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Packages\Admin\Modules\Barebone;
use Packages\Admin\Modules\Module\Info;
use Packages\Admin\Modules\Module\Install;
use Packages\Admin\Modules\Module\Remove;
use Packages\Admin\Modules\Module\Settings;
use Packages\Admin\Modules\Module\Update;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Core;
use System\Base\Providers\ModulesServiceProvider\Repositories;

class Modules extends BasePackage
{
	protected $repository;

	protected $localModules = [];

	protected $remoteModules = [];

	protected $modulesData = [];

	protected $module;

	public function syncRemoteWithLocal($id)
	{
		$this->repository = $this->repositories->getById($id)->getAllArr();

		$this->getLocalModules();

		if ($this->getRemoteModules()) {

			$this->updateRemoteModulesToDB();
		}

		return $this->packagesData;
	}

	public function getModulesData()
	{
		$this->getLocalModules();

		$this->packagesData->responseCode = 0;

		$this->packagesData->modulesData = $this->localModules;

		$this->packagesData->repositories =
			getAllArr($this->repositories->getAll());

		$this->packagesData->applications =
			getAllArr($this->applications->getAll());

		return $this->packagesData;
	}

	public function getLocalModules($filter = [], $includeCore = true)
	{
		if ($includeCore) {
			if ($this->core->getCoreInfo()) {
				foreach ($this->core->getCoreInfo() as $coreKey => $core) {
					$this->localModules['core'][$core->get('id')] = $core->getAllArr();
				}
			}
		}

		// dump($filter, $this->applications->getAll($filter));
		if (!$includeCore) {
			$applicationFilter = ['id' => $filter['application_id']];
		} else if (isset($filter['installed']) || isset($filter['update_available'])) {
			$applicationFilter = $filter;
		} else {
			$applicationFilter = [];
		}
		// var_dump($filter);

		if (count($this->applications->getAll($applicationFilter)) > 0) {
			foreach ($this->applications->getAll($applicationFilter) as $applicationKey => $application) {
				$this->localModules['applications'][$application->get('id')] = $application->getAllArr();
				$this->localModules['applications'][$application->get('id')]['settings']
					= json_decode($application->get('settings'), true);
				$this->localModules['applications'][$application->get('id')]['dependencies']
					= json_decode($application->get('dependencies'), true);
			}
		} else {
			$this->localModules['applications'] = [];
		}

		if (count($this->components->getAll($filter)) > 0) {
			foreach ($this->components->getAll($filter) as $componentKey => $component) {
				$this->localModules['components'][$component->get('id')] = $component->getAllArr();
				$this->localModules['components'][$component->get('id')]['settings']
					= json_decode($component->get('settings'), true);
				$this->localModules['components'][$component->get('id')]['dependencies']
					= json_decode($component->get('dependencies'), true);
			}
		} else {
			$this->localModules['components'] = [];
		}

		if (count($this->packages->getAll($filter)) > 0) {
			foreach ($this->packages->getAll($filter) as $packageKey => $package) {
				$this->localModules['packages'][$package->get('id')] = $package->getAllArr();
				$this->localModules['packages'][$package->get('id')]['settings']
					= json_decode($package->get('settings'), true);
				$this->localModules['packages'][$package->get('id')]['dependencies']
					= json_decode($package->get('dependencies'), true);
			}
		} else {
			$this->localModules['packages'] = [];
		}

		if (count($this->middlewares->getAll($filter)) > 0) {
			foreach ($this->middlewares->getAll($filter) as $middlewareKey => $middleware) {
				$this->localModules['middlewares'][$middleware->get('id')] = $middleware->getAllArr();
				$this->localModules['middlewares'][$middleware->get('id')]['settings']
					= json_decode($middleware->get('settings'), true);
				$this->localModules['middlewares'][$middleware->get('id')]['dependencies']
					= json_decode($middleware->get('dependencies'), true);
			}
		} else {
			$this->localModules['middlewares'] = [];
		}

		if (count($this->views->getAll($filter)) > 0) {
			foreach ($this->views->getAll($filter) as $viewKey => $view) {
				$this->localModules['views'][$view->get('id')] = $view->getAllArr();
				$this->localModules['views'][$view->get('id')]['settings']
					= json_decode($view->get('settings'), true);
				$this->localModules['views'][$view->get('id')]['dependencies']
					= json_decode($view->get('dependencies'), true);
			}
		} else {
			$this->localModules['views'] = [];
		}

		// dump($this->localModules);
		// dump(count($filter), !$includeCore);
		if (count($filter) > 0 || !$includeCore) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->modulesData = $this->localModules;

		// dump($this->localModules);
			return $this->packagesData;
		}
	}

	// public function getFilteredData($filter, $includeCore = true)
	// {
	// 	if ($includeCore) {
	// 		if (count($this->core->getAll($filter)) > 0) {
	// 			foreach ($this->core->getAll($filter) as $componentKey => $component) {
	// 				$this->localModules['core'][$component->get('id')] = $component->getAllArr();
	// 				$this->localModules['core'][$component->get('id')]['settings']
	// 					= unserialize($component->get('settings'));
	// 				$this->localModules['core'][$component->get('id')]['dependencies']
	// 					// = unserialize($this->localModules['components'][$component->get('id')]['dependencies']);
	// 					= unserialize($component->get('dependencies'));
	// 			}
	// 		} else {
	// 			$this->localModules['components'] = [];
	// 		}
	// 	}

	// 	if (count($this->components->getAll($filter)) > 0) {
	// 		foreach ($this->components->getAll($filter) as $componentKey => $component) {
	// 			$this->localModules['components'][$component->get('id')] = $component->getAllArr();
	// 			$this->localModules['components'][$component->get('id')]['settings']
	// 				= unserialize($component->get('settings'));
	// 			$this->localModules['components'][$component->get('id')]['dependencies']
	// 				// = unserialize($this->localModules['components'][$component->get('id')]['dependencies']);
	// 				= unserialize($component->get('dependencies'));
	// 		}
	// 	} else {
	// 		$this->localModules['components'] = [];
	// 	}

	// 	if (count($this->packages->getAll($filter)) > 0) {
	// 		foreach ($this->packages->getAll($filter) as $packageKey => $package) {
	// 			$this->localModules['packages'][$package->get('id')] = $package->getAllArr();
	// 			$this->localModules['packages'][$package->get('id')]['settings']
	// 				= unserialize($package->get('settings'));
	// 			$this->localModules['packages'][$package->get('id')]['dependencies']
	// 				// = unserialize($this->localModules['packages'][$package->get('id')]['dependencies']);
	// 				= unserialize($package->get('dependencies'));
	// 		}
	// 	} else {
	// 		$this->localModules['packages'] = [];
	// 	}

	// 	if (count($this->views->getAll($filter)) > 0) {
	// 		foreach ($this->views->getAll($filter) as $viewKey => $view) {
	// 			$this->localModules['views'][$view->get('id')] = $view->getAllArr();
	// 			$this->localModules['views'][$view->get('id')]['settings']
	// 				= unserialize($view->get('settings'));
	// 			$this->localModules['views'][$view->get('id')]['dependencies']
	// 				// = unserialize($this->localModules['views'][$view->get('id')]['dependencies']);
	// 				= unserialize($view->get('dependencies'));
	// 		}
	// 	} else {
	// 		$this->localModules['views'] = [];
	// 	}

	// 	$this->packagesData->responseCode = 0;

	// 	$this->packagesData->modulesData = $this->localModules;

	// 	return $this->packagesData;
	// }

	protected function getRemoteModules()
	{
		$client = new Client();
		//Token 799041c39191f64d811d08ee3a921602d5d620e6

		$repoUrl = $this->repository['url'];

		$headers =
			[
				'headers'	=>
					[
						'accept'	=>	'application/vnd.github.mercy-preview+json'
					],
				'auth'		=>
					[
						$this->repository['username'],
						$this->repository['token']
					]
			];

		try {

			$body = json_decode($client->get($repoUrl, $headers)->getBody()->getContents());

		} catch (ClientException $e) {
			$body = null;

			$this->packagesData->responseCode = 1;

			if ($e->getResponse()->getStatusCode() === 403) {
				$this->packagesData->responseMessage = 'Add username and token to repository.<br>' . $e->getMessage();
			} else {
				$this->packagesData->responseMessage = $e->getMessage();
			}

			return $this->packagesData;
		} catch (ConnectException $e) {

			$body = null;

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = $e->getMessage();

			return $this->packagesData;
		}

		if ($body) {
			foreach ($body as $key => $value) {

				$names = explode('-', $value->name);

				if (count($names) > 0) {
					if ($names[0] === 'core') {

						$url =
							'https://raw.githubusercontent.com/' .
							$value->full_name . '/master/core.json';

						$this->remoteModules['core'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);

					} else if ($names[0] === 'application') {
						array_shift($names);

						$url =
							'https://raw.githubusercontent.com/' .
							$value->full_name . '/master/applications/' .
							ucfirst($names[0]) . '/application.json';

						$this->remoteModules['applications'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);

					} else if ($names[0] === 'component') {
						array_shift($names);

						$url = '';
						$url .=
							'https://raw.githubusercontent.com/' .
							$value->full_name . '/master/components/' .
							ucfirst($names[0]) . '/Install/';

						array_shift($names);

						$url .= implode('/', array_map('ucfirst', $names));

						$url .= '/component.json';

						$this->remoteModules['components'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);

					} else if ($names[0] === 'package') {
						array_shift($names);

						$url = '';
						$url .=
							'https://raw.githubusercontent.com/' .
							$value->full_name . '/master/packages/' .
							ucfirst($names[0]) . '/Install/';

						array_shift($names);

						$url .= implode('/', array_map('ucfirst', $names));

						$url .= '/package.json';

						$this->remoteModules['packages'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);

					} else if ($names[0] === 'middleware') {
						array_shift($names);

						$url = '';
						$url .=
							'https://raw.githubusercontent.com/' .
							$value->full_name . '/master/middlewares/' .
							ucfirst($names[0]) . '/Install/';

						array_shift($names);

						$url .= implode('/', array_map('ucfirst', $names));

						$url .= '/middleware.json';

						$this->remoteModules['middlewares'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);

					} else if ($names[0] === 'view') {
						if (count($names) === 3) {
							array_shift($names);

							$url = '';
							$url .=
								'https://raw.githubusercontent.com/' .
								$value->full_name . '/master/views/' .
								ucfirst($names[0]) . '/' . ucfirst($names[1]);

							$url .= '/view.json';
						} else {
							//For custom View XMl.
							array_shift($names);

							$url = '';
							$url .=
								'https://raw.githubusercontent.com/' .
								$value->full_name . '/master/views/' .
								ucfirst($names[0]) . '/' . ucfirst($names[1]) . '/html/';

							array_shift($names);
							array_shift($names);

							$url .= implode('/', $names);

							$url .= '/view.json';
						}

						$this->remoteModules['views'][$value->name] =
							json_decode(
								$client->get($url)->getBody()->getContents()
								, true
							);
					}
				}
			}
		}

		$this->packagesData->responseCode = 0;

		return $this->packagesData;
	}

	// protected function compareInstalledWithRemote()
	// {
	// 	foreach ($this->localModules as $installedPackageKey => $installedPackage) {
	// 		if ($installedPackageKey === 'core') {
	// 			foreach ($installedPackage as $coreKey => $core) {
	// 				foreach ($this->modulesData[$installedPackageKey] as $repoCoreKey => $repoCore) {
	// 					if ($repoCore) {
	// 						if ($core['name'] === $repoCore['name'] &&
	// 							$core['repo'] === $repoCore['repo']
	// 						) {
	// 							$this->modulesData[$installedPackageKey][$repoCoreKey]['installed'] = true;
	// 							$this->modulesData[$installedPackageKey][$repoCoreKey]['installed_id']
	// 								= $core['id'];

	// 							if ($core['version'] !== $repoCore['version']) {
	// 								$this->modulesData[$installedPackageKey][$repoCoreKey]['update'] = true;
	// 								$this->modulesData[$installedPackageKey][$repoCoreKey]['installed_version']
	// 									= $core['version'];
	// 							} else {
	// 								$this->modulesData[$installedPackageKey][$repoCoreKey]['update'] = false;
	// 							}
	// 						} else {
	// 							$this->modulesData[$installedPackageKey][$repoCoreKey]['installed'] = false;
	// 						}
	// 					} else {
	// 						$this->modulesData[$installedPackageKey][$repoCoreKey]['error'] = true;
	// 					}
	// 				}
	// 			}
	// 		}

	// 		if ($installedPackageKey === 'applications') {
	// 			foreach ($installedPackage as $applicationKey => $application) {
	// 				foreach ($this->modulesData[$installedPackageKey] as $repoApplicationKey => $repoApplication) {
	// 					if ($repoApplication) {
	// 						if ($application['name'] === $repoApplication['name'] &&
	// 							$application['repo'] === $repoApplication['repo']
	// 						) {
	// 							$this->modulesData[$installedPackageKey][$repoApplicationKey]['installed'] = true;
	// 							$this->modulesData[$installedPackageKey][$repoApplicationKey]['installed_id']
	// 								= $application['id'];

	// 							if ($application['version'] !== $repoApplication['version']) {
	// 								$this->modulesData[$installedPackageKey][$repoApplicationKey]['update'] = true;
	// 								$this->modulesData[$installedPackageKey][$repoApplicationKey]['installed_version']
	// 									= $application['version'];
	// 							} else {
	// 								$this->modulesData[$installedPackageKey][$repoApplicationKey]['update'] = false;
	// 							}
	// 						} else {
	// 							$this->modulesData[$installedPackageKey][$repoApplicationKey]['installed'] = false;
	// 						}
	// 					} else {
	// 						$this->modulesData[$installedPackageKey][$repoApplicationKey]['error'] = true;
	// 					}
	// 				}
	// 			}
	// 		}

	// 		if ($installedPackageKey === 'components') {
	// 			foreach ($installedPackage as $componentKey => $component) {
	// 				foreach ($this->modulesData[$installedPackageKey] as $repoComponentKey => $repoComponent) {
	// 					if ($repoComponent) {
	// 						if ($component['name'] === $repoComponent['name'] &&
	// 							$component['repo'] === $repoComponent['repo']
	// 						) {
	// 							$this->modulesData[$installedPackageKey][$repoComponentKey]['installed'] = true;
	// 							$this->modulesData[$installedPackageKey][$repoComponentKey]['installed_id']
	// 								= $component['id'];

	// 							if ($component['version'] !== $repoComponent['version']) {
	// 								$this->modulesData[$installedPackageKey][$repoComponentKey]['update'] = true;
	// 								$this->modulesData[$installedPackageKey][$repoComponentKey]['installed_version']
	// 									= $component['version'];
	// 							} else {
	// 								$this->modulesData[$installedPackageKey][$repoComponentKey]['update'] = false;
	// 							}
	// 						} else {
	// 							$this->modulesData[$installedPackageKey][$repoComponentKey]['installed'] = false;
	// 						}
	// 					} else {
	// 							$this->modulesData[$installedPackageKey][$repoComponentKey]['error'] = true;
	// 					}
	// 				}
	// 			}
	// 		}

	// 		if ($installedPackageKey === 'packages') {
	// 			foreach ($installedPackage as $packageKey => $package) {
	// 				foreach ($this->modulesData[$installedPackageKey] as $repoPackageKey => $repoPackage) {
	// 					if ($repoPackage) {
	// 						if ($package['name'] === $repoPackage['name'] &&
	// 							$package['repo'] === $repoPackage['repo']
	// 						) {
	// 							$this->modulesData[$installedPackageKey][$repoPackageKey]['installed'] = true;
	// 							$this->modulesData[$installedPackageKey][$repoPackageKey]['installed_id']
	// 								= $package['id'];

	// 							if ($package['version'] !== $repoPackage['version']) {
	// 								$this->modulesData[$installedPackageKey][$repoPackageKey]['update'] = true;
	// 								$this->modulesData[$installedPackageKey][$repoPackageKey]['installed_version']
	// 									= $package['version'];
	// 							} else {
	// 								$this->modulesData[$installedPackageKey][$repoPackageKey]['update'] = false;
	// 							}
	// 						} else {
	// 							$this->modulesData[$installedPackageKey][$repoPackageKey]['installed'] = false;
	// 						}
	// 					} else {
	// 						$this->modulesData[$installedPackageKey][$repoPackageKey]['error'] = true;
	// 					}
	// 				}
	// 			}
	// 		}

	// 		if ($installedPackageKey === 'views') {
	// 			foreach ($installedPackage as $viewKey => $view) {
	// 				foreach ($this->modulesData[$installedPackageKey] as $repoViewKey => $repoView) {
	// 					if ($repoView) {
	// 						if ($view['name'] === $repoView['name'] &&
	// 							$view['repo'] === $repoView['repo']
	// 						) {
	// 							$this->modulesData[$installedPackageKey][$repoViewKey]['installed'] = true;
	// 							$this->modulesData[$installedPackageKey][$repoViewKey]['installed_id']
	// 								= $view['id'];

	// 							if ($view['version'] !== $repoView['version']) {
	// 								$this->modulesData[$installedPackageKey][$repoViewKey]['update'] = true;
	// 								$this->modulesData[$installedPackageKey][$repoViewKey]['installed_version']
	// 									= $view['version'];
	// 							} else {
	// 								$this->modulesData[$installedPackageKey][$repoViewKey]['update'] = false;
	// 							}
	// 						} else {
	// 							$this->modulesData[$installedPackageKey][$repoViewKey]['installed'] = false;
	// 						}
	// 					} else {
	// 						$this->modulesData[$installedPackageKey][$repoViewKey]['error'] = true;
	// 					}
	// 				}
	// 			}
	// 		}
	// 	}
	// }

	protected function findRemoteInLocal($remoteModules, $localModules)
	{
		$modules = [];
		$modules['update'] = [];

		foreach ($remoteModules as $remoteModuleKey => $remoteModule) {
			foreach ($localModules as $localModuleKey => $localModule) {
				if ($localModule['name'] === $remoteModule['name'] &&
					$localModule['repo'] === $remoteModule['repo']
					) {

					if ($this->moduleNeedsUpgrade($localModule, $remoteModule)) {

						if ($localModule['installed'] === 0) {

							$localModule['version'] = $remoteModule['version'];
						} else if ($localModule['installed'] === 1) {

							$localModule['update_available'] = true;

							$localModule['update_version'] = $remoteModule['version'];
						}

						if (isset($localModule['settings'])) {
							$localModule['settings'] = json_encode($localModule['settings']);
						} else {
							$localModule['settings'] = null;
						}

						if (isset($localModule['dependencies'])) {
							$localModule['dependencies'] = json_encode($remoteModule['dependencies']);
						} else {
							$localModule['dependencies'] = null;
						}

						$modules['update'][$localModuleKey] = $localModule;

						unset($remoteModules[$remoteModuleKey]);
					}

					unset($remoteModules[$remoteModuleKey]);
				}
			}
		}

		$modules['register'] = $remoteModules;

		return $modules;
	}

	protected function moduleNeedsUpgrade($localModule, $remoteModule)
	{
		if ($localModule['version'] !== $remoteModule['version'] &&
			$localModule['update_version'] !== $remoteModule['version']
		   ) {

			$installedModuleVersion = explode('.', $localModule['version']);

			$newModuleVersion = explode('.', $remoteModule['version']);

			if ($newModuleVersion[0] > $installedModuleVersion[0]) {

				return true;

			} else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
					   $newModuleVersion[1] > $installedModuleVersion[1]
					  ) {

				return true;

			} else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
					   $newModuleVersion[1] === $installedModuleVersion[1] &&
					   $newModuleVersion[2] > $installedModuleVersion[2]
					) {

				return true;
			}
		} else {
			return false;
		}
	}

	protected function updateRemoteModulesToDB()
	{
		$counter = [];
		$counter['register'] = 0;
		$counter['update'] = 0;

		foreach ($this->remoteModules as $remoteModulesType => $remoteModules) {
			if ($remoteModulesType === 'core') {

				$remoteCore = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);

				if (count($remoteCore['update']) > 0) {
					foreach ($remoteCore['update'] as $updateRemoteCoreKey => $updateRemoteCore) {
						$this->core->updateCoreInfo($updateRemoteCore);
						$counter['update'] = $counter['update'] + 1;
					}
				}
			}

			if ($remoteModulesType === 'applications') {

				if (count($this->localModules[$remoteModulesType]) > 0) {
					$remoteApplications = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);

				} else {
					$remoteApplications['update'] = [];
					$remoteApplications['register'] = $remoteModules;
				}

				if (count($remoteApplications['update']) > 0) {
					foreach ($remoteApplications['update'] as $updateRemoteApplicationKey => $updateRemoteApplication) {
						$this->applications->update($updateRemoteApplication);
						$counter['update'] = $counter['update'] + 1;
					}
				}

				if (count($remoteApplications['register']) > 0) {
					foreach ($remoteApplications['register'] as $registerRemoteApplicationKey => $registerRemoteApplication) {
						$registerRemoteApplication['installed'] = 0;

						$registerRemoteApplication['mode'] = $this->mode === 'true' ? 1 : 0;

						$registerRemoteApplication['display_name'] =
							isset($registerRemoteApplication['displayName']) ?
							$registerRemoteApplication['displayName'] :
							null;

						$registerRemoteApplication['settings'] =
							isset($registerRemoteApplication['settings']) ?
							json_encode($registerRemoteApplication['settings']) :
							json_encode([]);

						$registerRemoteApplication['dependencies'] =
							isset($registerRemoteApplication['dependencies']) ?
							json_encode($registerRemoteApplication['dependencies']) :
							json_encode([]);

						$registerRemoteApplication['is_default'] = 0;

						$this->applications->register($registerRemoteApplication);
						$counter['register'] = $counter['register'] + 1;
					}
				}
			}

			if ($remoteModulesType === 'components') {

				if (count($this->localModules[$remoteModulesType]) > 0) {
					$remoteComponents = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
				} else {
					$remoteComponents['update'] = [];
					$remoteComponents['register'] = $remoteModules;
				}

				if (count($remoteComponents['update']) > 0) {
					foreach ($remoteComponents['update'] as $updateRemoteComponentKey => $updateRemoteComponent) {

						$updateRemoteComponent['settings'] =
							isset($updateRemoteComponent['settings']) ?
							json_encode($updateRemoteComponent['settings']) :
							json_encode([]);

						$updateRemoteComponent['dependencies'] =
							isset($updateRemoteComponent['dependencies']) ?
							json_encode($updateRemoteComponent['dependencies']) :
							json_encode([]);

						$this->components->update($updateRemoteComponent);

						$counter['update'] = $counter['update'] + 1;
					}
				}

				if (count($remoteComponents['register']) > 0) {
					foreach ($remoteComponents['register'] as $registerRemoteComponentKey => $registerRemoteComponent) {

						if (isset($registerRemoteComponent['dependencies']['application'])) {
							$applications =
								$this->applications->getAll(
										['repo' => $registerRemoteComponent['dependencies']['application']['repo']]
									);

							if (count($applications) > 0) {
								$applicationId = $applications[0]->get('id');
							} else {
								$applicationId = null;
							}
						} else {
							//error application dependency not set, still add to DB with a warning?
						}

						if ($applicationId) {

							$registerRemoteComponent['application_id'] = $applicationId;

							$registerRemoteComponent['installed'] = 0;

							$registerRemoteComponent['display_name'] =
								isset($registerRemoteComponent['displayName']) ?
								$registerRemoteComponent['displayName'] :
								null;

							$registerRemoteComponent['settings'] =
								isset($registerRemoteComponent['settings']) ?
								json_encode($registerRemoteComponent['settings']) :
								json_encode([]);

							$registerRemoteComponent['dependencies'] =
								isset($registerRemoteComponent['dependencies']) ?
								json_encode($registerRemoteComponent['dependencies']) :
								json_encode([]);

							$this->components->register($registerRemoteComponent);
							$counter['register'] = $counter['register'] + 1;
						}
					}
				}
			}

			if ($remoteModulesType === 'packages') {

				if (count($this->localModules[$remoteModulesType]) > 0) {
					$remotePackages = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
				} else {
					$remotePackages['update'] = [];
					$remotePackages['register'] = $remoteModules;
				}


				if (count($remotePackages['update']) > 0) {
					foreach ($remotePackages['update'] as $updateRemotePackageKey => $updateRemotePackage) {
						$this->packages->update($updateRemotePackage);
						$counter['update'] = $counter['update'] + 1;
					}
				}

				if (count($remotePackages['register']) > 0) {
					foreach ($remotePackages['register'] as $registerRemotePackageKey => $registerRemotePackage) {

						if (isset($registerRemotePackage['dependencies']['application'])) {
							$applications =
								$this->applications->getAll(
										['repo' => $registerRemotePackage['dependencies']['application']['repo']]
									);

							if (count($applications) > 0) {
								$applicationId = $applications[0]->get('id');
							} else {
								$applicationId = null;
							}
						} else {
							//error application dependency not set, still add to DB with a warning?
						}

						if ($applicationId) {

							$registerRemotePackage['application_id'] = $applicationId;

							$registerRemotePackage['installed'] = 0;

							$registerRemotePackage['display_name'] =
								isset($registerRemotePackage['displayName']) ?
								$registerRemotePackage['displayName'] :
								null;

							$registerRemotePackage['settings'] =
								isset($registerRemotePackage['settings']) ?
								json_encode($registerRemotePackage['settings']) :
								json_encode([]);

							$registerRemotePackage['dependencies'] =
								isset($registerRemotePackage['dependencies']) ?
								json_encode($registerRemotePackage['dependencies']) :
								json_encode([]);

							$this->packages->register($registerRemotePackage);
							$counter['register'] = $counter['register'] + 1;
						}
					}
				}
			}

			if ($remoteModulesType === 'middlewares') {

				if (count($this->localModules[$remoteModulesType]) > 0) {
					$remoteMiddlewares = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
				} else {
					$remoteMiddlewares['update'] = [];
					$remoteMiddlewares['register'] = $remoteModules;
				}


				if (count($remoteMiddlewares['update']) > 0) {
					foreach ($remoteMiddlewares['update'] as $updateRemoteMiddlewareKey => $updateRemoteMiddleware) {
						$this->middlewares->update($updateRemoteMiddleware);
						$counter['update'] = $counter['update'] + 1;
					}
				}

				if (count($remoteMiddlewares['register']) > 0) {
					foreach ($remoteMiddlewares['register'] as $registerRemoteMiddlewareKey => $registerRemoteMiddleware) {

						if (isset($registerRemoteMiddleware['dependencies']['application'])) {
							$applications =
								$this->applications->getAll(
										['repo' => $registerRemoteMiddleware['dependencies']['application']['repo']]
									);

							if (count($applications) > 0) {
								$applicationId = $applications[0]->get('id');
							} else {
								$applicationId = null;
							}
						} else {
							//error application dependency not set, still add to DB with a warning?
						}

						if ($applicationId) {

							$registerRemoteMiddleware['application_id'] = $applicationId;

							$registerRemoteMiddleware['installed'] = 0;

							$registerRemoteMiddleware['display_name'] =
								isset($registerRemoteMiddleware['displayName']) ?
								$registerRemoteMiddleware['displayName'] :
								null;

							$registerRemoteMiddleware['settings'] =
								isset($registerRemoteMiddleware['settings']) ?
								json_encode($registerRemoteMiddleware['settings']) :
								json_encode([]);

							$registerRemoteMiddleware['dependencies'] =
								isset($registerRemoteMiddleware['dependencies']) ?
								json_encode($registerRemoteMiddleware['dependencies']) :
								json_encode([]);

							$registerRemoteMiddleware['sequence'] =
								isset($registerRemoteMiddleware['sequence']) ?
								$registerRemoteMiddleware['sequence'] :
								0;

							$registerRemoteMiddleware['enabled'] =
								isset($registerRemoteMiddleware['enabled']) ?
								$registerRemoteMiddleware['enabled'] :
								0;

							$this->middlewares->register($registerRemoteMiddleware);
							$counter['register'] = $counter['register'] + 1;
						}
					}
				}
			}

			if ($remoteModulesType === 'views') {

				if (count($this->localModules[$remoteModulesType]) > 0) {
					$remoteViews = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
				} else {
					$remoteViews['update'] = [];
					$remoteViews['register'] = $remoteModules;
				}


				if (count($remoteViews['update']) > 0) {
					foreach ($remoteViews['update'] as $updateRemoteViewKey => $updateRemoteView) {
						$this->views->update($updateRemoteView);
						$counter['update'] = $counter['update'] + 1;
					}
				}

				if (count($remoteViews['register']) > 0) {
					foreach ($remoteViews['register'] as $registerRemoteViewKey => $registerRemoteView) {

						if (isset($registerRemoteView['dependencies']['application'])) {
							$applications =
								$this->applications->getAll(
										['repo' => $registerRemoteView['dependencies']['application']['repo']]
									);

							if (count($applications) > 0) {
								$applicationId = $applications[0]->get('id');
							} else {
								$applicationId = null;
							}
						} else {
							//error application dependency not set, still add to DB with a warning?
						}

						if ($applicationId) {

							$registerRemoteView['application_id'] = $applicationId;

							$registerRemoteView['installed'] = 0;

							$registerRemoteView['display_name'] =
								isset($registerRemoteView['displayName']) ?
								$registerRemoteView['displayName'] :
								null;

							$registerRemoteView['settings'] =
								isset($registerRemoteView['settings']) ?
								json_encode($registerRemoteView['settings']) :
								json_encode([]);

							$registerRemoteView['dependencies'] =
								isset($registerRemoteView['dependencies']) ?
								json_encode($registerRemoteView['dependencies']) :
								json_encode([]);

							$this->views->register($registerRemoteView);
							$counter['register'] = $counter['register'] + 1;
						}
					}
				}
			}
		}

		$this->packagesData->counter = $counter;
	}

	public function viewModule($getData)
	{
		return $this->packages->use(Info::class)->runProcess($getData);
	}

	public function installModule($postData)
	{
		return $this->packages->use(Install::class)->runProcess($postData);
	}

	public function updateModule($postData)
	{
		return $this->packages->use(Update::class)->runProcess($postData);
	}

	public function removeModule($postData)
	{
		return $this->packages->use(Remove::class)->runProcess($postData);
	}

	public function getModuleSettings($getData)
	{
		return $this->packages->use(Settings::class)->get($getData);
	}

	public function updateModuleSettings($postData)
	{
		return $this->packages->use(Settings::class)->update($postData);
	}

	public function installBareboneModules($postData)
	{
		return $this->packages->use(Barebone::class)->runProcess($postData);
	}

	public function getApplicationComponentsViews($postData)
	{
		return $this->packages->use(Barebone::class)->getApplicationComponentsViews($postData);
	}

	public function getRepositoryById($id)
	{
		return $this->repositories->getById($id);
	}

	public function addRepository($postData)
	{
		return $this->repositories->register($postData);
	}

	public function updateRepository($postData)
	{
		return $this->repositories->update($postData);
	}

	public function deleteRepository($id)
	{
		return $this->repositories->remove($id);
	}
}