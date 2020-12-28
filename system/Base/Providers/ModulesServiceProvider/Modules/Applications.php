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
		} else {
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
		if (strtolower($data['route']) === 'common') {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'App route "common" is reserved. Please use different route.';

			return;
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

		if (strtolower($data['route']) === 'common') {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'App route "common" is reserved. Please use different route.';

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

	public function getAppCategories()
	{
		return
			[
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
				'1'	  =>
					[
						'id'  		=> 'admin',
						'parent'	=> 'ecom',
						'name' 		=> 'Admin'
					],
				'2'   =>
					[
						'id'  		=> 'dashboard',
						'parent'	=> 'ecom',
						'name' 		=> 'Dashboard'
					],
				'3'   =>
					[
						'id'  		=> 'eshop',
						'parent'	=> 'ecom',
						'name' 		=> 'EShop'
					],
				'4'   =>
					[
						'id'  		=> 'pos',
						'parent'	=> 'ecom',
						'name' 		=> 'PoS'
					]
			];
	}
}