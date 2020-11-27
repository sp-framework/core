<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DatabaseServiceProvider\GeoPdo;
use System\Base\Providers\DatabaseServiceProvider\Pdo;

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'db',
			function () use ($container) {
				$dbConfig = $container->getShared('config')->db;
				return (new Pdo($dbConfig))->init();
			}
		);

		$container->setShared(
			'geodb',
			function () use ($container) {
				$dbConfig = $container->getShared('config')->db;
				return (new GeoPdo($dbConfig))->init();
			}
		);
	}
}
