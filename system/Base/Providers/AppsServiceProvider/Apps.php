<?php

namespace System\Base\Providers\AppsServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Apps\Types;
use System\Base\Providers\AppsServiceProvider\Exceptions\AppNotFoundException;
use System\Base\Providers\AppsServiceProvider\IpFilter;
use System\Base\Providers\AppsServiceProvider\Model\Apps as AppsModel;
use System\Base\Providers\AppsServiceProvider\Model\AppsIpFilter;

class Apps extends BasePackage
{
	protected $modelToUse = AppsModel::class;

	protected $packageName = 'apps';

	public $apps;

	public $types;

	public $ipFilter;

	protected $reservedRoutes;

	protected $appInfo = null;

	public function init(bool $resetCache = false)
	{
		$this->types = $this->getAppTypes();

		$this->reservedRoutes = $this->getReservedRoutes();

		$this->getAll($resetCache);

		$this->app = $this->getAppInfo();

		$this->ipFilter = new IpFilter;

		return $this;
	}

	public function getAppInfo()
	{
		if (PHP_SAPI === 'cli') {
			$this->appInfo = $this->getRouteApp('admin');
		}

		if (isset($this->appInfo)) {
			return $this->appInfo;
		} else {
			if ($this->checkAppRegistration($this->getAppRoute())) {
				return $this->appInfo;
			}
		}

		throw new AppNotFoundException();
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
		foreach($this->apps as $app) {
			if ($app['id'] == $id) {
				return $app;
			}
		}

		return false;
	}

	public function getNamedApp($name)
	{
		foreach($this->apps as $app) {
			if (strtolower($app['name']) === strtolower($name)) {
				return $app;
			}
		}

		return false;
	}

	public function getRouteApp($route)
	{
		foreach($this->apps as $app) {
			if (strtolower($app['route']) == strtolower($route)) {
				return $app;
			}
		}

		return false;
	}

	public function getDefaultApp()
	{
		foreach($this->apps as $app) {
			if ($app['is_default'] == '1') {
				return $app;
			}
		}

		return false;
	}

	/**
	 * @notification(name=add)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function addApp(array $data)
	{
		if (!$this->checkType($data)) {
			return;
		}

		if ($this->getRouteApp($data['route'])) {
			$this->addResponse('App route ' . strtolower($data['route']) . ' is used by another app. Please use different route.', 1, []);

			return false;
		}

		$data['default_component'] = 0;
		$data['errors_component'] = 0;
		$data['ip_filter_default_action'] = 0;
		$data['can_login_role_ids'] = Json::encode(['1']);

		if ($this->add($data)) {

			$this->addActivityLog($data);

			$this->addResponse('Added ' . $data['name'] . ' app', 0, null, true);

			$this->addToNotification('add', 'Added new app ' . $data['name']);
		} else {
			$this->addResponse('Error adding new app.', 1, []);
		}
	}

	/**
	 * @notification(name=update)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function updateApp(array $data)
	{
		$app = $this->getById($data['id']);

		$app = array_merge($app, $data);

		if (isset($app['reset_structure']) && $app['reset_structure'] == '1') {
			$app['menu_structure'] = null;
		}

		if (isset($app['can_login_role_ids'])) {
			$app['can_login_role_ids'] = Json::decode($app['can_login_role_ids'], true);

			if (isset($app['can_login_role_ids']['data'])) {
				$app['can_login_role_ids'] = Json::encode($app['can_login_role_ids']['data']);
			} else {
				$app['can_login_role_ids'] = Json::encode($app['can_login_role_ids']);
			}
		}

		if (isset($app['components'])) {
			$this->modules->components->updateComponents($app);
		}

		if (isset($app['menus'])) {
			$this->basepackages->menus->updateMenus($app);
		}

		if (isset($app['middlewares'])) {
			$this->modules->middlewares->updateMiddlewares($app);
		}

		if (isset($app['views'])) {
			$this->modules->views->updateViews($app);
		}

		if ($this->update($app)) {
			$this->addActivityLog($data, $app);

			$this->addToNotification('update', 'Updated app ' . $app['name']);

			$this->addResponse('Updated ' . $app['name'] . ' app');
		} else {
			$this->addResponse('Error updating app.', 1);
		}
	}

	public function getFilters(array $data)
	{
		if (!isset($data['app_id'])) {
			return Json::encode([]);//blank array
		}

		$app = $this->getFirst('id', $data['app_id']);

		$filtersObj = $app->getIpFilters();

		if ($filtersObj && $filtersObj->count() > 0) {
			$filters = $filtersObj->toArray();
		} else {
			$filters = [];
		}

		return $filters;
	}

	public function addFilter(array $data)
	{
		if (!isset($data['filter_type']) || !isset($data['ip_address'])) {
			$this->addResponse('Please provide correct address and filter type', 1);

			return false;
		}

		if ($data['filter_type'] !== 'allow' && $data['filter_type'] !== 'block') {
			$this->addResponse('Please provide correct filter type', 1);

			return false;
		}

		$address = explode('/', $data['ip_address']);

		if (count($address) === 2 || count($address) === 1) {
			//validate address
			$ipFilter = new IpFilter;

			$validateIP = $ipFilter->validateIp($address[0]);

			if ($validateIP !== true) {
				$this->addResponse($validateIP, 1);

				return;
			}
			if (count($address) === 2) {
				$data['address_type'] = 2;
			} else if (count($address) === 1) {
				$data['address_type'] = 1;
			}
		} else {
			$this->addResponse('Please enter correct address', 1);

			return false;
		}

		$filtersObj = new AppsIpFilter();

		$newFilter['app_id'] = $data['app_id'];
		$newFilter['ip_address'] = $data['ip_address'];
		$newFilter['address_type'] = $data['address_type'];
		$newFilter['filter_type'] = $data['filter_type'] === 'allow' ? 1 : 2;
		$newFilter['added_by'] = $this->auth->account()['id'];

		$filtersObj->assign($newFilter);

		try {
			$filtersObj->create();
		} catch (\Exception $e) {
			if (strpos($e->getMessage(), 'UNIQUE') !== false) {
				$this->addResponse('Duplicate Entry!', 3);

				return false;
			}

			throw $e;
		}
	}

	public function removeFilter(array $data)
	{
		if (!isset($data['id'])) {
			$this->addResponse('Please provide correct filter ID', 1);

			return false;
		}

		$filtersObj = new AppsIpFilter();

		$filter = $filtersObj->findFirst('id = ' . $data['id']);

		if ($filter) {
			if ($filter->delete() !== false) {
				$this->addResponse('Filter removed.', 0);
			} else {
				$this->addResponse('Error removing filter.', 1);
			}
		}
	}

	public function blockMonitorFilter(array $data)
	{
		if (!isset($data['id'])) {
			$this->addResponse('Please provide correct filter ID', 1);

			return false;
		}

		$filtersObj = new AppsIpFilter();

		$monitorFilterObj = $filtersObj->findFirst('id = ' . $data['id']);

		if ($monitorFilterObj) {
			$monitorFilter = $monitorFilterObj->toArray();
			$monitorFilter['filter_type'] = 2;
			$monitorFilter['added_by'] = $this->auth->account()['id'];
			$monitorFilter['incorrect_attempts'] = null;

			$monitorFilterObj->assign($monitorFilter);

			if ($monitorFilterObj->update() !== false) {
				$this->addResponse('Filter moved from monitor to block.', 0);
			} else {
				$this->addResponse('Error blocking filter.', 1);
			}
		}
	}

	public function resetAppFilters(array $data)
	{
		if (!isset($data['app_id'])) {
			$this->addResponse('Incorrect App ID', 1);

			return;
		}

		$app = $this->getFirst('id', $data['app_id']);

		$filtersObj = $app->getIpFilters();

		if ($filtersObj && $filtersObj->count() > 0) {
			$filtersObj->delete();
		}

		$app->assign(['incorrect_login_attempt_block_ip' => 0, 'ip_filter_default_action' => 0]);

		$app->update();
	}

	protected function checkType($data)
	{
		$typesArr = $this->types;

		foreach ($typesArr as $key => $type) {
			if (strtolower($data['route']) === $type['app_type'] ||
				in_array(strtolower($data['route']), $this->reservedRoutes)
			) {
				$this->addResponse('App route ' . strtolower($data['route']) . ' is reserved. Please use different route.', 1, []);

				return false;
			}
		}

		return true;
	}

	/**
	 * @notification(name=remove)
	 * notification_allowed_methods(email, sms)//Example
	 * @notification_allowed_methods(email, sms)
	 */
	public function removeApp(array $data)
	{
		if ($data['id'] == 1) {
			$this->addResponse('Cannot remove Admin App. Error removing app.', 1);

			return false;
		}

		$app = $this->getById($data['id']);

		//Check relations before removing.
		if ($this->remove($data['id'])) {

			$this->domains->removeAppFromApps($data['id']);

			$this->addToNotification('remove', 'Removed app ' . $app['name']);

			$this->addResponse('Removed App ' . $app['name']);
		} else {
			$this->addResponse('Error removing app.', 1);
		}
	}

	protected function getReservedRoutes()
	{
		return
			[
				'admin', 'dash', 'ecom', 'pos', 'cms', 'api', 'pusher', 'messenger'
			];
	}

	public function getAppTypes()
	{
		return
			[
				'1'   =>
					[
						'app_type'      => 'dash',
						'name'          => 'Dashboard',
						'description'   => 'Dashboard. Can run modules that require a dashboard, like Admin, Cpanel or Dashboard.',
					],
				'2'    =>
					[
						'app_type'      => 'ecom',
						'name'          => 'E-Commerce E-Shop',
						'description'   => 'Online product catalogue and checkout system.',
					],
				'3'    =>
					[
						'app_type'      => 'pos',
						'name'          => 'Point of Sales System',
						'description'   => 'In-store checkout system.',
					],
				'4'    =>
					[
						'app_type'      => 'cms',
						'name'          => 'Content Management System',
						'description'   => 'App to display any web content. Like a blog.',
					]
			];
	}
}