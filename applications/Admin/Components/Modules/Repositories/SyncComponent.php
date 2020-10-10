<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class SyncComponent extends BaseComponent
{
	public function viewAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if (isset($this->request->getPost()['repoId'])) {

			$synced = $modules->syncRemoteWithLocal($this->request->getPost()['repoId']);

			if ($synced->packagesData->responseCode === 0) {

				$modulesData = $modules->getModulesData();

			} else {
				$this->view->responseCode = $synced->packagesData->responseCode;

				$this->view->responseMessage = $synced->packagesData->responseMessage;

				$this->view->pick('modules/modules.html');
			}

			if ($modulesData->packagesData->responseCode === 0) {

				$this->view->responseCode = $modulesData->packagesData->responseCode;

				$this->view->responseMessage = $modulesData->packagesData->responseMessage;

				$this->view->modulesData = $modulesData->packagesData->modulesData;

				$this->view->counter = $modulesData->packagesData->counter;

				$this->view->thisApplication = $this->modules->applications->applicationInfo;

				$this->view->pick('modules/modules.html');

			} else if ($modulesData->packagesData->responseCode === 1) {

				$this->view->responseCode = $modulesData->packagesData->responseCode;

				$this->view->responseMessage = $modulesData->packagesData->responseMessage;

				$this->view->pick('modules/modules.html');

			}
		}

		// $this->view->repositories = $this->packages->use(Repositories::class)->getAllRepositories();

		// $this->view->setup = isset($this->getData['setup']) ? $this->getData['setup'] : false;

	}
}