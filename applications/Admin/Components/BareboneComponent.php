<?php

namespace Applications\Admin\Components;

use Applications\Admin\Packages\Modules\Barebone;
use System\Base\BaseComponent;

class BareboneComponent extends BaseComponent
{
	public function viewAction()
	{
		//
	}

	public function installAction()
	{
		$barebonePackage = $this->usePackage(Barebone::class);

		$bareboneModule = $barebonePackage->runProcess($this->postData());

		if ($bareboneModule) {

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->bareboneModule = $barebonePackage->packagesData->bareboneModule;

			$this->view->responseMessage = $barebonePackage->packagesData->responseMessage;
		} else {

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->responseMessage = $barebonePackage->packagesData->responseMessage;

			return $this->sendJson();
		}

	}

	public function getSelectedApplicationViewsComponentsAction()
	{
		$barebonePackage = $this->usePackage(Barebone::class);

		$bareboneModule = $barebonePackage->getApplicationComponentsViews($this->postData());

		if ($bareboneModule) {

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->applicationComponents = $barebonePackage->packagesData->applicationComponents;

			$this->view->applicationViews = $barebonePackage->packagesData->applicationViews;

		} else {

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Could not get applications components data.';

			return $this->sendJson();
		}
	}
}