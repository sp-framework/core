<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\SessionServiceProvider\Connection;
use System\Base\Providers\SessionServiceProvider\Session;

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
			function () {
				return (new Connection())->init();
			}
		);
	}
}