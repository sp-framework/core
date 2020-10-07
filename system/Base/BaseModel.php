<?php

namespace System\Base;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

abstract class BaseModel extends Model
{
	public function onConstruct()
	{
		$this->setSource($this->source);

		$this->useDynamicUpdate(true);

	}

	public static function find($parameters = null, $customCacheKey = null, $cacheEnabled = true) : ResultsetInterface
	{
		if ($cacheEnabled) {
			$parameters = self::checkCacheParameters($parameters, $customCacheKey);
		}

		return parent::find($parameters);
	}

	public static function findFirst($parameters = null, $customCacheKey = null, $cacheEnabled = true) : ResultsetInterface
	{
		if ($cacheEnabled) {
			$parameters = self::checkCacheParameters($parameters, $customCacheKey);
		}

		return parent::findFirst($parameters);
	}

	protected static function checkCacheParameters($parameters = null, $customCacheKey = null)
	{
		if ($parameters) {
			$cacheName = clone $parameters;
		} else if ($customCacheKey) {
			$cacheName = $customCacheKey;
		} else {
			return null;
		}

		if (!is_array($cacheName)) {
			$cacheName = [$cacheName];
		}

		if (!isset($parameters['cache'])) {
			$parameters['cache'] = [
				'key'      	=> self::generateCacheKey($cacheName),
				'lifetime' 	=> 60,
				'service' 	=> 'modulesCache',
			];
		}

		return $parameters;
	}

	protected static function generateCacheKey($cacheName)
	{

		$uniqueKey = [];

		foreach ($cacheName as $key => $value) {
			if (true === is_scalar($value)) {
				$uniqueKey[] = $key . '-' . $value;
			} elseif (true === is_array($value)) {
				$uniqueKey[] = sprintf(
					'%s:[%s]',
					$key,
					self::generateCacheKey($value)
				);
			}
		}

		return join(',', $uniqueKey);
	}
}