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

	public function getNamedMiddlewareForApp($name, $appId)
	{
		foreach($this->middlewares as $middleware) {
			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if ($middleware['apps'][$appId]['enabled'] === true &&
				strtolower($middleware['name']) === strtolower($name)
			) {
				return $middleware;
			}
		}

		return false;
	}

	public function getMiddlewaresForApp($appId)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if (isset($middleware['apps'][$appId]['enabled']) &&
				$middleware['apps'][$appId]['enabled'] == true
			) {
				$middlewares[$middleware['id']] = $middleware;
				$middlewares[$middleware['id']]['sequence'] = $middleware['apps'][$appId]['sequence'];
				$middlewares[$middleware['id']]['enabled'] = $middleware['apps'][$appId]['enabled'];
			}
		}

		return $middlewares;
	}

	public function getMiddlewaresForCategoryAndSubcategory($category, $subCategory, $appId = null)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {

			if ($middleware['category'] === $category && $middleware['sub_category'] === $subCategory) {
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

	public function getMiddlewaresForAppType(string $type, $appId = null)
	{
		$middlewares = [];

		foreach($this->middlewares as $middleware) {
			if ($middleware['app_type'] == $type) {
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
		$middlewares = Json::decode($data['middlewares'], true);

		foreach ($middlewares['middlewares'] as $middlewareId => $status) {
			$middleware = $this->getById($middlewareId);

			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if ($status === true) {
				$middleware['apps'][$data['id']]['enabled'] = true;
			} else if ($status === false) {
				$middleware['apps'][$data['id']]['enabled'] = false;
			}

			$middleware['apps'] = Json::encode($middleware['apps']);

			$this->update($middleware);
		}

		foreach ($middlewares['sequence'] as $sequence => $middlewareId) {
			$middleware = $this->getById($middlewareId);

			$middleware['apps'] = Json::decode($middleware['apps'], true);

			if ($status === true) {
				$middleware['apps'][$data['id']]['sequence'] = $sequence;
			} else if ($status === false) {
				$middleware['apps'][$data['id']]['sequence'] = $sequence;
			}

			$middleware['apps'] = Json::encode($middleware['apps']);

			$this->update($middleware);
		}

		return true;
	}
}