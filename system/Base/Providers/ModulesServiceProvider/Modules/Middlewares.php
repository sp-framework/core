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
					if (isset($middleware['applications'][$applicationId]['installed']) &&
						$middleware['applications'][$applicationId]['installed'] === true
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