<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\RouterServiceProvider\Router;

class RouterServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$api = $container->getShared('api');
		$request = $container->getShared('request');

		$container->setShared(
			'router',
			function () use ($api, $request, $container) {
				$domains = $container->getShared('domains');
				$apps = $container->getShared('apps');
				if (!$api->isApi()) {
					$components = $container->getShared('modules')->components;
					$views = $container->getShared('modules')->views;
				} else {
					$components = null;
					$views = null;
				}
				$logger = $container->getShared('logger');
				$response = $container->getShared('response');
				$helper = $container->getShared('helper');
				$config = $container->getShared('config');
				$basepackages = $container->getShared('basepackages');
				return (new Router($api, $domains, $apps, $components, $views, $logger, $request, $response, $helper, $config, $basepackages))->init();
			}
		);
	}
}