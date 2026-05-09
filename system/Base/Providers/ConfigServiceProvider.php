<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use System\Base\Providers\ConfigServiceProvider\Config;

class ConfigServiceProvider implements ServiceProviderInterface
{
	protected $cliArgvs;

	public function __construct($argvs = null)
	{
		$this->cliArgvs = $argvs;
	}

	public function register(DiInterface $container) : void
	{
		$cliArgvs = $this->cliArgvs;

		$container->setShared(
			'config',
			function () use ($container, $cliArgvs) {
				$session = $container->getShared('session');
				$request = $container->getShared('request');
				return (new Config($session, $request))->getConfig($cliArgvs);
			}
		);
	}
}