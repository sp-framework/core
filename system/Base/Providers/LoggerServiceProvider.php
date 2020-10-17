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

		$request = $container->getShared('request');

		$email = $container->getShared('email');

		$core = $container->getShared('modules')->core->core[0];

		$container->setShared(
			'logger',
			function () use ($logsConfig, $session, $request, $email, $core) {
				return (new Logger($logsConfig, $session, $request, $email, $core))->init();
			}
		);
	}
}