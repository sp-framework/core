<?php

namespace Applications\Admin\Components;

use Applications\Admin\Packages\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	public function viewAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		if (isset($this->getData()['filter']) && $this->getData()['filter'] !== '0') { //Filtering
			if ($this->getData()['filter'] !== 'installed' &&
				$this->getData()['filter'] !== 'not_installed' &&
				$this->getData()['filter'] !== 'update_available'
			) {

				$modules->getLocalModules(
					[
						'application_id' 	=> $this->getData()['filter']
					],
					false
				);

			} else if ($this->getData()['filter'] === 'installed') {

				$modules->getLocalModules(
					[
						'installed'			=> 1
					]
				);

			} else if ($this->getData()['filter'] === 'not_installed') {

				$modules->getLocalModules(
					[
						'installed'			=> 0
					],
					false
				);

			} else if ($this->getData()['filter'] === 'update_available') {

				$modules->getLocalModules(
					[
						'update_available'	=> 1
					]
				);

			}
		} else {
			$modules->getModulesData();
		}

		if ($modules->packagesData->responseCode === 0) {

			if (isset($this->getData()['filter'])) { //Filtering
				$this->view->pick('modules/modules');
			}

			$this->view->responseCode = 0;

			$this->view->mode = $this->config->debug;

			$this->view->modulesData = $modules->packagesData->modulesData;

			if (!isset($this->getData()['filter'])) {
				$this->view->applications = $modules->packagesData->modulesData['applications'];

				$this->view->repositories = $modules->packagesData->repositories;
			}

			$this->view->thisApplication = $modules->packagesData->applicationInfo;

			$this->view->setup = isset($this->getData()['setup']) ? $this->getData()['setup'] : false;

		} else if ($modules->packagesData->responseCode === 1) {

			$this->view->responseCode = $modules->packagesData->responseCode;

			$this->view->responseMessage = $modules->packagesData->responseMessage;
		}
		// $this->view->disable();
	}
}