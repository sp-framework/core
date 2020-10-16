<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\EmailServiceProvider\Email;

class EmailServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'email',
			function () use ($container) {
				return (new Email($container))->init();
			}
		);
	}
}