<?php

namespace Apps\Dash\Components\Module\Settings;

use Apps\Dash\Packages\Module\Module;
use System\Base\BaseComponent;

class SettingsComponent extends BaseComponent
{
	public function viewAction()
	{
		$modulePackage = $this->usePackage(Module::class);

		if ($this->getData()['type'] === 'core') {

			$moduleSettings = $modulePackage->moduleSettings('Core')->get();

			$this->view->core = $moduleSettings->packagesData->core;

			$this->view->settings = $moduleSettings->packagesData->settings;

		} else if ($this->getData()['type'] === 'apps') {

			$moduleSettings = $modulePackage->moduleSettings('Apps')->get($this->getData());

			$this->view->app = $moduleSettings->packagesData->app;

			$this->view->settings = $moduleSettings->packagesData->settings;

			$this->view->components = $moduleSettings->packagesData->components;

			$this->view->domains = $moduleSettings->packagesData->domains;

			$this->view->email = $moduleSettings->packagesData->email;

			$this->view->views = $moduleSettings->packagesData->views;

		} else if ($this->getData()['type'] === 'components') {

			$moduleSettings = $modulePackage->moduleSettings('Components')->get($this->getData());

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

		$this->view->type = $this->getData()['type'];

		$this->view->thisApp = $this->apps->getAppInfo();

	}

	public function editAction()
	{
		$moduleSettings = $this->usePackage(Module::class)->moduleSettings();

		if ($moduleSettings->update($this->postData())) {

			$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			$this->flashSession->success($moduleSettings->packagesData->responseMessage);

		} else {
			$this->view->responseCode = $moduleSettings->packagesData->responseCode;

			$this->view->responseMessage = $moduleSettings->packagesData->responseMessage;

			return $this->sendJson();
		}
	}

	public function testEmailAction()
	{
		$emailTest = $this->usePackage(Module::class)->testEmail();

		if ($emailTest->runTest($this->postData())) {
			$this->view->responseCode = $emailTest->packagesData->responseCode;

			$this->view->responseMessage = $emailTest->packagesData->responseMessage;

			return $this->sendJson();
		}
	}
}