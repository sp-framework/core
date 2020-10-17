<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\RouterServiceProvider\Router;

class RouterServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$domains = $container->getShared('modules')->domains;

		$applications = $container->getShared('modules')->applications;

		$logger = $container->getShared('logger');

		$request = $container->getShared('request');

		$container->setShared(
			'router',
			function () use ($domains, $applications, $logger, $request) {
				return (new Router($domains, $applications, $logger, $request))->init();
			}
		);
	}
}