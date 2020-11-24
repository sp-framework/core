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
				$domains = $container->getShared('basepackages')->domains;
				$applications = $container->getShared('modules')->applications;
				$components = $container->getShared('modules')->components;
				$views = $container->getShared('modules')->views;
				$logger = $container->getShared('logger');
				$request = $container->getShared('request');
				return (new Router($domains, $applications, $components, $views, $logger, $request))->init();
			}
		);
	}
}