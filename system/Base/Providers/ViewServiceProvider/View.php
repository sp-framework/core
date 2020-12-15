<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;

class View
{
	protected $phalconView;

	protected $views;

	protected $events;

	public function __construct($views, $events)
	{
		$this->views = $views;

		$this->events = $events;
	}

	public function init()
	{
		$this->events->attach(
			'view',
			function (Event $event, $view) {

				if($event->getType() == 'beforeRender') {

					$path = $view->getViewsDir();

					$path .= strtolower($view->getControllerName()) . '/';

					$path .= $view->getActionName() . '.html';

					if (!file_exists($path)) {
						throw new \Exception('Template '.$path.' not found');
					}
				}
			}
		);

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

		$this->phalconView->setEventsManager($this->events);

		return $this->phalconView;
	}
}