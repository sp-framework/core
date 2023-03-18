<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Arr;
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

	public function get(array $data = [], bool $resetCache = false)
	{
		if (count($data) === 0) {
			return $this->middlewares;
		}

		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if (isset($data['app_id']) && isset($data['name'])) {
				if (isset($middleware['apps'][$data['app_id']]['enabled']) &&
					$middleware['apps'][$data['app_id']]['enabled'] == true &&
					strtolower($middleware['name']) == strtolower($data['name'])
				) {
					$middleware['sequence'] = $middleware['apps'][$data['app_id']]['sequence'];
					$middleware['enabled'] = $middleware['apps'][$data['app_id']]['enabled'];

					$middlewares[$middleware['id']] = $middleware;
				}
			} else if (isset($data['id'])) {
				if ($middleware['id'] == $data['id']) {
					return $middleware;
				}
			} else if (isset($data['name'])) {
				if (strtolower($middleware['name']) === strtolower($data['name'])) {
					return $middleware;
				}
			} else if (isset($data['category']) && isset($data['sub_category']) ||
				isset($data['app_type'])
			) {
				if (((isset($data['category']) && isset($data['sub_category'])) &&
					$middleware['category'] === $data['category'] &&
					$middleware['sub_category'] === $data['sub_category']) ||
					(isset($data['app_type']) &&
					$middleware['app_type'] === $data['app_type'])
				) {
					$middlewares[$middleware['id']] = $middleware;

					if ($data['app_id']) {
						if (isset($middleware['apps'][$data['app_id']])) {
							if (isset($middleware['apps'][$data['app_id']]['sequence'])) {
								$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$data['app_id']]['sequence'];
							} else {
								$middlewares[$middleware['id']]['sequence'] = 0;
							}
							if ($middleware['apps'][$data['app_id']]['enabled']) {
								$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$data['app_id']]['enabled'];
							} else {
								$middlewares[$middleware['id']]['enabled'] = false;
							}
						} else {
							$middlewares[$middleware['id']]['sequence'] = 0;
							$middlewares[$middleware['id']]['enabled'] = false;
						}
					}
				}
			} else if (isset($data['app_id'])) {
				if (isset($middleware['apps'][$data['app_id']]) &&
					$middleware['apps'][$data['app_id']]['enabled'] == true
				) {
					$middlewares[$middleware['id']] = $middleware;
					$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$data['app_id']]['sequence'];
					$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$data['app_id']]['enabled'];
				}
			}
		}

		return $middlewares;
	}

	public function add(array $data)
	{
		return;
	}

	public function update(array $data)
	{
		return;
	}

	public function remove(array $data)
	{
		return;
	}

	public function updateMiddlewares(array $data)
	{
		$dependencyArray = [];

		$middlewares = Json::decode($data['middlewares'], true);

		foreach ($middlewares['middlewares'] as $middlewareId => $status) {
			$middleware = $this->get(['id' => $middlewareId]);

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
			$middleware = $this->get(['id' => $middlewareId]);

			//System Middlewares
			if ($middleware['name'] === 'Maintenance') {
				$middleware['apps'][$data['id']]['sequence'] = 0;
			} else if ($middleware['name'] === 'IpBlackList') {
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