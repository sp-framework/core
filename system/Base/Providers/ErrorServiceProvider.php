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
				$appInfo = $container->getShared('apps')->getAppInfo();
				$config = $container->getShared('config');
				$logger = $container->getShared('logger');
				$request = $container->getShared('request');
				$response = $container->getShared('response');
				$auth = $container->getShared('access')->auth;
				return (new Error($appInfo, $config, $logger, $request, $response, $auth))->init();
			}
		);
	}
}