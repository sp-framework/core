<?php

namespace Apps\Core\Components\Modules;

use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	public function initialize()
	{
		$this->modulesManager = $this->usePackage('manager');

		$this->setModuleSettings(true);

		$this->setModuleSettingsData([
				'apis' => $this->modulesManager->getAvailableApis(true, false),
				'apiClients' => $this->modulesManager->getAvailableApis(false, false)
			]
		);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['queue'])) {
			if (isset($this->getData()['id']) && $this->getData()['id'] != 0) {
				$queue = $this->modules->queues->getById($this->getData()['id']);
			} else {
				$queue = $this->modules->queues->getActiveQueue();
			}

			if (!$queue) {
				return $this->throwIdNotFound();
			}

			$this->modules->queues->analyseActiveQueue($queue);

			$this->view->queue = $queue;
			$this->view->queueTasks = $this->modules->queues->packagesData->responseData['queueTasks'] ?? [];
			$this->view->queueTasksCounter = $this->modules->queues->packagesData->responseData['queueTasksCounter'] ?? [];

			$this->view->pick('modules/analyse');

			return;
		}

		$this->view->modules = $this->modulesManager->getRepositoryModules();

		if (!$this->view->modules) {
			$this->view->modules = ['modules' => []];
			$this->view->modulesError = $this->modulesManager->packagesData->responseMessage;
		}

		$queue = $this->modules->queues->getActiveQueue();

		if (is_string($queue['tasks'])) {
			$queue['tasks'] = $this->helper->decode($queue['tasks'], true);
		}

		$this->view->queue = $queue;
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
		$this->requestIsPost();

		if ($this->modulesManager->syncRemoteWithLocal($this->postData())) {
			$counter = $this->modulesManager->packagesData->counter;
			$modulesTree = $this->modulesManager->packagesData->responseData;

			$this->addResponse(
				$this->modulesManager->packagesData->responseMessage,
				$this->modulesManager->packagesData->responseCode,
				array_merge($modulesTree, ['counter' => $counter, 'modules_html' => $this->generateTree($modulesTree)])
			);

			return true;
		}

		$this->addResponse(
			$this->modulesManager->packagesData->responseMessage,
			$this->modulesManager->packagesData->responseCode
		);
	}

	public function getModuleInfoAction()
	{
		$this->requestIsPost();

		if (isset($this->postData()['module_type']) && isset($this->postData()['module_id'])) {
			$this->modulesManager->getModuleInfo($this->postData());

			$this->addResponse(
				$this->modulesManager->packagesData->responseMessage,
				$this->modulesManager->packagesData->responseCode,
				$this->modulesManager->packagesData->responseData,
			);
		} else {
			$this->addResponse('Please provide module type and module id', 1);
		}
	}

	public function getRepositoryModulesAction()
	{
		$this->requestIsPost();

		if (isset($this->postData()['api_id'])) {
			$this->modulesManager->getRepositoryModules($this->postData());

			$modulesTree = $this->modulesManager->packagesData->responseData;

			$responseData = array_merge($modulesTree, ['modules_html' => $this->generateTree($modulesTree)]);

			$this->addResponse(
				$this->modulesManager->packagesData->responseMessage,
				$this->modulesManager->packagesData->responseCode,
				$responseData
			);
		} else {
			$this->addResponse('Please provide module type and module id', 1);
		}
	}

	public function modifyQueueAction()
	{
		$this->requestIsPost();

		$this->modules->queues->modifyQueue($this->postData());

		$this->addResponse(
			$this->modules->queues->packagesData->responseMessage,
			$this->modules->queues->packagesData->responseCode,
			$this->modules->queues->packagesData->responseData ?? []
		);
	}

	public function analyseActiveQueueAction()
	{
		$this->requestIsPost();
		try {
			$this->modules->queues->analyseActiveQueue();
		} catch (\Exception $e) {
			trace([$e]);
		}

		$this->addResponse(
			$this->modules->queues->packagesData->responseMessage,
			$this->modules->queues->packagesData->responseCode,
			$this->modules->queues->packagesData->responseData ?? []
		);
	}

	public function processQueueAction()
	{
		$this->requestIsPost();

		$this->modules->installer->init($this->postData()['process'])->runProcess($this->postData());

		$this->addResponse(
			$this->modules->installer->packagesData->responseMessage,
			$this->modules->installer->packagesData->responseCode
		);
	}

	public function saveModuleSettingsAction()
	{
		$this->requestIsPost();

		if (isset($this->postData()['module_id']) && isset($this->postData()['module_type'])) {
			$this->modulesManager->saveModuleSettings($this->postData());

			$this->addResponse(
				$this->modulesManager->packagesData->responseMessage,
				$this->modulesManager->packagesData->responseCode
			);
		} else {
			$this->addResponse('Please provide module type and module id', 1);
		}
	}

	private function generateTree($modulesTree)
	{
		return $this->adminltetags->useTag(
			'tree',
			[
				'treeMode'      => 'jstree',
				'treeData'      => $modulesTree,
				'groupIcon' 	=> '{"icon" : "fas fa-fw fa-modules text-sm"}',
				'itemIcon' 		=> '{"icon" : "fas fa-fw fa-circle-dot text-sm"}'
			]
		);
	}
}