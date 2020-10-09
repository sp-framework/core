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

				$this->flashSession->success('Repository Added!');

			} else {

				$this->flashSession->error('Error! Could not add repository.');

			}
		} else {

			$this->flashSession->error('Request method not allowed.');

		}
	}
}