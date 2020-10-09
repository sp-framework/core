<?php

namespace Applications\Admin\Components\Modules;

use Applications\Admin\Packages\ModulesPackage;
use System\Base\BaseComponent;

class BareboneComponent extends BaseComponent
{
	public function installAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		$bareboneModule = $modules->installBareboneModules($this->postData);

		if ($bareboneModule->packagesData['responseCode'] === 0) {

			$this->view->responseCode = $bareboneModule->packagesData['responseCode'];

			$this->view->bareboneModule = $bareboneModule->packagesData['bareboneModule'];

		} else {

			$this->view->responseCode = $bareboneModule->packagesData['responseCode'];
		}

		$this->view->responseMessage = $bareboneModule->packagesData['responseMessage'];
	}

	public function getSelectedApplicationViewsComponentsAction()
	{
		$modules = $this->usePackage(ModulesPackage::class);

		$getData = $modules->getApplicationComponentsViews($this->postData);

		if ($getData->packagesData['responseCode'] === 0) {

			$this->view->responseCode = $getData->packagesData['responseCode'];

			$this->view->applicationComponents = $getData->packagesData['applicationComponents'];

			$this->view->applicationViews = $getData->packagesData['applicationViews'];

		} else {

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Could not get applications components data.';
		}
	}
}