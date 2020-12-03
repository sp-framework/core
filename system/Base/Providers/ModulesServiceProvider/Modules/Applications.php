<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications as ApplicationsModel;

class Applications extends BasePackage
{
	protected $modelToUse = ApplicationsModel::class;

	protected $packageName = 'applications';

	public $applications;

	protected $applicationInfo = null;

	// protected $defaults = null;

	public function init(bool $resetCache = false)
	{
		$this->getAll($resetCache);

		return $this;
	}

	public function getApplicationInfo()
	{
		if (isset($this->applicationInfo)) {
			return $this->applicationInfo;
		} else {
			if ($this->checkApplicationRegistration($this->getApplicationRoute())) {
				return $this->applicationInfo;
			}
		}
		return null;
	}

	protected function getApplicationRoute()
	{
		$uri = $this->request->getURI();

		$uri = explode('/q/', $uri);

		if ($uri[0] === '/') {
			if ($this->basepackages->domains->getDomain()) {
				return $this->getIdApplication($this->basepackages->domains->getDomain()['default_application_id'])['route'];
			}
			return null;
			// if (!$this->defaults) {

			// 	$this->getDefaults();

			// 	if ($this->defaults) {
			// 		return $this->defaults['application'];
			// 	} else {
			// 		return null;
			// 	}
			// } else {
			// 	return null;
			// }
		} else {
			return explode('/', $uri[0])[1];
		}
	}

	// protected function getDefaults($name = null)
	// {
	// 	if ($name) {

	// 		$namedApplication = $this->getNamedApplication($name);

	// 		if ($namedApplication) {
	// 			$application = $namedApplication;
	// 		} else {
	// 			$application = null;
	// 		}

	// 	} else if (!$this->defaults) {

	// 		$defaultApplication = $this->getDefaultApplication();

	// 		if ($defaultApplication) {
	// 			$application = $defaultApplication;
	// 		} else {
	// 			$application = null;
	// 		}

	// 	}

	// 	if (isset($application)) {
	// 		$this->defaults['id'] = $application['id'];

	// 		$this->defaults['application'] = $application['name'];

	// 		$this->defaults['view'] = json_decode($application['settings'], true)['view'];

	// 		$this->defaults['component'] = json_decode($application['settings'], true)['component'];

	// 		return $this->defaults;
	// 	}
	// }

	// public function getApplicationDefaults($name = null)
	// {
	// 	if (isset($this->defaults) && $name) {
	// 		if ($name === $this->defaults['application']) {
	// 			return $this->defaults;
	// 		} else {
	// 			return $this->getDefaults($name);
	// 		}
	// 	} else if (isset($this->defaults)) {
	// 		return $this->defaults;
	// 	}

	// 	return $this->getDefaults($name);
	// }

	protected function checkApplicationRegistration($route)
	{
		//First entry to check for application.
		// $application = $this->getNamedApplication($name);
		$application = $this->getRouteApplication($route);

		if ($application) {
			// if ($application) {
			// 	$this->applicationInfo = $application;
			// } else if ($route) {
				$this->applicationInfo = $application;
			// }
			return true;
		} else {
			return false;
		}
	}

	public function getIdApplication($id)
	{
		$filter =
			$this->model->filter(
				function($application) use ($id) {
					if ($application->id == $id) {
						return $application;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate application Id found for id ' . $id);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function getNamedApplication($name)
	{
		$filter =
			$this->model->filter(
				function($application) use ($name) {
					if ($application->name === ucfirst($name)) {
						return $application;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate application name found for application ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function getRouteApplication($route)
	{
		$filter =
			$this->model->filter(
				function($application) use ($route) {
					if ($application->route === ($route)) {
						return $application;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate application route found for route ' . $route);
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}

	}

	public function getDefaultApplication()
	{
		$filter =
			$this->model->filter(
				function($application) {
					if ($application->is_default === '1') {
						return $application;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate default application for application. DB Corrupt');
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	// public function removeDefaultFlag()
	// {
	// 	$defaultApplication = $this->getDefaultApplication();

	// 	if ($defaultApplication) {

	// 		$defaultApplication['is_default'] = 0;

	// 		$this->modules->applications->update($defaultApplication);
	// 	}
	// }

	public function addApplication(array $data)
	{
		// if (!$this->addApplicationStructure($data['route'])) {
		// 	$this->packagesData->responseCode = 1;

		// 	$this->packagesData->responseMessage = 'App files already exists.';

		// 	return false;
		// }

		if ($this->add($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Added ' . $data['name'] . ' application';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error adding new application.';
		}
	}

	public function updateApplication(array $data)
	{
		if ($data['middlewares']) {
			$this->modules->middlewares->updateMiddlewares(Json::decode($data['middlewares'], true));
		}

		if ($this->update($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' application';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating application.';
		}
	}

	public function removeApplication(array $data)
	{
		$application = $this->getById($data['id']);

		// if (!$this->removeApplicationStructure($application['route'])) {
		// 	$this->packagesData->responseCode = 1;

		// 	$this->packagesData->responseMessage = 'App files already exists.';

		// 	return false;
		// }
		//Check relations before removing.
		if ($this->remove($data['id'])) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Removed application';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error removing application.';
		}
	}

	// protected function addApplicationStructure(string $route)
	// {
	// 	if ($this->localContent->has('applications/' . ucfirst($route))) {
	// 		return false;
	// 	}
	// 	if ($this->localContent->has('public/' . ucfirst($route))) {
	// 		return false;
	// 	}

	// 	$this->localContent->createDir('public/' . ucfirst($route));
	// 	$this->localContent->createDir('applications/' . ucfirst($route));
	// 	$this->localContent->createDir('applications/' . ucfirst($route) . '/Components');
	// 	$this->localContent->createDir('applications/' . ucfirst($route) . '/Packages');
	// 	$this->localContent->createDir('applications/' . ucfirst($route) . '/Middlewares');
	// 	$this->localContent->createDir('applications/' . ucfirst($route) . '/Views');

	// 	return true;
	// }

	// protected function removeApplicationStructure(string $route)
	// {
	// 	$structure = [];
	// 	$structure =
	// 		array_merge(
	// 			$this->localContent->listContents('applications/' . ucfirst($route), true),
	// 			$this->localContent->listContents('public/' . ucfirst($route), true),
	// 		);

	// 	$appStructure = [];
	// 	$appStructure['dir'] = [];
	// 	$appStructure['files'] = [];

	// 	foreach ($structure as $key => $value) {
	// 		if ($value['type'] === 'dir') {
	// 			array_push($appStructure['dir'], $value['path']);
	// 		} else if ($value['type'] === 'file') {
	// 			array_push($appStructure['files'], $value['path']);
	// 		}
	// 	}

	// 	if (count($appStructure['files']) > 0) {
	// 		foreach ($appStructure['files'] as $fileKey => $file) {
	// 			$this->localContent->delete($file);
	// 		}
	// 	}

	// 	if (count($appStructure['dir']) > 0) {
	// 		foreach ($appStructure['dir'] as $dirKey => $dir) {
	// 			$this->localContent->deleteDir($dir);
	// 		}
	// 	}

	// 	$this->localContent->deleteDir('public/' . ucfirst($route));
	// 	$this->localContent->deleteDir('applications/' . ucfirst($route));

	// 	return true;
	// }

	public function getAppCategories()
	{
		return
			[
				'0'   =>
					[
						'id'   => 'core',
						'name' => 'Core'
					],
				'1'   =>
					[
						'id'   => 'ecom',
						'name' => 'E-Commerce Management System'
					],
				'2'    =>
					[
						'id'   => 'tms',
						'name' => 'Transport Management System'
					]
			];
	}

	public function getAppSubCategories()
	{
		return
			[
				'0'	  =>
					[
						'id'  		=> 'admin',
						'parent'	=> 'core',
						'name' 		=> 'Admin'
					],
				'1'   =>
					[
						'id'  		=> 'dashboard',
						'parent'	=> 'ecom',
						'name' 		=> 'Dashboard'
					],
				'2'   =>
					[
						'id'  		=> 'eshop',
						'parent'	=> 'ecom',
						'name' 		=> 'EShop'
					],
				'3'   =>
					[
						'id'  		=> 'pos',
						'parent'	=> 'ecom',
						'name' 		=> 'PoS'
					]
			];
	}
}