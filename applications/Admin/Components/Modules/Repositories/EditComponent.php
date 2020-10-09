<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class EditComponent extends BaseComponent
{
	public function viewAction()
	{
		$this->view->thisApplication = $this->modules->applications->getApplicationInfo();

		$this->view->repository = $this->getRepositoryById($this->getData()['id']);

		$this->view->responseCode = 0;
	}

	public function updateAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if ($this->requestMethod === 'POST') {

			if ($modules->updateRepository($this->postData)) {

				$this->flash->now('success', 'Repository Updated!');

				$this->view->responseCode = 0;

				$this->view->responseMessage = 'Repository Updated!';

			} else {

				$this->flash->now('error', 'Error! Could not update repository.');

				$this->view->responseCode = 1;

				$this->view->responseMessage = 'Error! Could not update repository.';
			}
		} else {

			$this->flash->now('error', 'Request method not allowed.');

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Request method not allowed.';
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