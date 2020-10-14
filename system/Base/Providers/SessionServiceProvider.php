<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\SessionServiceProvider\Session;

class SessionServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		include('../system/Base/Providers/SessionServiceProvider/Session.php');

		$container->setShared(
			'session',
			function () use ($container) {
				return (new Session($container))->init();
			}
		);
	}
}