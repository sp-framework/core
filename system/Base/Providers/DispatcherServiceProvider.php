<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DispatcherServiceProvider\Dispatcher;

class DispatcherServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$applicationInfo =
			$container->getShared('modules')->applications->getApplicationInfo();

		$config = $container->getShared('config');

		$container->setShared(
			'dispatcher',
			function () use ($applicationInfo, $config) {
				return (new Dispatcher($applicationInfo, $config))->init();
			}
		);
	}
}
