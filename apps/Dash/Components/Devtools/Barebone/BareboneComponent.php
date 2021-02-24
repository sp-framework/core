<?php

namespace Apps\Dash\Components\Devtools\Barebone;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Barebone\Barebone;
use System\Base\BaseComponent;

class BareboneComponent extends BaseComponent
{
	use DynamicTable;

	protected $barebonePackage;

	public function initialize()
	{
		$barebonePackage = $this->usePackage(Barebone::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		$this->view->apps = $this->apps;
	}

	/**
	 * @acl(name=install)
	 */
	public function installAction()
	{
		$bareboneModule = $barebonePackage->install($this->postData());

		if ($bareboneModule) {
			if ($this->postData()['task'] !== 'view') {
				$this->view->bareboneModule = $barebonePackage->packagesData->bareboneModule;
			}

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->responseMessage = $barebonePackage->packagesData->responseMessage;
		} else {

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->responseMessage = $barebonePackage->packagesData->responseMessage;

		}
		return $this->sendJson();
	}

	public function getSelectedAppViewsComponentsAction()
	{
		$barebonePackage = $this->usePackage(Barebone::class);

		$bareboneModule = $barebonePackage->getAppComponentsViews($this->postData());

		if ($bareboneModule) {

			$this->view->responseCode = $barebonePackage->packagesData->responseCode;

			$this->view->appComponents = $barebonePackage->packagesData->appComponents;

			$this->view->appViews = $barebonePackage->packagesData->appViews;

		} else {

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Could not get apps components/views data.';

			return $this->sendJson();
		}
	}
}