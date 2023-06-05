<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\DatabaseServiceProvider\Ff;
use System\Base\Providers\DatabaseServiceProvider\ModelsManager;
use System\Base\Providers\DatabaseServiceProvider\Pdo;
use System\Base\Providers\DatabaseServiceProvider\PdoCli;
use System\Base\Providers\DatabaseServiceProvider\Sqlite;

class DatabaseServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$config = $container->getShared('config');

		$container->setShared(
			'db',
			function () use ($container, $config) {
				$dbConfig = $config->db;
				$session = $container->getShared('session');
				$crypt = $container->getShared('crypt');
				$localContent = $container->getShared('localContent');

				if (PHP_SAPI === 'cli') {
					return (new PdoCli($dbConfig, $localContent, $crypt))->init();
				} else {
					return (new Pdo($config, $session, $localContent, $crypt))->init();
				}
			}
		);

		$container->setShared(
			'modelsManager',
			function () {
				return (new ModelsManager())->init();
			}
		);

		$container->setShared(
			'ff',
			function () use ($container, $config) {
				$cacheConfig = $config->cache;

				return (new Ff($cacheConfig))->init();
			}
		);

		$container->setShared(
			'sqlite',
			function () {
				return (new Sqlite())->init();
			}
		);
	}
}
