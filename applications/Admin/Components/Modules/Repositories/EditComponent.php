<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class EditComponent extends BaseComponent
{
	public function viewAction()
	{
		$this->view->thisApplication = $this->modules->applications->getApplicationInfo();

		$this->view->repository = $this->modules->repositories->get($this->getData()['id']);

		$this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

		$this->view->responseMessage = $this->modules->repositories->packagesData->responseMessage;
		// $this->view->disable();
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if ($this->modules->repositories->update($this->request->getPost())) {

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

	protected function getRepositoryById($id)
	{
		return $this->modules->repositories->repositories
			[
				array_search($id, array_column($this->modules->repositories->repositories, 'id'))
			];
	}
}