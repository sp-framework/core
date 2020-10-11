<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications as ApplicationsModel;

class Applications extends BasePackage
{
	protected $modelToUse = ApplicationsModel::class;

	protected $packageName = 'applications';

	public $applications;

	protected $applicationInfo = null;

	protected $defaults = null;

	// public function getAll(bool $resetCache = false)
	// {
	// 	$parameters = $this->cacheTools->addModelCacheParameters([], $this->getCacheKey());

	// 	if (!$this->applications || $resetCache) {

	// 		$this->model = ApplicationsModel::find($parameters);

	// 		$this->applications = $this->model->toArray();
	// 	}

	// 	return $this;
	// }

	// public function getById(int $id = null, bool $resetCache = false)
	// {
	// 	$parameters = $this->paramsWithCache($this->getIdParams($id));

	// 	$this->model = ApplicationsModel::find($parameters);

	// 	return $this->getDbData($parameters);
	// }

	// public function getByParams($params = null, bool $resetCache = false)
	// {
	// 	$parameters = $this->cacheTools->addModelCacheParameters($params, $this->getCacheKey());

	// 	$this->model = ApplicationsModel::find($parameters);

	// 	return $this->getDbData($parameters);
	// }

	// protected function getDbData($parameters)
	// {
	// 	if ($this->model->count() === 1) {
	// 		$this->packagesData->responseCode = 0;
	// 		$this->packagesData->responseMessage = 'Found';

	// 		array_push($this->cacheKeys, $parameters['cache']['key']);

	// 		return $this->model->toArray()[0];

	// 	} else if ($this->model->count() > 1) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';

	// 	} else if ($this->model->count() === 0) {
	// 		$this->packagesData->responseCode = 1;
	// 		$this->packagesData->responseMessage = 'No Record Found!';
	// 	}

	// 	$this->cacheTools->deleteCache($parameters['cache']['key']); //We delete cache on error.

	// 	return false;
	// }

	// public function add(array $data)
	// {
	// 	try {
	// 		$txManager = new Manager();
	// 		$transaction = $txManager->get();

	// 		$application = new ApplicationsModel();

	// 		$application->setTransaction($transaction);

	// 		$application->assign($data);

	// 		$create = $application->create();

	// 		if (!$create) {
	// 			$transaction->rollback('Could not add application.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			$this->resetCache();

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Added application!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function update(array $data)
	// {
	// 	try {
	// 		$txManager = new Manager();
	// 		$transaction = $txManager->get();

	// 		$application = new ApplicationsModel();

	// 		$application->setTransaction($transaction);

	// 		$application->assign($data);

	// 		if (!$application->update()) {
	// 			$transaction->rollback('Could not update application.');
	// 		}

	// 		if ($transaction->commit()) {
	// 			//Delete Old cache if exists and generate new cache
	// 			$this->updateCache($data['id']);

	// 			$this->packagesData->responseCode = 0;

	// 			$this->packagesData->responseMessage = 'Application Updated!';

	// 			return true;
	// 		}
	// 	} catch (\Exception $e) {
	// 		throw $e;
	// 	}
	// }

	// public function remove(int $id)
	// {
	// 	//Need to solve dependencies for removal
	// 	// $this->get($id);

	// 	// if ($this->model->count() === 1) {
	// 	// 	if ($this->model->delete()) {

	// 	// 		$this->resetCache($id);

	// 	// 		$this->packagesData->responseCode = 0;
	// 	// 		$this->packagesData->responseMessage = 'Application Deleted!';
	// 	// 		return true;
	// 	// 	} else {
	// 	// 		$this->packagesData->responseCode = 1;
	// 	// 		$this->packagesData->responseMessage = 'Could not delete application.';
	// 	// 	}
	// 	// } else if ($this->model->count() > 1) {
	// 	// 	$this->packagesData->responseCode = 1;
	// 	// 	$this->packagesData->responseMessage = 'Duplicate Id found! Database Corrupt';
	// 	// } else if ($this->model->count() === 0) {
	// 	// 	$this->packagesData->responseCode = 1;
	// 	// 	$this->packagesData->responseMessage = 'No Record Found with that ID!';
	// 	// }
	// }

	public function getApplicationInfo()
	{
		if (isset($this->applicationInfo)) {
			return $this->applicationInfo;
		} else {
			if ($this->checkApplicationRegistration($this->getApplicationName())) {
				return $this->applicationInfo;
			} else {
				// Throw Error
			}
		}
	}

	protected function getApplicationName()
	{
		$uri = $this->request->getURI();

		$uri = explode('?', $uri);

		if ($uri[0] === '/') {
			if (!$this->defaults) {
				$this->getDefaults();

				if ($this->defaults) {
					return $this->defaults['application'];
				} else {
					return null;
				}
			} else {
				return null;
			}
		} else {
			return explode('/', $uri[0])[1];
		}
	}

	protected function getDefaults($name = null)
	{
		if ($name) {

			$namedApplication = $this->getNamedApplication($name);

			if ($namedApplication) {
				$application = $namedApplication;
			} else {
				$application = null;
			}

		} else if (!$this->defaults) {

			$defaultApplication = $this->getDefaultApplication();

			if ($defaultApplication) {
				$application = $defaultApplication;
			} else {
				$application = null;
			}

		}

		if (isset($application)) {
			$this->defaults['id'] = $application['id'];

			$this->defaults['application'] = $application['name'];

			$this->defaults['view'] = json_decode($application['settings'], true)['view'];

			$this->defaults['component'] = json_decode($application['settings'], true)['component'];

			return $this->defaults;
		}
	}

	public function getApplicationDefaults($name = null)
	{
		if (isset($this->defaults) && $name) {
			if ($name === $this->defaults['application']) {
				return $this->defaults;
			} else {
				return $this->getDefaults($name);
			}
		} else if (isset($this->defaults)) {
			return $this->defaults;
		}

		return $this->getDefaults($name);
	}

	protected function checkApplicationRegistration($name)
	{
		//First entry to check for application.
		$application = $this->getNamedApplication($name);

		$route = $this->getRouteApplication($name);

		if (count($application) > 0 || count($route) > 0) {
			if ($application) {
				$this->applicationInfo = $application;
			} else if ($route) {
				$this->applicationInfo = $route;
			}
			return true;
		} else {
			return false;
		}
	}

	protected function getNamedApplication($name)
	{
		return $this->applications
			[
				array_search(ucfirst($name), array_column($this->applications, 'name'))
			];
	}

	protected function getRouteApplication($route)
	{
		return $this->applications
			[
				array_search($route, array_column($this->applications, 'route'))
			];
	}

	protected function getDefaultApplication()
	{
		return $this->applications
			[
				array_search('1', array_column($this->applications, 'is_default'))
			];
	}
}