<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;

class Volt
{
	protected $view;

	protected $volt;

	public function __construct(ViewBaseInterface $view)
	{
		$this->view = $view;
	}

	public function init($container, $cache, $compiledPath)
	{
		$this->volt = new PhalconVolt($this->view, $container);

		$this->volt->setOptions(
			[
				'always'        => $cache ? false : true,
				'separator'     => '-',
				'stat'          => true,
				'path'          => $compiledPath
			]
		);

		return $this->volt;
	}
}