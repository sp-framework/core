<?php

namespace System\Base\Installer\Components;

use Phalcon\Di\FactoryDefault;
use Phalcon\Helper\Json;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\View\Simple;
use System\Base\Installer\Packages\Setup as SetupPackage;
use System\Base\Providers\ContentServiceProvider\Local\Content as LocalContent;
use System\Base\Providers\SecurityServiceProvider\Crypt;
use System\Base\Providers\SecurityServiceProvider\Random;
use System\Base\Providers\SecurityServiceProvider\Security;
use System\Base\Providers\ValidationServiceProvider\Validation;

Class Setup
{
	private $container;

	private $setupPackage;

	private $view;

	private $localContent;

	protected $coreJson;

	public function __construct($session)
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

		$container->setShared(
			'validation',
			function () {
				return (new Validation())->init();
			}
		);

		$container->setShared(
			'security',
			function () {
				return (new Security())->init();
			}
		);

		$container->setShared(
			'crypt',
			function () {
				return (new Crypt())->init();
			}
		);

		$container->setShared(
			'random',
			function () {
				return (new Random())->init();
			}
		);

		$container->setShared(
			'cookies',
			function () {
				return new Cookies();
			}
		);

		$container->setShared('session', $session);

		$this->container = $container;

		$this->response = $this->container->getShared('response');
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setHeader('Cache-Control', 'no-store');

		$this->request = $this->container->getShared('request');
		$this->postData = $this->request->getPost();

		$this->view = $this->container->getShared('view');

		$this->localContent = $this->container->getShared('localContent');
	}

	public function run($onlyUpdateDb = false, $message = null)
	{
		try {
			$this->setupPackage = new SetupPackage($this->container, $this->postData);
		} catch (\Exception $e) {
			$this->view->responseCode = 1;
			$this->view->responseMessage = $e->getMessage();

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		}

		if ($this->request->isPost()) {
			if (isset($this->postData['create-username']) &&
				isset($this->postData['create-password'])
			) {
				try {
					$this->setupPackage->createNewDb();
					$this->setupPackage->checkUser();

					unset($this->postData['create-username']);
					unset($this->postData['create-password']);

					$this->setupPackage = new SetupPackage($this->container, $this->postData);
				} catch (\Exception $e) {
					$this->view->responseCode = 1;
					$this->view->responseMessage = $e->getMessage();

					if ($this->response->isSent() !== true) {
						$this->response->setJsonContent($this->view->getParamsToView());

						return $this->response->send();
					}
				}
			}

			if ($onlyUpdateDb) {
				$this->setupPackage->writeConfigs();

				$this->view->responseCode = 0;

				$this->view->responseMessage = 'Configuration Updated.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}

				return;
			}

			$this->setupPackage->cleanVar();

			$this->setupPackage->cleanOldCookies();

			$this->coreJson = Json::decode($this->container->getShared('localContent')->read('core.json'), true);

			try {
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

				$this->setupPackage->registerDomain();

				$baseConfig = $this->setupPackage->writeConfigs($this->coreJson);

				$this->setupPackage->registerCore($baseConfig);

				$adminAppId = $this->setupPackage->registerApp();

				if ($adminAppId) {

					$this->setupPackage->registerModule('components');

					$this->setupPackage->updateAdminAppComponents();

					$this->setupPackage->registerModule('packages');

					$this->setupPackage->registerModule('middlewares');

					$this->setupPackage->registerModule('views');

					$adminRoleId = $this->setupPackage->registerRootAdminRole();

					if ($adminRoleId) {
						$adminAccountId =
						$this->setupPackage->registerAdminAccount(
							$adminRoleId, $baseConfig['settings']['security']['passwordWorkFactor']
						);
					}

					if ($adminAccountId) {
						$this->setupPackage->registerAdminProfile($adminAccountId);
					}

					$this->setupPackage->registerRegisteredUserAndGuestRoles();

					$this->setupPackage->registerExcludeAutoGeneratedFilters();

					$this->setupPackage->registerCountries();

					$this->setupPackage->registerTimezones();

					$this->setupPackage->registerWorkers();

					$this->setupPackage->registerSchedules();

					$this->setupPackage->registerTasks();
				}

				// $this->setupPackage->removeInstaller();

				$this->view->responseCode = 0;

				$this->view->responseMessage = 'Schema Updated.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}
			} catch (\Exception $e) {

				$this->setupPackage->revertBaseConfig($this->coreJson);

				throw $e;
			}
		} else {
			echo $this->container->getShared('view')->render('setup', ['onlyUpdateDb' => $onlyUpdateDb, 'message' => $message]);
		}
	}
}