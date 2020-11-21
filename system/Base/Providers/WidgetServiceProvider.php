<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\WidgetServiceProvider\Widget;

class WidgetServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{

		$container->setShared(
			'widget',
			function () use ($container) {
				$connection = $container->getShared('connection');
				$session = $container->getShared('session');
				$request = $container->getShared('request');
				$remoteContent = $container->getShared('remoteContent');
				$logger = $container->getShared('logger');
				return (new Widget($session, $connection, $request, $remoteContent, $logger))->init();
			}
		);
	}
}