<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\EmailServiceProvider\Email;
use System\Base\Providers\EmailServiceProvider\EmailServices;

class EmailServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'email',
			function () use ($container) {
				$application = $container->getShared('modules')->applications->getApplicationInfo();
				$domain = $container->getShared('basepackages')->domains->getDomain();
				return (new Email($application, $domain))->init();
			}
		);

		$container->setShared(
			'emailservices',
			function () {
				return (new EmailServices())->init();
			}
		);
	}
}