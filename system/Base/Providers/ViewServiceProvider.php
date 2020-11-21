<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\ViewBaseInterface;
use System\Base\Providers\ViewServiceProvider\Escaper;
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
			function(ViewBaseInterface $view) use ($container) {
				$cache = $container->getShared('modules')->views->getCache();
				$compiledPath = $container->getShared('modules')->views->getVoltCompiledPath();
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
			'voltTools',
			function () use ($container) {
				return new VoltTools($container);
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
			$application = $container->getShared('modules')->applications->getApplicationInfo();
			$tagsName = strtolower($tags['name']);

			$package = 'Applications\\' . ucfirst($application['name']) . '\\' . 'Packages\\' . $tags['name'] . '\\' . $tags['name'];

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
