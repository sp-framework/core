<?php

namespace System\Base\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Volt;
use System\Base\Providers\ViewServiceProvider\View;

class ViewServiceProvider implements ServiceProviderInterface
{
	public function register(DiInterface $container) : void
	{
		$container->setShared(
			'voltTemplateService',
			function(ViewBaseInterface $view) {

				$this->volt = new Volt($view, $this);

				if ($this->getShared('modules')->views->getCache()) {
					$always = false;
				} else {
					$always = true;
				}

				$this->volt->setOptions(
					[
						'always'        => $always,
						'separator'     => '-',
						'stat'          => true,
						'path'          => $this->getShared('modules')->views->getVoltCompiledPath()
					]
				);

				return $this->volt;
			}
		);

		$views = $container->getShared('modules')->views->init();

		$container->setShared(
			'view',
			function () use ($views) {
				return (new View($views))->init();
			}
		);
	}
}
