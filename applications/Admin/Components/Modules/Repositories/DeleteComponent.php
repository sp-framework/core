<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class DeleteComponent extends BaseComponent
{
	public function removeAction()
	{
		if ($this->request->isPost()) {
			if ($this->modules->repositories->remove($this->postData()['id'])) {

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