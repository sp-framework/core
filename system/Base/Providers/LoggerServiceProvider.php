<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\LoggerServiceProvider\Logger;

class LoggerServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'logger',
			function () use ($container) {
				$logsConfig = $container->getShared('config')->logs;
				$session = $container->getShared('session');
				$connection = $container->getShared('connection');
				$request = $container->getShared('request');
				$helper = $container->getShared('helper');
				$email = null;
				if (PHP_SAPI !== 'cli') {
					$email = $container->getShared('basepackages')->email;
				}
				return (new Logger($logsConfig, $session, $connection, $request, $helper, $email))->init();
			}
		);
	}
}