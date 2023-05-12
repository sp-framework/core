<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesMiddlewares;

class Middlewares extends BasePackage
{
	protected $modelToUse = ModulesMiddlewares::class;

	public $middlewares;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getMiddlewareByNameForAppId($name, $appId)
	{
		foreach($this->middlewares as $middleware) {
			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if (isset($middleware['apps'][$appId]) &&
				$middleware['apps'][$appId]['enabled'] === true &&
				strtolower($middleware['name']) === strtolower($name)
			) {
				$middleware['sequence'] = $middleware['apps'][$appId]['sequence'];
				$middleware['enabled'] = $middleware['apps'][$appId]['enabled'];

				return $middleware;
			}
		}

		return false;
	}

	public function getMiddlewareById($id)
	{
		foreach($this->middlewares as $middleware) {
			if ($middleware['id'] == $id) {
				return $middleware;
			}
		}

		return false;
	}

	public function getMiddlewareByRepo($repo)
	{
		foreach($this->middlewares as $middleware) {
			if ($middleware['repo'] == $repo) {
				return $middleware;
			}
		}

		return false;
	}

	public function getMiddlewaresByApiId($apiId)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			if ($middleware['api_id'] == $apiId) {
				array_push($middlewares, $middleware);
			}
		}

		return $middlewares;
	}

	public function getMiddlewaresForAppId($appId)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if (isset($middleware['apps'][$appId]) &&
				$middleware['apps'][$appId]['enabled'] == true
			) {
				$middlewares[$middleware['id']] = $middleware;
				$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$appId]['sequence'];
				$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$appId]['enabled'];
			}
		}

		return $middlewares;
	}

	public function getMiddlewaresForCategoryAndSubcategory($category, $appId = null)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {

			if ($middleware['category'] === $category) {
				$middlewares[$middleware['id']] = $middleware;

				if ($appId) {
					$middleware['apps'] = Json::decode($middleware['apps'], true);
					if (isset($middleware['apps'][$appId])) {
						if (isset($middleware['apps'][$appId]['sequence'])) {
							$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$appId]['sequence'];
						} else {
							$middlewares[$middleware['id']]['sequence'] = 0;
						}
						if ($middleware['apps'][$appId]['enabled']) {
							$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$appId]['enabled'];
						} else {
							$middlewares[$middleware['id']]['enabled'] = false;
						}
					} else {
						$middlewares[$middleware['id']]['sequence'] = 0;
						$middlewares[$middleware['id']]['enabled'] = false;
					}
				}
			}
		}

		return $middlewares;
	}

	public function getMiddlewaresForAppType($appType, $appId = null)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			if ($middleware['app_type'] == $appType) {
				$middlewares[$middleware['id']] = $middleware;

				if ($appId) {
					$middleware['apps'] = Json::decode($middleware['apps'], true);
					if (isset($middleware['apps'][$appId])) {
						if (isset($middleware['apps'][$appId]['sequence'])) {
							$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$appId]['sequence'];
						} else {
							$middlewares[$middleware['id']]['sequence'] = 0;
						}
						if ($middleware['apps'][$appId]['enabled']) {
							$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$appId]['enabled'];
						} else {
							$middlewares[$middleware['id']]['enabled'] = false;
						}
					} else {
						$middlewares[$middleware['id']]['sequence'] = 0;
						$middlewares[$middleware['id']]['enabled'] = false;
					}
				}
			}
		}

		return $middlewares;
	}

	public function updateMiddlewares(array $data)
	{
		$dependencyArray = [];

		$middlewares = Json::decode($data['middlewares'], true);

		foreach ($middlewares['middlewares'] as $middlewareId => $status) {
			$middleware = $this->getById($middlewareId);

			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if ($status === true) {
				$middleware['apps'][$data['id']]['enabled'] = true;

				$dependencyArray = 
					array_merge($dependencyArray, $this->checkMiddlewareDependencies($data, $middlewares, $middleware));
			} else if ($status === false) {
				$middleware['apps'][$data['id']]['enabled'] = false;
			}

			if (in_array($middlewareId, $dependencyArray)) {
				$middleware['apps'][$data['id']]['enabled'] = true;				
			}

			$middleware['apps'] = Json::encode($middleware['apps']);

			$this->update($middleware);
		}

		//For middlewars that are not system based.
		//Change this number to reflect the number of system middlewares in future.
		$nonSystemMiddlewaresSeqStart = 5;

		foreach ($middlewares['sequence'] as $sequence => $middlewareId) {
			$middleware = $this->getById($middlewareId);

			$middleware['apps'] = Json::decode($middleware['apps'], true);

			//System Middlewares
			if ($middleware['name'] === 'IpFilter') {
				$middleware['apps'][$data['id']]['sequence'] = 0;
			} else if ($middleware['name'] === 'Maintenance') {
				$middleware['apps'][$data['id']]['sequence'] = 1;
			} else if ($middleware['name'] === 'Auth') {
				$middleware['apps'][$data['id']]['sequence'] = 2;
			} else if ($middleware['name'] === 'AgentCheck') {
				$middleware['apps'][$data['id']]['sequence'] = 3;
			} else if ($middleware['name'] === 'Acl') {
				$middleware['apps'][$data['id']]['sequence'] = 4;
			} else {
				//Non System Middlewares
				$middleware['apps'][$data['id']]['sequence'] = $nonSystemMiddlewaresSeqStart;
				$nonSystemMiddlewaresSeqStart++;
			}

			$middleware['apps'] = Json::encode($middleware['apps']);

			$this->update($middleware);
		}
	}

	protected function checkMiddlewareDependencies($data, &$middlewares, &$middleware)
	{
		$dependencyArray = [];

		if (is_string($middleware['settings'])) {
			$middleware['settings'] = Json::decode($middleware['settings'], true);
		}

		if (!isset($middleware['settings']['dependencies'])) {
			return $dependencyArray;
		} 

		if (is_array($middleware['settings']['dependencies']) && count($middleware['settings']['dependencies']) === 0) {
			return $dependencyArray;
		}

		foreach ($middleware['settings']['dependencies'] as $key => $dependency) {
			$dependencyMiddleware = $this->getFirst('name', $dependency, false, true, null, [], true);

			if ($dependencyMiddleware) {
				$dependencyMiddleware['apps'] = Json::decode($dependencyMiddleware['apps'], true);

				$dependencyMiddleware['apps'][$data['id']]['enabled'] = true;

				$dependencyMiddleware['apps'] = Json::encode($dependencyMiddleware['apps']);

				$middlewares['middlewares'][$dependencyMiddleware['id']] = true;

				array_push($dependencyArray, $dependencyMiddleware['id']);

				$this->update($dependencyMiddleware);				
			}
		}

		return $dependencyArray;
	}
}