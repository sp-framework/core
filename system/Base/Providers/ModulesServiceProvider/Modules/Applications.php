<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\ApplicationTypes;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Applications as ApplicationsModel;

class Applications extends BasePackage
{
	protected $modelToUse = ApplicationsModel::class;

	protected $packageName = 'applications';

	public $applications;

	public $applicationTypes;

	protected $applicationInfo = null;

	public function init(bool $resetCache = false)
	{
		$this->applicationTypes =
			(new ApplicationTypes)->init($resetCache)->applicationTypes;

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

		$domain = $this->basepackages->domains->getDomain();

		if ($uri[0] === '/') {
			if ($domain) {
				return $this->getIdApplication($domain['default_application_id'])['route'];
			}
			return null;
		} else {
			if (isset($domain['exclusive_to_default_application']) &&
				$domain['exclusive_to_default_application'] == 1
			) {
				return $this->getIdApplication($domain['default_application_id'])['route'];
			}
			return explode('/', $uri[0])[1];
		}
	}

	protected function checkApplicationRegistration($route)
	{
		$application = $this->getRouteApplication($route);

		if ($application) {
			$this->applicationInfo = $application;

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

	public function addApplication(array $data)
	{
		if (!$this->checkType($data)) {
			return;
		}

		if ($this->getRouteApplication($data['route'])) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'App route ' . strtolower($data['route']) . ' is used by another app. Please use different route.';

			return false;
		}

		$data['default_component'] = 0;
		$data['errors_component'] = 0;
		$data['can_login_role_ids'] = Json::decode($data['can_login_role_ids'], true);
		$data['can_login_role_ids'] = Json::encode($data['can_login_role_ids']['data']);

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
		if (isset($data['modules']) && $data['modules'] == true) {
			$app = $this->getById($data['id']);

			$data = array_merge($app, $data);
		}

		if (!$this->checkType($data)) {
			return;
		}

		if (!isset($data['modules'])) {
			$data['can_login_role_ids'] = Json::decode($data['can_login_role_ids'], true);
			$data['can_login_role_ids'] = Json::encode($data['can_login_role_ids']['data']);
		}

		if (isset($data['modules']) && $data['modules'] == true) {
			if ($data['components']) {
				$this->modules->components->updateComponents($data);
			}

			if ($data['menus']) {
				$this->basepackages->menus->updateMenus($data);
			}

			if ($data['views']) {
				$this->modules->views->updateViews($data);
			}

			if ($data['middlewares']) {
				$this->modules->middlewares->updateMiddlewares($data);
			}
		}

		if ($this->update($data)) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' application';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating application.';
		}
	}

	protected function checkType($data)
	{
		$typesArr = $this->modules->applications->applicationTypes;

		foreach ($typesArr as $key => $type) {
			if (strtolower($data['route']) === $type['app_type']) {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'App route ' . strtolower($data['route']) . ' is reserved. Please use different route.';

				return false;
			}
		}

		return true;
	}

	public function removeApplication(array $data)
	{
		if ($data['id'] == 1) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Cannot remove Admin App. Error removing application.';

			return false;
		}

		$application = $this->getById($data['id']);

		//Check relations before removing.
		if ($this->remove($data['id'])) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Removed application';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error removing application.';
		}
	}

	// public function getAppCategories()
	// {
	// 	return
	// 		[
	// 			'1'   =>
	// 				[
	// 					'id'   => 'ecom',
	// 					'name' => 'E-Commerce Management System'
	// 				],
	// 			'2'    =>
	// 				[
	// 					'id'   => 'tms',
	// 					'name' => 'Transport Management System'
	// 				]
	// 		];
	// }

	// public function getAppSubCategories()
	// {
	// 	return
	// 		[
	// 			'1'	  =>
	// 				[
	// 					'id'  		=> 'admin',
	// 					'parent'	=> 'ecom',
	// 					'name' 		=> 'Admin'
	// 				],
	// 			'2'   =>
	// 				[
	// 					'id'  		=> 'dashboard',
	// 					'parent'	=> 'ecom',
	// 					'name' 		=> 'Dashboard'
	// 				],
	// 			'3'   =>
	// 				[
	// 					'id'  		=> 'eshop',
	// 					'parent'	=> 'ecom',
	// 					'name' 		=> 'EShop'
	// 				],
	// 			'4'   =>
	// 				[
	// 					'id'  		=> 'pos',
	// 					'parent'	=> 'ecom',
	// 					'name' 		=> 'PoS'
	// 				]
	// 		];
	// }
}