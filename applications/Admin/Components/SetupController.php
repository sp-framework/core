<?php

namespace Applications\Admin\Components;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\View;
use System\Base\BaseController;
use Applications\Admin\Packages\Setup;

class SetupController extends BaseController
{
	public function viewAction()
	{

	}

	public function runAction()
	{
		if ($this->request->isPost()) {

			$setup = new Setup($this->container);

			if (!$setup->checkFields()) {
				$this->view->responseCode = 1;
				$this->view->responseMessage = 'All fields are required.';
				return;
			}

			var_dump($this->container->getShared('db'));
			if ($dbConnection !== true) {
				$this->view->responseCode = 1;

				$this->view->responseMessage = $dbConnection->getMessage();

				$this->container['emitter']->emit(
					$this->container['viewsClass']->render(
						$this->container['response'],
						'Admin/Default/html/setup/view.html',
						$this->view,
						'Setup'
					)
				);
				exit;
			}

			if (!$setup->checkDbEmpty()) {

				$this->view->responseCode = 1;

				$this->view->responseMessage =
					'Database <strong>' . $this->container['request']->getParsedBody()['database_name'] . '</strong> not empty!' .
					' Use drop existing tables checkbox to drop existing tables.';

				$this->container['emitter']->emit(
					$this->container['viewsClass']->render(
						$this->container['response'],
						'Admin/Default/html/setup/view.html',
						$this->view,
						'Setup'
					)
				);
			}

			$buildSchema = $setup->buildSchema();

			if ($buildSchema !== true) {
				$this->view->responseCode = 1;

				$this->view->responseMessage = $buildSchema->getMessage();

				$this->container['emitter']->emit(
					$this->container['viewsClass']->render(
						$this->container['response'],
						'Admin/Default/html/setup/view.html',
						$this->view,
						'Setup'
					)
				);
				exit;
			}

			$setup
				->registerCore(
					json_decode(
						$this->container['fileSystem']->read('core.json'),
						true)
				);

			$setup->registerHWFRepository();

			$adminApplication = $setup->registerModule('applications', null);

			if ($adminApplication) {

				$setup->registerModule('components', $adminApplication['id']);

				$setup->registerModule('packages', $adminApplication['id']);

				$setup->registerModule('middlewares', $adminApplication['id']);

				$setup->registerModule('views', $adminApplication['id']);

			}

			$setup->writeDbConfig();

			// $setup->removeSetup();

			$this->view->responseCode = 0;

			$this->view->responseMessage = 'Schema Updated.';

			$this->container['emitter']->emit(
				$this->container['viewsClass']->render(
					$this->container['response'],
					'Admin/Default/html/setup/view.html',
					$this->view,
					'Setup'
				)
			);

		}
	}
}