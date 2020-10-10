<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class AddComponent extends BaseComponent
{
	public function viewAction()
	{
		$this->view->thisApplication = $this->modules->applications->getApplicationInfo();
	}

	public function insertAction()
	{
		if ($this->request->isPost()) {

			if ($this->modules->repositories->add($this->request->getPost())) {

				$this->flashSession->clear();

				$this->view->responseCode =
					$this->modules->repositories->packagesData->responseCode;

				$this->flashSession->success(
					$this->modules->repositories->packagesData->responseMessage);

			} else {

				$this->view->responseMessage = 'Error! Could not add repository.';

				$this->view->responseCode = 1;
			}
		} else {

			$this->view->responseMessage = 'Request method not allowed.';

			$this->view->responseCode = 1;
		}
	}
}