<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\SessionServiceProvider\Connection;
use System\Base\Providers\SessionServiceProvider\Session;
use System\Base\Providers\SessionServiceProvider\SessionTools;

class SessionServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'session',
			function () {
				return (new Session())->init();
			}
		);

		$container->setShared(
			'connection',
			function () use ($container) {
				$helper = $container->getShared('helper');
				return (new Connection($helper))->init();
			}
		);

		$container->setShared(
			'sessionTools',
			function () use ($container) {
				$session = $container->getShared('session');
				$connection = $container->getShared('connection');
				$localContent = $container->getShared('localContent');
				return new SessionTools($session, $connection, $localContent);
			}
		);
	}
}