<?php

namespace Applications\Admin\Components\Modules;

use Applications\Admin\Packages\Modules\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		// $infoModule = $this->usePackage(Module::class)->moduleInfo();

		// $infoModule->runProcess($this->getData());

		// $this->view->module = $infoModule->packagesData->info;

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
	}

	/**
	 * @acl(name=add)
	 */
	public function addAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		$installModule = $modules->installModule($this->postData);

		if ($installModule->packagesData['responseCode'] === 0) {

			$this->view->responseCode = 0;

			$this->view->responseMessage =
				rtrim(ucfirst($this->postData['type'])) . ' ' . ucfirst($this->postData['name']) . ' Installed Successfully! ' .
				'<br>Backup was successfully taken at location .backups/' . $installModule->packagesData['backupFile'];

			$this->flash->now(
				'success',
				rtrim(ucfirst($this->postData['type'])) . ' ' .
					ucfirst($this->postData['name']) . ' Installed Successfully! ' .
					'<br>Backup was successfully taken at location .backups/' .
					$installModule->packagesData['backupFile']
			);

			return $this->generateView();

		} else if ($installModule->packagesData['responseCode'] === 1) {

			$this->view->responseCode = 1;

			$this->view->responseMessage = $installModule->packagesData['responseMessage'];

			return $this->generateView();
		}
	}

	/**
	 * @acl(name=update)
	 */
	public function updateAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		$updateModule = $modules->installModule($this->postData);

		if ($updateModule->packagesData['responseCode'] === 0) {

			$this->view->responseCode = 0;

			$this->view->responseMessage =
				rtrim(ucfirst($this->postData['type'])) . ' ' . ucfirst($this->postData['name']) . ' Updated Successfully! ' .
				'<br>Backup was successfully taken at location .backups/' . $updateModule->packagesData['backupFile'];

			$this->flash->now(
				'success',
				rtrim(ucfirst($this->postData['type'])) . ' ' .
					ucfirst($this->postData['name']) . ' Updated Successfully! ' .
					'<br>Backup was successfully taken at location .backups/' .
					$updateModule->packagesData['backupFile']
			);

			return $this->generateView();

		} else if ($updateModule->packagesData['responseCode'] === 1) {

			$this->view->responseCode = 1;

			$this->view->responseMessage = $updateModule->packagesData['responseMessage'];

			return $this->generateView();
		}
	}
}