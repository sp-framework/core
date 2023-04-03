<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DatabaseServiceProvider\ModelsManager;
use System\Base\Providers\DatabaseServiceProvider\Pdo;
use System\Base\Providers\DatabaseServiceProvider\PdoCli;

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		if (PHP_SAPI === 'cli') {

			$container->setShared(
				'db',
				function () use ($container) {
					$dbConfig = $container->getShared('config')->db;
					$localContent = $container->getShared('localContent');
					$crypt = $container->getShared('crypt');
					return (new PdoCli($dbConfig, $localContent, $crypt))->init();
				}
			);

			$container->setShared(
				'modelsManager',
				function () {
					return (new ModelsManager())->init();
				}
			);

			return;
		}

		$container->setShared(
			'db',
			function () use ($container) {
				$dbConfig = $container->getShared('config')->db;
				$session = $container->getShared('session');
				$localContent = $container->getShared('localContent');
				$crypt = $container->getShared('crypt');
				return (new Pdo($dbConfig, $session, $localContent, $crypt))->init();
			}
		);

		$container->setShared(
			'modelsManager',
			function () {
				return (new ModelsManager())->init();
			}
		);
	}
}
