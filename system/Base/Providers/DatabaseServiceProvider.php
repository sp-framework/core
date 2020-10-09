<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DatabaseServiceProvider\Pdo;

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'db',
			function () use ($container) {
				return (new Pdo($container))->init();
			}
		);
	}
}
