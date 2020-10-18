<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\EmailServiceProvider\Email;

class EmailServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$application = $container->getShared('modules')->applications->getApplicationInfo();

		$container->setShared(
			'email',
			function () use ($application) {
				return (new Email($application))->init();
			}
		);
	}
}