<?php

namespace Apps\Dash\Components\Modules;

use Apps\Dash\Packages\Modules\Modules as ModulesPackage;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['filter'])) {
			$this->modules->manager->getModulesData($this->getData()['filter']);

			$this->view->modulesData = $this->modules->manager->packagesData->modulesData;
			$this->view->pick('modules/modulesdata');

			return;
		}

		$repositoriesArr = $this->modules->repositories->repositories;

		$repositories = [];

		foreach ($repositoriesArr as $key => $value) {
			$repositories[$key] = $value;
			$repositories[$key]['data']['repo_url'] = $repositoriesArr[$key]['repo_url'];
			$repositories[$key]['data']['site_url'] = $repositoriesArr[$key]['site_url'];
			$repositories[$key]['data']['branch'] = $repositoriesArr[$key]['branch'];
		}

		$this->view->repositories = $repositories;

		$appsArr = $this->apps->apps;

		$filters = [];

		$filters[] =
			[
				'id' => 'core',
				'name' => 'Core'
			];
		foreach ($appsArr as $key => $value) {
			$filters[] =
				[
					'id' => $value['id'],
					'name' => 'Modules for App - ' . $value['name']
				];
		}

		$this->view->filters = $filters;

		// $this->view->disable();

		// $infoModule = $this->usePackage(Module::class)->moduleInfo();

		// $infoModule->runProcess($this->getData());


		// $this->view->module = $infoModule->packagesData->info;

		// $modules = $this->usePackage(ModulesPackage::class);

		// } else {
		// 	$modules->getModulesData();
		// }

		// if ($modules->packagesData->responseCode === 0) {

			// if (isset($this->getData()['filter'])) { //Filtering
			// 	$this->view->pick('modules/modules');
			// }

			// $this->view->responseCode = 0;

			// $this->view->mode = $this->config->debug;


			// if (!isset($this->getData()['filter'])) {
			// 	$this->view->apps = $modules->packagesData->modulesData['apps'];

			// }

			// $this->view->thisApp = $modules->packagesData->appInfo;

			// $this->view->setup = isset($this->getData()['setup']) ? $this->getData()['setup'] : false;

		// } else if ($modules->packagesData->responseCode === 1) {

		// 	$this->view->responseCode = $modules->packagesData->responseCode;

		// 	$this->view->responseMessage = $modules->packagesData->responseMessage;
		// }
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