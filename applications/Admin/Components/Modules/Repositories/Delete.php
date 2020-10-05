<?php

namespace Components\Admin\Modules\Repositories;

use Packages\Admin\Modules;
use System\Base\BaseComponent;

class Delete extends BaseComponent
{
	public function Remove()
	{
		$modules = $this->packages->use(Modules::class);

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