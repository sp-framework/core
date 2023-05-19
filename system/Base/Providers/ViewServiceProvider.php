<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\ViewBaseInterface;
use System\Base\Providers\ViewServiceProvider\Escaper;
use System\Base\Providers\ViewServiceProvider\SimpleView;
use System\Base\Providers\ViewServiceProvider\Tag;
use System\Base\Providers\ViewServiceProvider\View;
use System\Base\Providers\ViewServiceProvider\Volt;
use System\Base\Providers\ViewServiceProvider\VoltTools;

class ViewServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'volt',
			function() use ($container) {
				$cache = $container->getShared('modules')->views->getCache();
				$compiledPath = $container->getShared('modules')->views->getVoltCompiledPath();
				$view = $container->getShared('view');
				return (new Volt($view))->init($container, $cache, $compiledPath);
			}
		);

		$container->setShared(
			'view',
			function () use ($container) {
				$views = $container->getShared('modules')->views->init();
				$events = $container->getShared('events');
				return (new View($views, $events))->init();
			}
		);

		$container->setShared(
			'viewSimple',
			function () use ($container) {
				$views = $container->getShared('modules')->views->init();
				return (new SimpleView($views))->init();
			}
		);

		$container->setShared(
			'voltTools',
			function () use ($container) {
				$volt = $container->getShared('volt');
				$view = $container->getShared('view');
				return new VoltTools($volt, $view);
			}
		);
		$container->setShared(
			'tag',
			function () {
				return (new Tag())->init();
			}
		);

		$container->setShared(
			'escaper',
			function () {
				return (new Escaper())->init();
			}
		);

		$tags = $container->getShared('modules')->views->getViewTags();

		if ($tags) {
			$app = $container->getShared('apps')->getAppInfo();
			$tagsName = strtolower($tags['name']);

			$appPackage =
				'Apps\\' .
				ucfirst($app['app_type']) . '\\' .
				'Packages\\' . $tags['name'] . '\\' . $tags['name'];

			try {
				$reflection = new \ReflectionClass($appPackage);
				$package = $appPackage;
			} catch (\Exception $e) {
				throw $e;
			}

			$container->setShared(
				$tagsName,
				function () use ($package, $container) {
					$view = $container->getShared('view');
					$tag = $container->getShared('tag');
					$links = $container->getShared('links');
					$escaper = $container->getShared('escaper');
					return new $package($view, $tag, $links, $escaper);
				}
			);
		}
	}
}
