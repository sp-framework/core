<?php

namespace Components\Admin\Modules\Repositories;

use Packages\Admin\Modules;
use System\Base\BaseComponent;

class Edit extends BaseComponent
{
	public function view()
	{
		$modules = $this->packages->use(Modules::class);

		$this->viewsData->repository = $modules->getRepositoryById($this->getData['id']);

		$this->viewsData->responseCode = 0;

		return $this->generateView();
	}

	public function update()
	{
		$modules = $this->packages->use(Modules::class);

		if ($this->requestMethod === 'POST') {

			if ($modules->updateRepository($this->postData)) {

				$this->flash->now('success', 'Repository Updated!');

				$this->viewsData->responseCode = 0;

				$this->viewsData->responseMessage = 'Repository Updated!';

			} else {

				$this->flash->now('error', 'Error! Could not update repository.');

				$this->viewsData->responseCode = 1;

				$this->viewsData->responseMessage = 'Error! Could not update repository.';
			}
		} else {

			$this->flash->now('error', 'Request method not allowed.');

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = 'Request method not allowed.';
		}
		return $this->generateView();
	}
}