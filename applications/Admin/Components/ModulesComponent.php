<?php

namespace Applications\Admin\Components;

use Applications\Admin\Packages\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	public function viewAction()
	{
		$modules = $this->modules->packages->usePackage(ModulesPackage::class);

		if (isset($this->getData()['filter']) && $this->getData()['filter'] !== '0') { //Filtering
			if ($this->getData()['filter'] !== 'installed' && $this->getData()['filter'] !== 'update_available') {

				$modulesData = $modules->getLocalModules(
					[
						'application_id' 	=> $this->getData()['filter']
					],
					false
				);

			} else if ($this->getData()['filter'] === 'installed') {

				$modulesData = $modules->getLocalModules(
					[
						'installed'			=> 1
					]
				);

			} else if ($this->getData()['filter'] === 'update_available') {

				$modulesData = $modules->getLocalModules(
					[
						'update_available'	=> 1
					]
				);

			}
		} else {
			$modulesData = $modules->getModulesData();
		}

		if ($modulesData->packagesData['responseCode'] === 0) {

			if (isset($this->getData()['filter'])) { //Filtering
				$this->viewFile = 'Admin/Default/html/modules/modules.html';
			}

			$this->viewsData->responseCode = 0;

			$this->viewsData->mode = $this->mode;

			$this->viewsData->modulesData = $modulesData->packagesData['modulesData'];

			if (!isset($this->getData()['filter'])) {
				$this->viewsData->applications = $modulesData->packagesData['applications'];

				$this->viewsData->repositories = $modulesData->packagesData['repositories'];
			}

			$this->viewsData->thisApplication = $this->applicationInfo;

			$this->viewsData->setup = isset($this->getData()['setup']) ? $this->getData()['setup'] : false;

			return $this->generateView();

		} else if ($modulesData->packagesData['responseCode'] === 1) {

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = $modulesData->packagesData['responseMessage'];

			return $this->generateView();
		}
	}
}