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
				$appInfo = $container->getShared('apps')->getAppInfo();
				$events = $container->getShared('events');
				$components = $container->getShared('modules')->components;
				$helper = $container->getShared('helper');
				return (new Dispatcher($appInfo, $events, $components, $helper))->init();
			}
		);
	}
}
