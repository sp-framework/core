<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;
use Phalcon\Mvc\View\Engine\Volt;
use System\Base\Providers\ModulesServiceProvider\Views\ViewsData;

class View
{
	protected $phalconView;

	protected $views;

	protected $view;

	protected $applications;

	protected $applicationInfo;

	protected $db;

	protected $path;

	protected $cache;

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
				'.html'     => 'voltTemplateService',
				'.phtml'    => PhpTemplateService::class
			]
		);

		return $this->phalconView;
	}
}