<?php

namespace Components\Admin\Modules\Repositories;

use Packages\Admin\Modules;
use Packages\Admin\Repositories;
use System\Base\BaseComponent;

class Add extends BaseComponent
{
	public function view()
	{
		return $this->generateView();
	}

	public function insert()
	{
		$modules = $this->packages->use(Modules::class);

		if ($this->requestMethod === 'POST') {

			if ($modules->addRepository($this->postData)) {

				$this->flash->now('success', 'Repository Added!');

				$this->viewsData->responseCode = 0;

				$this->viewsData->responseMessage = 'Repository Added!';

			} else {

				$this->flash->now('error', 'Error! Could not add repository.');

				$this->viewsData->responseCode = 1;

				$this->viewsData->responseMessage = 'Error! Could not add repository.';

			}
		} else {

			$this->flash->now('error', 'Request method not allowed.');

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = 'Request method not allowed.';
		}
		return $this->generateView();
	}
}