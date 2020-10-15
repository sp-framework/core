<?php

namespace System\Base\Installer\Components;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Simple;
use System\Base\Installer\Packages\Setup as SetupPackage;
use System\Base\Providers\ContentServiceProvider\Local\Content as LocalContent;

Class Setup
{
	private $container;

	private $setupPackage;

	private $view;

	public function __construct()
	{
		$container = new FactoryDefault();

		$container->setShared(
			'view',
			function () {
				$view = new Simple();

				$view->setViewsDir(base_path('system/Base/Installer/View/'));

				return $view;
			}
		);

		$container->setShared(
			'localContent',
			function () use ($container) {
				return (new LocalContent($container))->init();
			}
		);

		$this->container = $container;

		$this->response = $this->container->getShared('response');
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setHeader('Cache-Control', 'no-store');

		$this->request = $this->container->getShared('request');
		$this->postData = $this->request->getPost();

		$this->view = $this->container->getShared('view');
	}

	public function run()
	{
		try {
			$this->setupPackage = new SetupPackage($this->container);
		} catch (\Exception $e) {
			$this->view->responseCode = 1;
			$this->view->responseMessage = $e->getMessage();

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		}

		if ($this->request->isPost()) {

			if (!$this->setupPackage->checkDbEmpty()) {

				$this->view->responseCode = 1;
				$this->view->responseMessage =
					'Database <strong>' . $this->postData['database_name'] . '</strong> not empty!' .
					' Use drop existing tables checkbox to drop existing tables.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}
			}

			$this->setupPackage->buildSchema();

			$this->setupPackage->registerRepository();

			$this->setupPackage
				->registerCore(
					json_decode(
						$this->container->getShared('localContent')->read('core.json'),
						true)
				);

			$adminApplicationId = $this->setupPackage->registerModule('applications', null);

			if ($adminApplicationId) {

				$this->setupPackage->registerModule('components', $adminApplicationId);

				$this->setupPackage->registerModule('packages', $adminApplicationId);

				$this->setupPackage->registerModule('middlewares', $adminApplicationId);

				$this->setupPackage->registerModule('views', $adminApplicationId);

			}

			$this->setupPackage->writeConfigs();

			// $this->setupPackage->removeInstaller();

			$this->view->responseCode = 0;

			$this->view->responseMessage = 'Schema Updated.';

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		} else {

			echo $this->container->getShared('view')->render('setup');
		}
	}
}