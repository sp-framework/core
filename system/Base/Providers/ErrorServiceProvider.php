<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ErrorServiceProvider\Error;

class ErrorServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'error',
			function () use ($container) {
				$applicationInfo = $container->getShared('modules')->applications->getApplicationInfo();
				$config = $container->getShared('config');
				$logger = $container->getShared('logger');
				return (new Error($applicationInfo, $config, $logger))->init();
			}
		);
	}
}