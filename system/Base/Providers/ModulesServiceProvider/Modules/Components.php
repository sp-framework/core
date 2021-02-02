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
		$filter =
			$this->model->filter(
				function($component) use ($name) {
					$component = $component->toArray();

					if ($component['name'] === $name) {
						return $component;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate component route found for component ' . $route);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getRouteComponentForApp($route, $appId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($route, $appId) {
					$component = $component->toArray();

					$component['apps'] = Json::decode($component['apps'], true);

					if (isset($component['apps'][$appId])) {
						if ($component['apps'][$appId]['enabled'] === true &&
							$component['route'] === $route
						) {
							return $component;
						}
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate component route found for component ' . $route);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamedComponentForApp($name, $appId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($name, $appId) {
					$component = $component->toArray();
					$component['apps'] = Json::decode($component['apps'], true);

					if (isset($component['apps'][$appId])) {
						if ($component['apps'][$appId]['enabled'] === true &&
							$component['name'] === ucfirst($name)
						) {
							return $component;
						}
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate component name found for component ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getComponentsForApp($appId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($appId) {
					$component = $component->toArray();
					$component['apps'] = Json::decode($component['apps'], true);
					if (isset($component['apps'][$appId]['enabled']) &&
						$component['apps'][$appId]['enabled'] === true
					) {
						return $component;
					}
				}
			);

		return $filter;
	}

	public function getComponentsForAppAndType($appId, $type)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($appId, $type) {
					if ($component->app_id === $appId && $component->type === $type) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$components[$key] = $value->toArray();
		}

		return $components;
	}

	public function getComponentById($id)
	{
		$filter =
			$this->model->filter(
				function($component) use ($id) {
					if ($component->id == $id) {
						return $component;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate component Id found for id ' . $id);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function getComponentsForCategoryAndSubcategory($category, $subCategory, $inclCommon = true)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($category, $subCategory, $inclCommon) {
					$component = $component->toArray();
					if ($inclCommon) {
						if (($component['category'] === $category && $component['sub_category'] === $subCategory) ||
							($component['category'] === $category && $component['sub_category'] === 'common')
						) {
							return $component;
						}
					} else {
						if ($component['category'] === $category && $component['sub_category'] === $subCategory) {
							return $component;
						}
					}
				}
			);

		foreach ($filter as $key => $value) {
			$components[$key] = $value;
		}
		return $components;
	}

	public function getComponentsForAppType(string $type)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($type) {
					$component = $component->toArray();
					if ($component['app_type'] === $type) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$components[$key] = $value;
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

				$component['dependencies'] = Json::encode($component['dependencies']);

			} else if ($status === false) {
				$component['apps'][$data['id']]['enabled'] = false;
			}

			// if ($component['menu_id']) {
			// 	$this->updateMenu($data, $component, $status);
			// }

			$component['apps'] = Json::encode($component['apps']);

			$this->update($component);
		}

		return true;
	}

	// protected function updateMenu($data, $component, $status)
	// {
	// 	$menu = $this->basepackages->menus->getById($component['menu_id']);

	// 	$menu['apps'] = Json::decode($menu['apps'], true);

	// 	if ($status === true) {
	// 		$menu['apps'][$data['id']]['enabled'] = true;
	// 	} else if ($status === false) {
	// 		$menu['apps'][$data['id']]['enabled'] = false;
	// 	}

	// 	$menu['apps'] = Json::encode($menu['apps']);

	// 	$this->basepackages->menus->update($menu);

	// 	$this->basepackages->menus->init(true);
	// }
}