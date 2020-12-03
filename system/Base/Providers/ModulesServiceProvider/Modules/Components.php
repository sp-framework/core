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
					if ($component['applications'][$applicationId]['installed'] === true &&
						$component['route'] === $route
					) {
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

	public function getNamedComponentForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($name, $applicationId) {
					$component = $component->toArray();
					$component['applications'] = Json::decode($component['applications'], true);
					if ($component['applications'][$applicationId]['installed'] === true &&
						$component['name'] === ucfirst($name)
					) {
						return $component;
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
					if (isset($component['applications'][$applicationId]['installed']) &&
						$component['applications'][$applicationId]['installed'] === true
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

	public function getComponentsForCategoryAndSubcategory($category, $subCategory)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($category, $subCategory) {
					if ($component->category === $category &&
						$component->sub_category === $subCategory
					) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$components[$key] = $value->toArray();
		}

		return $components;
	}
}