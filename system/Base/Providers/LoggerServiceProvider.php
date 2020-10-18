<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\LoggerServiceProvider\Logger;

class LoggerServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$logsConfig = $container->getShared('config')->logs;

		$session = $container->getShared('session');

		$connection = $container->getShared('connection');

		$request = $container->getShared('request');

		$email = $container->getShared('email');

		$container->setShared(
			'logger',
			function () use ($logsConfig, $session, $connection, $request, $email) {
				return (new Logger($logsConfig, $session, $connection, $request, $email))->init();
			}
		);
	}
}