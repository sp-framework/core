<?php

namespace Components\Admin\Modules\Module;

use Packages\Admin\Modules;
use System\Base\BaseComponent;

class Settings extends BaseComponent
{
	public function view()
	{
		$modules = $this->packages->use(Modules::class);

		$settingsModule = $modules->getModuleSettings($this->getData);

		if ($this->getData['type'] === 'applications') {

			$this->viewsData->type = $settingsModule->packagesData['type'];

			$this->viewsData->application = $settingsModule->packagesData['application'];

			$this->viewsData->settings = $settingsModule->packagesData['settings'];

			$this->viewsData->components = $settingsModule->packagesData['components'];

			$this->viewsData->views = $settingsModule->packagesData['views'];

		} else if ($this->getData['type'] === 'components') {

			$this->viewsData->type = $settingsModule->packagesData['type'];

			$this->viewsData->components = $settingsModule->packagesData['components'];

			$this->viewsData->component = $settingsModule->packagesData['component'];

			$this->viewsData->settings = $settingsModule->packagesData['settings'];

			$this->viewsData->componentSettingsFileContent = $settingsModule->packagesData['componentSettingsFileContent'];

		} else if ($this->getData['type'] === 'packages') {

			$this->viewsData->type = $settingsModule->packagesData['type'];

			$this->viewsData->packages = $settingsModule->packagesData['packages'];

			$this->viewsData->package = $settingsModule->packagesData['package'];

			$this->viewsData->settings = $settingsModule->packagesData['settings'];

			$this->viewsData->packageSettingsFileContent = $settingsModule->packagesData['packageSettingsFileContent'];

		} else if ($this->getData['type'] === 'middlewares') {

			$this->viewsData->type = $settingsModule->packagesData['type'];

			$this->viewsData->middlewares = $settingsModule->packagesData['middlewares'];

			$this->viewsData->middleware = $settingsModule->packagesData['middleware'];

			$this->viewsData->settings = $settingsModule->packagesData['settings'];

			$this->viewsData->middlewareSettingsFileContent = $settingsModule->packagesData['middlewareSettingsFileContent'];

		} else if ($this->getData['type'] === 'views') {

			$this->viewsData->type = $settingsModule->packagesData['type'];

			$this->viewsData->view = $settingsModule->packagesData['view'];

			$this->viewsData->settings = $settingsModule->packagesData['settings'];
		}

		$this->viewsData->thisApplication = $this->applicationInfo;

		return $this->generateView();
	}

	public function edit()
	{
		$modules = $this->packages->use(Modules::class);

		$settingsModule = $modules->updateModuleSettings($this->postData);

		if ($this->postData['type'] === 'applications') {

			$this->viewsData->responseCode = $settingsModule->packagesData['responseCode'];

			$this->viewsData->responseMessage = $settingsModule->packagesData['responseMessage'];

			$this->flash->now('success', $settingsModule->packagesData['responseMessage']);

		} else if ($this->postData['type'] === 'components') {

			$this->viewsData->responseCode = $settingsModule->packagesData['responseCode'];

			$this->viewsData->responseMessage = $settingsModule->packagesData['responseMessage'];

			$this->flash->now('success', $settingsModule->packagesData['responseMessage']);

		} else if ($this->postData['type'] === 'packages') {

			$this->viewsData->responseCode = $settingsModule->packagesData['responseCode'];

			$this->viewsData->responseMessage = $settingsModule->packagesData['responseMessage'];

			$this->flash->now('success', $settingsModule->packagesData['responseMessage']);

		} else if ($this->postData['type'] === 'middlewares') {

			$this->viewsData->responseCode = $settingsModule->packagesData['responseCode'];

			$this->viewsData->responseMessage = $settingsModule->packagesData['responseMessage'];

			$this->flash->now('success', $settingsModule->packagesData['responseMessage']);

		} else if ($this->postData['type'] === 'views') {

			$this->viewsData->responseCode = $settingsModule->packagesData['responseCode'];

			$this->viewsData->responseMessage = $settingsModule->packagesData['responseMessage'];

			$this->flash->now('success', $settingsModule->packagesData['responseMessage']);
		}

		return $this->generateView();
	}
}