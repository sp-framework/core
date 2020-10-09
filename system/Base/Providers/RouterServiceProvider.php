<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\RouterServiceProvider\Router;

class RouterServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'router',
			function () use ($container) {
				return (new Router($container))->init();
			}
		);
	}
}