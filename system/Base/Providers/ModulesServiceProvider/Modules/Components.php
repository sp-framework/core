<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

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

	public function getComponentByNameForAppType($name, $appType)
	{
		foreach($this->components as $component) {
			if (strtolower($component['name']) === strtolower($name) &&
				strtolower($component['app_type']) === strtolower($appType)
			) {
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

	public function getComponentByAppTypeAndRepoAndRoute($appType, $repo, $route)
	{
		foreach($this->components as $component) {
			if ($component['app_type'] === $appType &&
				$component['repo'] === $repo &&
				trim($route, '/') === trim($component['route'], '/')
			) {
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

	public function getComponentByRouteForAppId($route, $appId = null)
	{
		if (!$appId) {
			$appId = isset($this->apps->getAppInfo()['id']) ? $this->apps->getAppInfo()['id'] : false;

			if (!$appId) {
				return false;
			}
		}

		if ($route === '') {
			$route = 'home';
		}

		foreach($this->components as $component) {
			$component['apps'] = $this->helper->decode($component['apps'], true);

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
			$component['apps'] = $this->helper->decode($component['apps'], true);

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

	public function getComponentByClassForAppId($class, $appId = null)
	{
		if (!$appId) {
			$appId = isset($this->apps->getAppInfo()['id']) ? $this->apps->getAppInfo()['id'] : false;

			if (!$appId) {
				return false;
			}
		}

		if (!str_contains($class, 'Component')) {
			$class = ucfirst($class) . 'Component';
		}

		$classArr = explode('\\', $class);

		foreach($this->components as $component) {
			$component['apps'] = $this->helper->decode($component['apps'], true);

			if (count($classArr) === 1) {//Only Class Name Given
				if (!str_contains($component['class'], $classArr[0])) {
					continue;
				}
			} else {
				$class = implode('\\', $classArr);

				if ($component['class'] !== $class) {
					continue;
				}
			}

			if (isset($component['apps'][$appId])) {
				if (isset($component['apps'][$appId]['enabled']) &&
					$component['apps'][$appId]['enabled'] === true
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
			$component['apps'] = $this->helper->decode($component['apps'], true);

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
			if (is_string($component['settings'])) {
				$component['settings'] = $this->helper->decode($component['settings'], true);
			}

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
			if (is_string($component['settings'])) {
				$component['settings'] = $this->helper->decode($component['settings'], true);
			}

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
		$components = $this->helper->decode($data['components'], true);
		$needAuths = $this->helper->decode($data['need_auths'], true);

		$homeNeedsAuth = false;
		if (in_array(true, $needAuths)) {//If any of them is true, we have to make home true.
			$homeNeedsAuth = true;
		}

		foreach ($components as $componentId => $status) {
			$component = $this->getById($componentId);

			if ($component['route'] === 'home' && $homeNeedsAuth) {
				$needAuths[$componentId] = true;
			}

			if (is_string($component['apps'])) {
				$component['apps'] = $this->helper->decode($component['apps'], true);
			}

			if (is_string($component['settings'])) {
				$component['settings'] = $this->helper->decode($component['settings'], true);
			}

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

				if (is_string($component['dependencies'])) {
					$component['dependencies'] = $this->helper->decode($component['dependencies'], true);
				}

				if (isset($component['dependencies']['packages']) && count($component['dependencies']['packages']) > 0) {

					foreach ($component['dependencies']['packages'] as $key => $dependencyPackage) {

						$package = $this->modules->packages->getPackageByNameForRepo($dependencyPackage['name'], $dependencyPackage['repo']);

						if ($package) {
							$package['apps'] = $this->helper->decode($package['apps'], true);

							$package['apps'][$data['id']]['enabled'] = true;

							$package['apps'] = $this->helper->encode($package['apps']);

							$this->modules->packages->update($package);
						}
					}
				}

				$component['dependencies'] = $this->helper->encode($component['dependencies'], JSON_UNESCAPED_SLASHES);

			} else if ($status === false) {
				$component['apps'][$data['id']]['enabled'] = false;
			}

			$component['apps'] = $this->helper->encode($component['apps']);

			$this->update($component);
		}

		return true;
	}

	public function msupdate(array $data)//module settings update
	{
		$component = $this->getById($data['id']);

		if (is_string($component['settings'])) {
			$component['settings'] = $this->helper->decode($component['settings'], true);
		}

		foreach ($data as $key => $settingsData) {
			if ($key !== 'id' &&
				$key !== 'module_type' &&
				$settingsData !== $this->security->getRequestToken()
			) {
				if (isset($component['settings'][$key])) {
					$settingsData = $this->helper->decode($settingsData, true);

					$component['settings'][$key] = $settingsData;
				}
			}
		}

		$component['settings'] = $this->helper->encode($component['settings']);

		$this->update($component);
	}
}