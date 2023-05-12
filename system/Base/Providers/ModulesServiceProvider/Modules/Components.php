<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesComponents;

class Components extends BasePackage
{
	protected $modelToUse = ModulesComponents::class;

	public $components;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getComponentByName($name)
	{
		foreach($this->components as $component) {
			if (strtolower($component['name']) === strtolower($name)) {
				return $component;
			}
		}

		return false;
	}

	public function getComponentsByApiId($apiId)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['api_id'] == $apiId) {
				array_push($components, $component);
			}
		}

		return $components;
	}

	public function getComponentByRepo($repo)
	{
		foreach($this->components as $component) {
			if ($component['repo'] == $repo) {
				return $component;
			}
		}

		return false;
	}

	public function getComponentByRoute($route)
	{
		foreach($this->components as $component) {
			if (strtolower($component['route']) === strtolower($route)) {
				return $component;
			}
		}

		return false;
	}

	public function getComponentByRouteForAppId($route, $appId)
	{
		foreach($this->components as $component) {
			$component['apps'] = Json::decode($component['apps'], true);

			if (isset($component['apps'][$appId])) {
				if (isset($component['apps'][$appId]['enabled']) &&
					$component['apps'][$appId]['enabled'] === true &&
					$component['route'] === $route
				) {
					return $component;
				}
			}
		}

		return false;
	}

	public function getComponentByNameForAppId($name, $appId)
	{
		foreach($this->components as $component) {
			$component['apps'] = Json::decode($component['apps'], true);

			if (isset($component['apps'][$appId])) {
				if (isset($component['apps'][$appId]['enabled']) &&
					$component['apps'][$appId]['enabled'] === true &&
					strtolower($component['name']) === strtolower($name)
				) {
					return $component;
				}
			}
		}

		return false;
	}

	public function getComponentsForAppId($appId)
	{
		$components = [];

		foreach($this->components as $component) {
			$component['apps'] = Json::decode($component['apps'], true);

			if (isset($component['apps'][$appId]['enabled']) &&
				$component['apps'][$appId]['enabled'] === true
			) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function getComponentsForAppIdAndAppType($appId, $appType)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['app_id'] == $appId &&
				$component['app_type'] == $appType
			) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function getComponentById($id)
	{
		foreach($this->components as $component) {
			if ($component['id'] == $id) {
				return $component;
			}
		}
	}

	public function getComponentsForCategory($category)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['category'] === $category) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function getComponentsForAppType($appType)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['app_type'] === $appType) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function getImportComponents()
	{
		$components = [];

		foreach($this->components as $component) {
			$component['settings'] = Json::decode($component['settings'], true);

			if (isset($component['settings']['import']) &&
				$component['settings']['import'] == 'true' &&
				isset($component['settings']['importexportPackage']) &&
				isset($component['settings']['importMethod'])
			) {
				$package = $this->modules->packages->getPackageByName($component['settings']['importexportPackage']);

				if ($package &&
					$this->usePackage($package['class']) &&
					(method_exists($package['class'], 'add' . $component['settings']['importMethod']) &&
					 method_exists($package['class'], 'update' . $component['settings']['importMethod']))
				) {
					$availableComponent['id'] = $component['id'];
					$availableComponent['name'] = $component['name'];

					array_push($components, $availableComponent);
				}
			}
		}

		return $components;
	}

	public function getExportComponents()
	{
		$components = [];

		foreach($this->components as $component) {
			$component['settings'] = Json::decode($component['settings'], true);

			if (isset($component['settings']['export']) &&
				$component['settings']['export'] == 'true' &&
				isset($component['settings']['importexportPackage'])
			) {
				$package = $this->modules->packages->getPackageByName($component['settings']['importexportPackage']);

				if ($package && $this->usePackage($package['class'])) {
					$availableComponent['id'] = $component['id'];
					$availableComponent['name'] = $component['name'];

					array_push($components, $availableComponent);
				}
			}
		}

		return $components;
	}

	public function updateComponents(array $data)
	{
		$components = Json::decode($data['components'], true);
		$needAuths = Json::decode($data['need_auths'], true);

		foreach ($components as $componentId => $status) {
			$component = $this->getById($componentId);

			$component['apps'] = Json::decode($component['apps'], true);
			$component['settings'] = Json::decode($component['settings'], true);

			if ($status === true) {
				$component['apps'][$data['id']]['enabled'] = true;

				if (isset($needAuths[$componentId])) {
					if (isset($component['settings']['needAuth'])) {
						if ($component['settings']['needAuth'] === 'mandatory') {
							$component['apps'][$data['id']]['needAuth'] = 'mandatory';
						} else if ($component['settings']['needAuth'] === 'disabled') {
							$component['apps'][$data['id']]['needAuth'] = 'disabled';
						}
					} else {
						$component['apps'][$data['id']]['needAuth'] = $needAuths[$componentId];
					}
				} else {
					$component['apps'][$data['id']]['needAuth'] = 'disabled';
				}

				$component['dependencies'] = Json::decode($component['dependencies'], true);

				if (isset($component['dependencies']['packages']) && count($component['dependencies']['packages']) > 0) {

					foreach ($component['dependencies']['packages'] as $key => $dependencyPackage) {

						$package = $this->modules->packages->getPackageByNameForRepo($dependencyPackage['name'], $dependencyPackage['repo']);

						if ($package) {
							$package['apps'] = Json::decode($package['apps'], true);

							$package['apps'][$data['id']]['enabled'] = true;

							$package['apps'] = Json::encode($package['apps']);

							$this->modules->packages->update($package);
						}
					}
				}

				$component['dependencies'] = Json::encode($component['dependencies'], JSON_UNESCAPED_SLASHES);

			} else if ($status === false) {
				$component['apps'][$data['id']]['enabled'] = false;
			}

			$component['apps'] = Json::encode($component['apps']);

			$this->update($component);
		}

		return true;
	}

	public function msupdate(array $data)//module settings update
	{
		$component = $this->getById($data['module_id']);

		$component['settings'] = Json::decode($component['settings'], true);

		foreach ($data as $key => $settingsData) {
			if ($key !== 'module_id' &&
				$key !== 'module_type' &&
				$settingsData !== $this->security->getRequestToken()
			) {
				if (isset($component['settings'][$key])) {
					$settingsData = Json::decode($settingsData, true);

					$component['settings'][$key] = $settingsData;
				}
			}
		}

		$component['settings'] = Json::encode($component['settings']);

		$this->update($component);
	}
}