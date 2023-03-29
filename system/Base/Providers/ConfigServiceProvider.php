<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ConfigServiceProvider\Config;

class ConfigServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'config',
			function () use ($container) {
				$session = $container->getShared('session');
				return (new Config($session))->getConfigs();
			}
		);
	}
}