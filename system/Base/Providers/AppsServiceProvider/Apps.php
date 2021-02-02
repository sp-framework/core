<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Apps\Types;
use System\Base\Providers\AppsServiceProvider\Model\Apps as AppsModel;

class Apps extends BasePackage
{
	protected $modelToUse = AppsModel::class;

	protected $packageName = 'apps';

	public $apps;

	public $types;

	protected $appInfo = null;

	public function init(bool $resetCache = false)
	{
		$this->types =
			(new Types)->init($resetCache)->types;

		$this->getAll($resetCache);

		return $this;
	}

	public function getAppInfo()
	{
		if (isset($this->appInfo)) {
			return $this->appInfo;
		} else {
			if ($this->checkAppRegistration($this->getAppRoute())) {
				return $this->appInfo;
			}
		}
		return null;
	}

	protected function getAppRoute()
	{
		$uri = $this->request->getURI();

		$uri = explode('/q/', $uri);

		$domain = $this->domains->getDomain();

		if ($uri[0] === '/') {
			if ($domain) {
				return $this->getIdApp($domain['default_app_id'])['route'];
			}
			return null;
		} else {
			if (isset($domain['exclusive_to_default_app']) &&
				$domain['exclusive_to_default_app'] == 1
			) {
				return $this->getIdApp($domain['default_app_id'])['route'];
			}
			return explode('/', $uri[0])[1];
		}
	}

	protected function checkAppRegistration($route)
	{
		$app = $this->getRouteApp($route);

		if ($app) {
			$this->appInfo = $app;

			return true;
		} else {
			return false;
		}
	}

	public function getIdApp($id)
	{
		$filter =
			$this->model->filter(
				function($app) use ($id) {
					$app = $app->toArray();
					if ($app['id'] == $id) {
						return $app;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate app Id found for id ' . $id);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getNamedApp($name)
	{
		$filter =
			$this->model->filter(
				function($app) use ($name) {
					$app = $app->toArray();
					if ($app['name'] === ucfirst($name)) {
						return $app;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate app name found for app ' . $name);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}
	}

	public function getRouteApp($route)
	{
		$filter =
			$this->model->filter(
				function($app) use ($route) {
					$app = $app->toArray();
					if ($app['route'] === ($route)) {
						return $app;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate app route found for route ' . $route);
		} else if (count($filter) === 1) {
			return $filter[0];
		} else {
			return false;
		}

	}

	public function getDefaultApp()
	{
		$filter =
			$this->model->filter(
				function($app) {
					if ($app->is_default === '1') {
						return $app;
					}
				}
			);

		if (count($filter) > 1) {
			throw new \Exception('Duplicate default app for app. DB Corrupt');
		} else if (count($filter) === 1) {
			return $filter[0]->toArray();
		} else {
			return false;
		}
	}

	public function addApp(array $data)
	{
		if (!$this->checkType($data)) {
			return;
		}

		if ($this->getRouteApp($data['route'])) {
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

			$this->packagesData->responseMessage = 'Added ' . $data['name'] . ' app';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error adding new app.';
		}
	}

	public function updateApp(array $data)
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

			$this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' app';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating app.';
		}
	}

	protected function checkType($data)
	{
		$typesArr = $this->types;

		foreach ($typesArr as $key => $type) {
			if (strtolower($data['route']) === $type['app_type']) {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'App route ' . strtolower($data['route']) . ' is reserved. Please use different route.';

				return false;
			}
		}

		return true;
	}

	public function removeApp(array $data)
	{
		if ($data['id'] == 1) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Cannot remove Admin App. Error removing app.';

			return false;
		}

		$app = $this->getById($data['id']);

		//Check relations before removing.
		if ($this->remove($data['id'])) {
			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Removed app';
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error removing app.';
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