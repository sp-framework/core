<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Components as ComponentsModel;

class Components extends BasePackage
{
	protected $modelToUse = ComponentsModel::class;

	public $components;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getRouteComponentForApplication($route, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($route, $applicationId) {
					$component = $component->toArray();

					$component['applications'] = Json::decode($component['applications'], true);

					if (isset($component['applications'][$applicationId])) {
						if ($component['applications'][$applicationId]['enabled'] === true &&
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

	public function getNamedComponentForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($name, $applicationId) {
					$component = $component->toArray();
					$component['applications'] = Json::decode($component['applications'], true);

					if (isset($component['applications'][$applicationId])) {
						if ($component['applications'][$applicationId]['enabled'] === true &&
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

	public function getComponentsForApplication($applicationId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($applicationId) {
					$component = $component->toArray();
					$component['applications'] = Json::decode($component['applications'], true);
					if (isset($component['applications'][$applicationId]['enabled']) &&
						$component['applications'][$applicationId]['enabled'] === true
					) {
						return $component;
					}
				}
			);

		return $filter;
	}

	public function getComponentsForApplicationAndType($applicationId, $type)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($applicationId, $type) {
					if ($component->application_id === $applicationId && $component->type === $type) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$components[$key] = $value->toArray();
		}

		return $components;
	}

	public function getIdComponent($id)
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

	public function updateComponents(array $data)
	{
		$components = Json::decode($data['components'], true);

		foreach ($components as $componentId => $status) {
			$component = $this->getById($componentId);

			$component['applications'] = Json::decode($component['applications'], true);

			if ($status === true) {
				$component['applications'][$data['id']]['enabled'] = true;

				$component['dependencies'] = Json::decode($component['dependencies'], true);

				if (isset($component['dependencies']['packages']) && count($component['dependencies']['packages']) > 0) {

					foreach ($component['dependencies']['packages'] as $key => $dependencyPackage) {

						$package = $this->modules->packages->getNamedPackageForRepo($dependencyPackage['name'], $dependencyPackage['repo']);

						if ($package) {
							$package['applications'] = Json::decode($package['applications'], true);

							$package['applications'][$data['id']]['enabled'] = true;

							$package['applications'] = Json::encode($package['applications']);

							$this->modules->packages->update($package);
						}
					}
				}

				$component['dependencies'] = Json::encode($component['dependencies']);

			} else if ($status === false) {
				$component['applications'][$data['id']]['enabled'] = false;
			}

			if ($component['menu_id']) {
				$this->updateMenu($data, $component, $status);
			}

			$component['applications'] = Json::encode($component['applications']);

			$this->update($component);
		}

		return true;
	}

	protected function updateMenu($data, $component, $status)
	{
		$menu = $this->basepackages->menus->getById($component['menu_id']);

		$menu['applications'] = Json::decode($menu['applications'], true);

		if ($status === true) {
			$menu['applications'][$data['id']]['enabled'] = true;
		} else if ($status === false) {
			$menu['applications'][$data['id']]['enabled'] = false;
		}

		$menu['applications'] = Json::encode($menu['applications']);

		$this->basepackages->menus->update($menu);

		$this->basepackages->menus->init(true);
	}
}