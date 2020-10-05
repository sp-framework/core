<?php

namespace Components\Admin\Modules;

use Packages\Admin\Modules;
use System\Base\BaseComponent;

class Barebone extends BaseComponent
{
	public function Install()
	{
		$modules = $this->packages->use(Modules::class);

		$bareboneModule = $modules->installBareboneModules($this->postData);

		if ($bareboneModule->packagesData['responseCode'] === 0) {

			$this->viewsData->responseCode = $bareboneModule->packagesData['responseCode'];

			$this->viewsData->bareboneModule = $bareboneModule->packagesData['bareboneModule'];

		} else {

			$this->viewsData->responseCode = $bareboneModule->packagesData['responseCode'];
		}

		$this->viewsData->responseMessage = $bareboneModule->packagesData['responseMessage'];

		return $this->generateView();
	}

	public function getSelectedApplicationViewsComponents()
	{
		$modules = $this->packages->use(Modules::class);

		$getData = $modules->getApplicationComponentsViews($this->postData);

		if ($getData->packagesData['responseCode'] === 0) {

			$this->viewsData->responseCode = $getData->packagesData['responseCode'];

			$this->viewsData->applicationComponents = $getData->packagesData['applicationComponents'];

			$this->viewsData->applicationViews = $getData->packagesData['applicationViews'];

		} else {

			$this->viewsData->responseCode = 1;

			$this->viewsData->responseMessage = 'Could not get applications components data.';
		}

		return $this->generateView();
	}
}