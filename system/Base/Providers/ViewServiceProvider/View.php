<?php

namespace System\Base\Providers\ViewServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\View as PhalconView;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;
use Phalcon\Mvc\View\Engine\Volt;
use System\Base\Providers\ModulesServiceProvider\Views\ViewsData;

class View
{
	private $container;

	protected $phalconView;

	protected $views;

	protected $view;

	protected $applications;

	protected $applicationInfo;

	protected $db;

	protected $path;

	protected $cache;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->registerVoltTemplateService();
	}

	public function registerPhalconView()
	{
		$this->phalconView = new PhalconView();

		$this->phalconView->setViewsDir(
			$this->container->getShared('modules')->views->init()->getPhalconViewPath()
		);

		$this->phalconView->registerEngines(
			[
				'.html'     => 'voltTemplateService',
				'.phtml'    => PhpTemplateService::class
			]
		);

		return $this->phalconView;
	}

	protected function registerVoltTemplateService()
	{
		$this->container->setShared(
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

		return $this;
	}

	// protected function setPath()
	// {
	// 	$this->path =
	// 		base_path('applications/' . $this->applicationInfo['name'] .
	// 				  '/Views/' . $this->view['name'] .
	// 				  '/html_compiled/');
	// }

	// public function getPath()
	// {
	// 	return $this->path;
	// }

	// public function getCache()
	// {
	// 	return $this->cache;
	// }

	// public function getViewInfo()
	// {
	// 	return $this->view;
	// }

	// public function getViewsData()
	// {
	// 	return $this->viewsData;
	// }

	// protected function setApplicationInfo()
	// {
	// 	$this->applicationInfo = $this->applications->getApplicationInfo();

	// 	if ($this->applicationInfo) {

	// 		$applicationDefaults = $this->applications->getApplicationDefaults($this->applicationInfo['name']);
	// 	} else {
	// 		$applicationDefaults = null;
	// 	}
	// 	if ($this->applicationInfo && $applicationDefaults) {

	// 		$applicationName = $applicationDefaults['application'];

	// 		$viewsName = $applicationDefaults['view'];

	// 		if (!$this->view) {
	// 			$this->getApplicationView($viewsName, $this->applicationInfo['id']);
	// 		}

	// 		$this->cache = json_decode($this->view['settings'], true)['cache'];
	// 	}
	// }

	// protected function getAllViews()
	// {
	// 	return $this->db->fetchAll("SELECT * FROM `views`");
	// }

	// protected function getApplicationView($name, $id)
	// {
	// 	$this->view =
	// 			$this->views[array_search($id, array_column($this->views, 'application_id'))];
	// }
}