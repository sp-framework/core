<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class DeleteComponent extends BaseComponent
{
	public function removeAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if ($modules->deleteRepository($this->postData['id'])) {

			$this->viewsData->responseCode = 0;

			$this->flash->now('success', 'Repository Deleted!');

			$this->viewsData->responseMessage = 'Repository Deleted!';

		} else {

			$this->viewsData->responseCode = 1;

			$this->flash->now('error', 'Error! Could not delete repository.');

			$this->viewsData->responseMessage = 'Error! Could not delete repository.';

		}

		return $this->generateView();
	}
}