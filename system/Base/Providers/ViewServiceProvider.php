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
		$cache = $container->getShared('modules')->views->getCache();
		$compiledPath = $container->getShared('modules')->views->getVoltCompiledPath();

		$container->setShared(
			'volt',
			function(ViewBaseInterface $view) use ($container, $cache, $compiledPath) {
				return (new Volt($view))->init($container, $cache, $compiledPath);
			}
		);

		$views = $container->getShared('modules')->views->init();

		$container->setShared(
			'view',
			function () use ($views) {
				return (new View($views))->init();
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

		$application = $container->getShared('modules')->applications->getApplicationInfo();
		$tags = $container->getShared('modules')->views->getViewTags();
		$view = $container->getShared('view');
		$tag = $container->getShared('tag');
		$escaper = $container->getShared('escaper');
		$links = $container->getShared('links');

		if ($tags) {
			$tagsName = strtolower($tags['name']);

			$package = 'Applications\\' . ucfirst($application['name']) . '\\' . 'Packages\\' . $tags['name'];

			$container->setShared(
				$tagsName,
				function () use ($package, $view, $tag, $links, $escaper) {
					return new $package($view, $tag, $links, $escaper);
				}
			);
		}
	}
}
