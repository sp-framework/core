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
		$request = $container->getShared('request');

		if ($config->databasetype === 'db') {
			$container->setShared(
				'db',
				function () use ($container, $config) {
					$dbConfig = $config->db;
					$session = $container->getShared('session');
					$localContent = $container->getShared('localContent');
					$crypt = $container->getShared('crypt');
					$helper = $container->getShared('helper');

					if (PHP_SAPI === 'cli') {
						return (new PdoCli($dbConfig, $localContent, $crypt, $helper))->init();
					} else {
						return (new Pdo($config, $session, $localContent, $crypt, $helper))->init();
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
				'sqlite',
				function () {
					return (new Sqlite())->init();
				}
			);
		} else {
			if ($config->databasetype === 'hybrid') {
				$container->setShared(
					'ff',
					function () use ($container, $config, $request) {
						$session = $container->getShared('session');
						$localContent = $container->getShared('localContent');
						$crypt = $container->getShared('crypt');
						$helper = $container->getShared('helper');

						$db = (new Pdo($config, $session, $localContent, $crypt, $helper))->init();

						$container->setShared('db', $db);

						$basepackages = $container->getShared('basepackages');

						return (new Ff($config, $request, $db, $basepackages))->init();
					}
				);
			} else {
				$container->setShared(
					'ff',
					function () use ($config, $request) {
						return (new Ff($config, $request))->init();
					}
				);
			}
		}
	}
}
