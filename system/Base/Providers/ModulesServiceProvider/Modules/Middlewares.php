<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Middlewares as MiddlewaresModel;

class Middlewares extends BasePackage
{
	protected $modelToUse = MiddlewaresModel::class;

	public $middlewares;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getNamedMiddlewareForApplication($name, $applicationId)
	{
		$filter =
			$this->model->filter(
				function($middleware) use ($name, $applicationId) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);
					if ($middleware['applications'][$applicationId]['installed'] === true &&
						$middleware['name'] === ucfirst($name)
					) {
						return $middleware;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate middleware name found for middleware ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getMiddlewaresForApplication($applicationId)
	{
		$filters =
			$this->model->filter(
				function($middleware) use ($applicationId) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);
					if (isset($middleware['applications'][$applicationId]['enabled']) &&
						$middleware['applications'][$applicationId]['enabled'] === true
					) {
						return $middleware;
					}
				}
			);

		$middlewares = [];

		foreach ($filters as $key => $filter) {
			$middlewares[$key] = $filter;
			$middlewares[$key]['sequence'] = $filter['applications'][$applicationId]['sequence'];
			$middlewares[$key]['enabled'] = $filter['applications'][$applicationId]['enabled'];
		}

		return $middlewares;
	}

	public function getMiddlewaresForCategoryAndSubcategory($category, $subCategory, $applicationId = null, $inclCommon = true)
	{
		$middlewares = [];

		$filter =
			$this->model->filter(
				function($middleware) use ($category, $subCategory, $inclCommon) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);
					if ($inclCommon) {
						if (($middleware['category'] === $category && $middleware['sub_category'] === $subCategory) ||
							($middleware['category'] === $category && $middleware['sub_category'] === 'common')
						) {
							return $middleware;
						}
					} else {
						if ($middleware['category'] === $category && $middleware['sub_category'] === $subCategory) {
							return $middleware;
						}
					}
				}
			);

		foreach ($filter as $key => $value) {
			$middlewares[$key] = $value;

			if ($applicationId) {
				if (isset($filter[$key]['applications'][$applicationId])) {
					if (isset($filter[$key]['applications'][$applicationId]['sequence'])) {
						$middlewares[$key]['sequence'] = $filter[$key]['applications'][$applicationId]['sequence'];
					} else {
						$middlewares[$key]['sequence'] = 0;
					}
					if ($filter[$key]['applications'][$applicationId]['enabled']) {
						$middlewares[$key]['enabled'] = $filter[$key]['applications'][$applicationId]['enabled'];
					} else {
						$middlewares[$key]['enabled'] = false;
					}
				} else {
					$middlewares[$key]['sequence'] = 0;
					$middlewares[$key]['enabled'] = false;
				}
			}
		}

		return $middlewares;
	}

	public function getMiddlewaresForAppType(string $type, $applicationId = null)
	{
		$middlewares = [];

		$filter =
			$this->model->filter(
				function($middleware) use ($type) {
					$middleware = $middleware->toArray();
					$middleware['applications'] = Json::decode($middleware['applications'], true);

					if ($middleware['app_type'] === $type) {
						return $middleware;
					}
				}
			);

		foreach ($filter as $key => $value) {
			$middlewares[$key] = $value;

			if ($applicationId) {
				if (isset($filter[$key]['applications'][$applicationId])) {
					if (isset($filter[$key]['applications'][$applicationId]['sequence'])) {
						$middlewares[$key]['sequence'] = $filter[$key]['applications'][$applicationId]['sequence'];
					} else {
						$middlewares[$key]['sequence'] = 0;
					}
					if ($filter[$key]['applications'][$applicationId]['enabled']) {
						$middlewares[$key]['enabled'] = $filter[$key]['applications'][$applicationId]['enabled'];
					} else {
						$middlewares[$key]['enabled'] = false;
					}
				} else {
					$middlewares[$key]['sequence'] = 0;
					$middlewares[$key]['enabled'] = false;
				}
			}
		}

		return $middlewares;
	}

	public function updateMiddlewares(array $data)
	{
		$middlewares = Json::decode($data['middlewares'], true);

		foreach ($middlewares['middlewares'] as $middlewareId => $status) {
			$middleware = $this->getById($middlewareId);

			$middleware['applications'] = Json::decode($middleware['applications'], true);

			if ($status === true) {
				$middleware['applications'][$data['id']]['enabled'] = true;
			} else if ($status === false) {
				$middleware['applications'][$data['id']]['enabled'] = false;
			}

			$middleware['applications'] = Json::encode($middleware['applications']);

			$this->update($middleware);
		}

		foreach ($middlewares['sequence'] as $sequence => $middlewareId) {
			$middleware = $this->getById($middlewareId);

			$middleware['applications'] = Json::decode($middleware['applications'], true);

			if ($status === true) {
				$middleware['applications'][$data['id']]['sequence'] = $sequence;
			} else if ($status === false) {
				$middleware['applications'][$data['id']]['sequence'] = $sequence;
			}

			$middleware['applications'] = Json::encode($middleware['applications']);

			$this->update($middleware);
		}

		return true;
	}
}