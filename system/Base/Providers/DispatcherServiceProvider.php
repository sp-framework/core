<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DispatcherServiceProvider\Dispatcher;

class DispatcherServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'dispatcher',
			function () use ($container) {
				$applicationInfo = $container->getShared('modules')->applications->getApplicationInfo();
				$config = $container->getShared('config');
				$events = $container->getShared('events');
				return (new Dispatcher($applicationInfo, $config, $events))->init();
			}
		);
	}
}
