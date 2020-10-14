<?php

namespace Applications\Admin\Packages;

use Applications\Admin\Packages\Barebone\Tasks\All;
use Applications\Admin\Packages\Barebone\Tasks\Component;
use Applications\Admin\Packages\Barebone\Tasks\Middleware;
use Applications\Admin\Packages\Barebone\Tasks\Package;
use Applications\Admin\Packages\Barebone\Tasks\View;
use System\Base\BasePackage;

class Barebone extends BasePackage
{
	protected $applicationName;

	protected $applicationDescription;

	protected $defaultApplication;

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

	public function getApplicationComponentsViews($postData)
	{
		$this->packagesData->applicationComponents =
			$this->modules->components->getComponentsForApplication($postData['application_id']);

		$this->packagesData->applicationViews =
			$this->modules->views->getViewsForApplication($postData['application_id']);

		if ($this->packagesData->applicationComponents && $this->packagesData->applicationViews) {

			$this->packagesData->responseCode = 0;

			return $this->packagesData;
		} else {
			$this->packagesData->responseCode = 1;

			return false;
		}

	}
}