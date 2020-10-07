<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ConfigServiceProvider\Config;

class ConfigServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		include('../system/Base/Providers/ConfigServiceProvider/Config.php');

		$container->setShared(
			'config',
			function () use ($container) {
				return (new Config($container))->getConfigs();
			}
		);
	}
}