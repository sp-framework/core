<?php

namespace System\Base\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class ErrorServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
	protected $provides = [
	];

	public function register()
	{
		//
	}

	public function boot()
	{
		$container = $this->getContainer();

		$applicationInfo = $this->container->get('applications')->getApplicationInfo();

		if ($applicationInfo) {
			if ($applicationInfo['mode'] === 0) {
				$applicationDebug = false;
			} else if ($applicationInfo['mode'] === 1) {
				$applicationDebug = true;
			} else {
				$applicationDebug = (bool) $container->get('config')->get('base.debug');
			}
		} else {
			$applicationDebug = (bool) $container->get('config')->get('base.debug');
		}

		if ($applicationDebug) {
			error_reporting(-1);
		} else {
			error_reporting(0);
		}
	}
}