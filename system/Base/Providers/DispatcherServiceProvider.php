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
				return (new Dispatcher($container))->init();
			}
		);
	}
}
