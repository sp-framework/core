<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\FlashServiceProvider\Flash;
use System\Base\Providers\FlashServiceProvider\FlashSession;

class FlashServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'flash',
			function () {
				return (new Flash())->init();
			}
		);

		$container->setShared(
			'flashSession',
			function () {
				return (new FlashSession())->init();
			}
		);
	}
}
