<?php

namespace Apps\Ecom\Admin\Packages\Barebone;

use Apps\Ecom\Admin\Packages\Barebone\Tasks\All;
use Apps\Ecom\Admin\Packages\Barebone\Tasks\Component;
use Apps\Ecom\Admin\Packages\Barebone\Tasks\Middleware;
use Apps\Ecom\Admin\Packages\Barebone\Tasks\Package;
use Apps\Ecom\Admin\Packages\Barebone\Tasks\View;
use System\Base\BasePackage;

class Barebone extends BasePackage
{
	protected $appName;

	protected $appDescription;

	protected $defaultApp;

	public function install($postData)
	{
		if ($postData['task'] === 'all') {

			$task = new All;

		} else if ($postData['task'] === 'component') {

			$task = new Component;

		} else if ($postData['task'] === 'package') {

			$task = new Package;

		} else if ($postData['task'] === 'middleware') {

			$task = new Middleware;

		} else if ($postData['task'] === 'view') {

			$task = new View;
		}

		$taskResult = $task->run($postData);

		$this->packagesData = $task->packagesData;

		return $taskResult;
	}

	public function getAppComponentsViews($postData)
	{
		$this->packagesData->appComponents =
			$this->modules->components->getComponentsForApp($postData['app_id']);

		$this->packagesData->appViews =
			$this->modules->views->getViewsForApp($postData['app_id']);

		if ($this->packagesData->appComponents && $this->packagesData->appViews) {

			$this->packagesData->responseCode = 0;

			return $this->packagesData;
		} else {
			$this->packagesData->responseCode = 1;

			return false;
		}

	}
}