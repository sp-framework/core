<?php

namespace Applications\Admin\Components\Module;

use Applications\Admin\Packages\Module\Settings;
use System\Base\BaseComponent;

class SettingsComponent extends BaseComponent
{
	public function viewAction()
	{
		$moduleSettings = $this->usePackage(Settings::class);

		$moduleSettings->get($this->getData());

		if ($this->getData()['type'] === 'applications') {

			$this->view->application = $moduleSettings->packagesData->application;

			$this->view->settings = $moduleSettings->packagesData->settings;

			$this->view->components = $moduleSettings->packagesData->components;

			$this->view->views = $moduleSettings->packagesData->views;

		} else if ($this->getData()['type'] === 'components') {

			$this->view->components = $settingsModule->packagesData->components;

			$this->view->component = $settingsModule->packagesData->component;

			$this->view->settings = $settingsModule->packagesData->settings;

			$this->view->componentSettingsFileContent = $settingsModule->packagesData->componentSettingsFileContent;

		} else if ($this->getData()['type'] === 'packages') {

			$this->view->packages = $settingsModule->packagesData->packages;

			$this->view->package = $settingsModule->packagesData->package;

			$this->view->settings = $settingsModule->packagesData->settings;

			$this->view->packageSettingsFileContent = $settingsModule->packagesData->packageSettingsFileContent;

		} else if ($this->getData()['type'] === 'middlewares') {

			$this->view->middlewares = $settingsModule->packagesData->middlewares;

			$this->view->middleware = $settingsModule->packagesData->middleware;

			$this->view->settings = $settingsModule->packagesData->settings;

			$this->view->middlewareSettingsFileContent = $settingsModule->packagesData->middlewareSettingsFileContent;

		} else if ($this->getData()['type'] === 'views') {

			$this->view->view = $settingsModule->packagesData->view;

			$this->view->settings = $settingsModule->packagesData->settings;
		}

		$this->view->type = $moduleSettings->packagesData->type;

		$this->view->thisApplication = $this->modules->applications->getApplicationInfo();

	}

	public function editAction()
	{
		$moduleSettings = $this->usePackage(Settings::class);

		if ($moduleSettings->update($this->postData())) {

			$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			$this->flashSession->success($moduleSettings->packagesData->responseMessage);

		} else {
			$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			return $this->sendJson();
		}

			// if ($this->postData()['type'] === 'applications') {

			// 	$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			// 	$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			// 	$this->flashSession->success($moduleSettings->packagesData->responseMessage);

			// } else if ($this->postData()['type'] === 'components') {

			// 	$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			// 	$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			// 	$this->flashSession->success($moduleSettings->packagesData->responseMessage);

			// } else if ($this->postData()['type'] === 'packages') {

			// 	$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			// 	$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			// 	$this->flashSession->success($moduleSettings->packagesData->responseMessage);

			// } else if ($this->postData()['type'] === 'middlewares') {

			// 	$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			// 	$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			// 	$this->flashSession->success($moduleSettings->packagesData->responseMessage);

			// } else if ($this->postData()['type'] === 'views') {

			// 	$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			// 	$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			// 	$this->flashSession->success($moduleSettings->packagesData->responseMessage);
			// }
	}
}