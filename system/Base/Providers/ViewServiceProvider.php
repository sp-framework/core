<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\ViewBaseInterface;
use System\Base\Providers\ViewServiceProvider\Tag;
use System\Base\Providers\ViewServiceProvider\View;
use System\Base\Providers\ViewServiceProvider\Volt;

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
			'tag',
			function () {
				return (new Tag())->init();
			}
		);
	}
}
