<?php

namespace Applications\Admin\Components\Repositories;

use Applications\Admin\Packages\Modules as ModulesPackage;
use System\Base\BaseComponent;

class SyncComponent extends BaseComponent
{
	public function viewAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if (isset($this->getData()['repoId'])) {

			$synced = $modules->syncRemoteWithLocal($this->getData()['repoId']);

			if ($synced === true) {

				$modulesData = $modules->getModulesData(true);

			} else {
				$this->view->responseCode = $modules->packagesData->responseCode;

				$this->view->responseMessage = $modules->packagesData->responseMessage;

				return $this->sendJson();
			}

			if ($modulesData === true) {

				$this->view->responseCode = $modules->packagesData->responseCode;

				$this->view->responseMessage = $modules->packagesData->responseMessage;

				$this->view->modulesData = $modules->packagesData->modulesData;

				$this->view->counter = $modules->packagesData->counter;

				$this->view->thisApplication = $modules->packagesData->applicationInfo;

				$this->view->pick('../modules');
			} else {

				$this->view->responseCode = $modulesData->packagesData->responseCode;

				$this->view->responseMessage = $modulesData->packagesData->responseMessage;

				return $this->sendJson();
			}
		}
				// var_dump($this->view);

		// $this->view->disable();
		// $this->view->repositories = $this->packages->use(Repositories::class)->getAllRepositories();

		// $this->view->setup = isset($this->getData['setup']) ? $this->getData['setup'] : false;

	}
}