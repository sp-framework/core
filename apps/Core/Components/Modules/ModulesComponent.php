<?php

namespace Apps\Core\Components\Modules;

use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		$this->view->repositories = $this->modules->repositories->repositories;

		$core = $this->modules->packages->getNamePackage('core');
		$module['id'] = $core['id'];
		$module['name'] = $core['display_name'];
		$module['data']['moduleid'] = $core['module_type'] . '-' . $core['id'];
		$module['data']['installed'] = $core['installed'];
		$module['data']['update_available'] = $core['update_available'];
		$modules['childs'] = [$module];
		$this->view->modules = $modules;
	}

	/**
	 * @acl(name=add)
	 * install module action
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
	 * update module action
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

	/**
	 * @acl(name=remove)
	 * uninstall module action
	 */
	public function removeAction()
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

	/**
	 * sync local modules with remote modules
	 */
	public function syncAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if (isset($this->postData()['repoId'])) {
				$counter = null;

				if ($this->modules->manager->syncRemoteWithLocal($this->postData()['repoId'])) {
					$counter = $this->modules->manager->packagesData->counter;

					$this->addResponse(
						$this->modules->manager->packagesData->responseMessage,
						$this->modules->manager->packagesData->responseCode,
						$counter
					);

					return true;
				}

				$this->addResponse(
					$this->modules->manager->packagesData->responseMessage,
					$this->modules->manager->packagesData->responseCode
				);
			} else {
				$this->addResponse('Repo id not provided', 1);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function getModuleInfoAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if (isset($this->postData()['module_type']) && isset($this->postData()['module_id'])) {
				$this->modules->manager->getModuleInfo($this->postData());

				$this->addResponse(
					$this->modules->manager->packagesData->responseMessage,
					$this->modules->manager->packagesData->responseCode,
					$this->modules->manager->packagesData->responseData,
				);
			} else {
				$this->addResponse('Please provide module type and module id', 1);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function getRepositoryModulesAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if (isset($this->postData()['api_id'])) {
				$this->modules->manager->getRepositoryModules($this->postData());

				$modulesTree = $this->modules->manager->packagesData->responseData;

				$treeData =
					$this->adminltetags->useTag(
						'tree',
						[
							'treeMode'      => 'jstree',
							'treeData'      => $modulesTree,
							'groupIcon' 	=> '{"icon" : "fa fa-fw fa-modules text-sm"}',
							'itemIcon' 		=> '{"icon" : "fas fa-fw fa-circle-dot text-sm"}'
						]
					);

				$responseData = ['modules' => $modulesTree, 'modules_html' => $treeData];

				$this->addResponse(
					$this->modules->manager->packagesData->responseMessage,
					$this->modules->manager->packagesData->responseCode,
					$responseData
				);
			} else {
				$this->addResponse('Please provide module type and module id', 1);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}
}