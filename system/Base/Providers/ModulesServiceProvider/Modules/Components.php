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

	public function getNamedComponentForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($component) use ($name, $applicationId) {
					if ($component->name === ucfirst($name) &&
						$component->application_id === $applicationId
					) {
						return $component;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate component name found for component ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function getComponentsForApplication($applicationId)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($applicationId) {
					if ($component->application_id === $applicationId) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			array_push($components, $value->toArray());
		}

		return $components;
	}

	public function getComponentsForType($type)
	{
		$components = [];

		$filter =
			$this->model->filter(
				function($component) use ($type) {
					if ($component->type === $type) {
						return $component;
					}
				}
			);

		foreach ($filter as $key => $value) {
			array_push($components, $value->toArray());
		}

		return $components;
	}
}