<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\LoggerServiceProvider\Logger;

class LoggerServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		include('../system/Base/Providers/LoggerServiceProvider/Logger.php');

		$container->setShared(
			'logger',
			function () use ($container) {
				$logger = (new Logger($container))->init();

				return $logger;
			}
		);
	}
}