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

	public function getRouteComponentForApp($route, $appId)
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

	public function getNamedComponentForApp($name, $appId)
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

	public function getComponentsForApp($appId)
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

	public function getComponentsForAppAndType($appId, $type)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['app_id'] == $appId &&
				$component['type'] == $type
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

	public function getComponentsForCategoryAndSubcategory($category, $subCategory)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['category'] === $category && $component['sub_category'] === $subCategory) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function getComponentsForAppType(string $type)
	{
		$components = [];

		foreach($this->components as $component) {
			if ($component['app_type'] === $type) {
				$components[$component['id']] = $component;
			}
		}

		return $components;
	}

	public function updateComponents(array $data)
	{
		$components = Json::decode($data['components'], true);

		foreach ($components as $componentId => $status) {
			$component = $this->getById($componentId);

			$component['apps'] = Json::decode($component['apps'], true);

			if ($status === true) {
				$component['apps'][$data['id']]['enabled'] = true;

				$component['dependencies'] = Json::decode($component['dependencies'], true);

				if (isset($component['dependencies']['packages']) && count($component['dependencies']['packages']) > 0) {

					foreach ($component['dependencies']['packages'] as $key => $dependencyPackage) {

						$package = $this->modules->packages->getNamedPackageForRepo($dependencyPackage['name'], $dependencyPackage['repo']);

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
}