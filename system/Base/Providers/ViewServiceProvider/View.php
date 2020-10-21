<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;

class View
{
	protected $phalconView;

	protected $views;

	public function __construct($views)
	{
		$this->views = $views;
	}

	public function init()
	{
		$this->phalconView = new PhalconView();

		$this->phalconView->setViewsDir($this->views->getPhalconViewPath());

		$this->phalconView->setLayoutsDir($this->views->getPhalconViewLayoutPath());

		$this->phalconView->setMainView('view');

		$this->phalconView->setLayout($this->views->getPhalconViewLayoutFile());

		$this->phalconView->registerEngines(
			[
				'.html'     => 'volt',
				'.phtml'    => PhpTemplateService::class
			]
		);

		return $this->phalconView;
	}
}