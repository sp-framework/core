<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Controller;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages\PackagesData;

abstract class BasePackage extends Controller
{
	protected $container;

	protected $packagesData = [];

	public function onConstruct()
	{
		$this->packagesData = new PackagesData;
	}

	protected function usePackage($packageClass)
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if ($this->checkPackage($packageClass)) {
			return new $packageClass($this->container);
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		$packageName = Arr::last(explode('\\', $packageClass));

		$packageApplicationId =
			$this->packages[array_search($packageName, array_column($this->packages, 'name'))]['application_id'];

		if ($packageApplicationId === $this->application['id']) {
			return true;
		} else {
			return false;
		}
	}
	// public function __get($name)
	// {
	// 	if (isset($this->{$name})) {
	// 		return $this->{$name};
	// 	}
	// }
	// public function onConstruct()
	// {
	// 	$this->setSource($this->source);

	// 	$this->useDynamicUpdate(true);

	// }

	// public static function find($parameters = null)
	// {
	// 	$parameters = self::checkCacheParameters($parameters);

	// 	return parent::find($parameters);
	// }

	// public static function findFirst($parameters = null)
	// {
	// 	$parameters = self::checkCacheParameters($parameters);

	// 	return parent::findFirst($parameters);
	// }

	// protected static function checkCacheParameters($parameters = null)
	// {
	// 	if (null !== $parameters) {
	// 		if (true !== is_array($parameters)) {
	// 			$parameters = [$parameters];
	// 		}

	// 		if (true !== isset($parameters['cache'])) {
	// 			$parameters['cache'] = [
	// 				'key'      => self::generateCacheKey($parameters),
	// 				'lifetime' => 300,
	// 			];
	// 		}
	// 	}

	// 	return $parameters;
	// }

	// protected static function generateCacheKey(array $parameters)
	// {
	// 	$uniqueKey = [];

	// 	foreach ($parameters as $key => $value) {
	// 		if (true === is_scalar($value)) {
	// 			$uniqueKey[] = $key . ':' . $value;
	// 		} elseif (true === is_array($value)) {
	// 			$uniqueKey[] = sprintf(
	// 				'%s:[%s]',
	// 				$key,
	// 				self::generateCacheKey($value)
	// 			);
	// 		}
	// 	}

	// 	return join(',', $uniqueKey);
	// }
}