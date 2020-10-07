<?php

namespace System\Base;

use Phalcon\Di\DiInterface;

abstract class BasePackage
{
	protected $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

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