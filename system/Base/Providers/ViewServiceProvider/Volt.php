<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Volt as PhalconVolt;

class Volt
{
	protected $view;

	protected $volt;

	protected $compiler;

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

		$this->compiler = $this->volt->getCompiler();

		$this->registerFunctions();

		$this->registerFilters();

		return $this->volt;
	}

	protected function registerFunctions()
	{
		$enabledFunctions = get_defined_functions(true);

		foreach ($enabledFunctions['internal'] as $internalFunction) {
			$this->compiler->addFunction($internalFunction, $internalFunction);
		}
		foreach ($enabledFunctions['user'] as $userFunction) {
			$this->compiler->addFunction($userFunction, $userFunction);
		}
	}

	protected function registerFilters()
	{
		//
	}
}