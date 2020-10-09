<?php

namespace Applications\Admin\Components\Modules\Repositories;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class SyncComponent extends BaseComponent
{
	public function view()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if (isset($this->getData['repoId'])) {

			$synced = $modules->syncRemoteWithLocal($this->getData['repoId']);

			if ($synced->packagesData['responseCode'] === 0) {

				$modulesData = $modules->getModulesData();

			} else {
				$this->viewsData->responseCode = 1;

				$this->viewsData->responseMessage = $synced->packagesData['responseMessage'];

				$this->viewFile = 'modules/modules.html';

				return $this->generateView();
			}

			if ($modulesData->packagesData['responseCode'] === 0) {

				$this->viewsData->responseCode = 0;

				$this->viewsData->modulesData = $modulesData->packagesData['modulesData'];

				$this->viewsData->counter = $modulesData->packagesData['counter'];

				$this->viewsData->thisApplication = $this->applicationInfo;

				$this->viewFile = 'modules/modules.html';

				return $this->generateView();
			} else if ($modulesData->packagesData['responseCode'] === 1) {

				$this->viewsData->responseCode = 1;

				$this->viewsData->responseMessage = $modulesData->packagesData['responseMessage'];

				$this->viewFile = 'modules/modules.html';

				return $this->generateView();
			}
		}

		// $this->viewsData->repositories = $this->packages->use(Repositories::class)->getAllRepositories();

		// $this->viewsData->setup = isset($this->getData['setup']) ? $this->getData['setup'] : false;

		return $this->generateView();
	}
}