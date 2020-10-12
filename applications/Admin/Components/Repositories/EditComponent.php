<?php

namespace Applications\Admin\Components\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class EditComponent extends BaseComponent
{
	public function viewAction()
	{
		$this->view->repository = $this->modules->repositories->getById($this->getData()['id']);

		if (!$this->view->repository) {

			$this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

			$this->view->responseMessage = $this->modules->repositories->packagesData->responseMessage;

			return $this->sendJson();
		}

		$this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

		$this->view->thisApplication = $this->modules->applications->getApplicationInfo();
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if ($this->modules->repositories->update($this->postData())) {

				$this->flashSession->clear();

				$this->view->responseCode =
					$this->modules->repositories->packagesData->responseCode;

				$this->flashSession->success(
					$this->modules->repositories->packagesData->responseMessage
				);
			} else {
				$this->view->responseCode =
					$this->modules->repositories->packagesData->responseCode;

				$this->view->responseMessage =
					$this->modules->repositories->packagesData->responseMessage;
			}
		} else {

			$this->view->responseMessage = 'Request method not allowed.';

			$this->view->responseCode = 1;
		}
	}
}