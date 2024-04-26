<?php

namespace System\Base\Providers\AppsServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\AppsServiceProvider\Exceptions\AppNotFoundException;
use System\Base\Providers\AppsServiceProvider\IpFilter;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderApps;
use System\Base\Providers\AppsServiceProvider\Types;

class Apps extends BasePackage
{
	protected $modelToUse = ServiceProviderApps::class;

	protected $packageName = 'apps';

	protected $packageNameS = 'app';

	public $apps;

	public $types;

	public $ipFilter;

	protected $reservedRoutes;

	protected $appInfo = null;

	public $isMurl = false;

	public function init(bool $resetCache = false)
	{
		$this->types = (new Types)->init($resetCache);

		$this->reservedRoutes = $this->getReservedRoutes();

		$this->getAll($resetCache);

		$this->app = $this->getAppInfo();

		$this->ipFilter = (new IpFilter())->init($this, $this->app);

		return $this;
	}

	public function getAppInfo($route = null)
	{
		if (PHP_SAPI === 'cli') {
			$this->appInfo = $this->getAppByRoute($route);

			$this->ipFilter = (new IpFilter())->init($this, $this->appInfo);
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
				return $this->getAppById($domain['default_app_id'])['route'];
			}
			return null;
		} else {
			if (isset($domain['exclusive_to_default_app']) &&
				$domain['exclusive_to_default_app'] == 1
			) {
				return $this->getAppById($domain['default_app_id'])['route'];
			}

			$uri = explode("/", trim($uri[0], '/'));

			if ($this->api->isApi()) {
				$apiUri = $uri;
				if ($apiUri[0] === 'api') {
					unset($apiUri[0]);
				}

				$apiUri = array_values($apiUri);

				if ($this->api->isApiCheckVia === 'pub') {
					if (isset($apiUri[0]) &&
						$apiUri[0] === 'pub'
					) {
						unset($apiUri[0]);
					}
				}

				$apiUri = array_values($apiUri);
			}

			if ((isset($apiUri) && count($apiUri) === 1) ||
				count($uri) === 1
			) {//Check for Murl
				if (isset($apiUri)) {
					$this->isMurl = $this->basepackages->murls->getMurlByDomainId($this, trim($apiUri[0], '/'), $domain['id']);
				} else {
					$this->isMurl = $this->basepackages->murls->getMurlByDomainId($this, trim($uri[0], '/'), $domain['id']);
				}

				if ($this->isMurl) {
					return $this->getAppById($this->isMurl['app_id'])['route'];
				}
			}

			if ($uri[0] === 'api' && $uri[1] === 'pub') {
				return $uri[2];
			} else if ($uri[0] === 'api') {
				return $uri[1];
			} else if ($uri[0] === 'pub') {
				return $uri[1];
			}

			return $uri[0];
		}
	}

	protected function checkAppRegistration($route)
	{
		$app = $this->getAppByRoute($route);

		if ($app) {
			$this->appInfo = $app;

			return true;
		} else {
			return false;
		}
	}

	public function getAppById($id)
	{
		foreach($this->apps as $app) {
			if ($app['id'] == $id) {
				return $app;
			}
		}

		return false;
	}

	public function getAppByName($name)
	{
		foreach($this->apps as $app) {
			if (strtolower($app['name']) === strtolower($name)) {
				return $app;
			}
		}

		return false;
	}

	public function getAppByRoute($route)
	{
		if (!$route) {
			return false;
		}

		foreach($this->apps as $app) {
			if (strtolower($app['route']) == strtolower($route)) {
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

		if ($this->getAppByRoute($data['route'])) {
			$this->addResponse('App route ' . strtolower($data['route']) . ' is used by another app. Please use different route.', 1, []);

			return false;
		}

		$data['default_component'] = 0;
		$data['errors_component'] = 0;
		$data['ip_filter_default_action'] = 0;
		$data['can_login_role_ids'] = $this->helper->encode(['1']);
		$data['acceptable_usernames'] = $this->helper->encode(['email']);

		if (isset($data['default_dashboard']) && $data['default_dashboard']) {
			$data['settings']['defaultDashboard'] = $data['default_dashboard'];
		}

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
		if (isset($data['domains'])) {//Coming via app wizard.
			$domains = $this->helper->decode($data['domains'], true);

			if (isset($domains['data'])) {
				$domains = $domains['data'];
			}

			foreach ($domains as $domain) {
				$domain = $this->domains->getDomainById($domain);

				if (is_string($domain['apps'])) {
					$domain['apps'] = $this->helper->decode($domain['apps'], true);
				}

				$domain['apps'][$data['id']]['allowed'] = true;
				$domain['apps'][$data['id']]['api'] = false;
				$domain['apps'][$data['id']]['view'] = $data['view'];
				$domain['apps'][$data['id']]['email_service'] = $data['email'];
				$domain['apps'][$data['id']]['publicStorage'] = $data['public'];
				$domain['apps'][$data['id']]['privateStorage'] = $data['private'];

				//add new viewsettings
				$viewSettingsData = [];
				$viewSettingsData['view_id'] = $data['view'];
				$viewSettingsData['domain_id'] = $domain['id'];
				$viewSettingsData['app_id'] = $data['id'];
				$viewSettingsData['via_app'] = true;

				$this->domains->updateDomain($domain);
				$this->modules->viewsSettings->addViewsSettings($viewSettingsData);
			}

			return true;
		}

		$app = $this->getById($data['id']);

		if (isset($data['default_dashboard']) && $data['default_dashboard']) {
			$data['settings']['defaultDashboard'] = $data['default_dashboard'];
		}

		$app = array_merge($app, $data);

		if (isset($app['reset_structure']) && $app['reset_structure'] == '1') {
			$app['menu_structure'] = null;
		}

		if (isset($app['can_login_role_ids'])) {
			if (is_string($app['can_login_role_ids'])) {
				$app['can_login_role_ids'] = $this->helper->decode($app['can_login_role_ids'], true);
			}

			if (isset($app['can_login_role_ids']['data'])) {
				$app['can_login_role_ids'] = $this->helper->encode($app['can_login_role_ids']['data']);
			} else {
				$app['can_login_role_ids'] = $this->helper->encode($app['can_login_role_ids']);
			}
		}

		if (isset($app['views']) && $app['views'] !== '') {
			$views = $this->helper->decode($app['views'], true);

			foreach ($views as $viewId => $view) {
				if ($view === false) {
					$viewInfo = $this->modules->views->getViewById($viewId);

					$domainCheckAppsSettings = $this->domains->checkAppsSettings($app['id'], 'view', $viewId);

					if ($domainCheckAppsSettings) {
						$this->addResponse('View ' . $viewInfo['display_name'] . ' is being used by Domain ' . $domainCheckAppsSettings['name'], 1);

						return false;
					}
				}
			}

			$this->modules->views->updateViews($app);
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

		if ($app['acceptable_usernames']) {
			if (is_string($app['acceptable_usernames'])) {
				$app['acceptable_usernames'] = $this->helper->decode($app['acceptable_usernames'], true);
			}

			if (isset($app['acceptable_usernames']['data'])) {
				$app['acceptable_usernames'] = $this->helper->encode($app['acceptable_usernames']['data']);
			} else {
				$app['acceptable_usernames'] = $this->helper->encode($app['acceptable_usernames']);
			}
		} else {
			$app['acceptable_usernames'] = $this->helper->encode(['email']);
		}

		if ($this->update($app)) {
			$this->addActivityLog($data, $app);

			$this->addToNotification('update', 'Updated app ' . $app['name']);

			$this->addResponse('Updated ' . $app['name'] . ' app');
		} else {
			$this->addResponse('Error updating app.', 1);
		}
	}

	protected function checkType($data)
	{
		$typesArr = $this->types->types;

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
			$this->addResponse('Cannot remove core app!', 1);

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
				'core', 'dash', 'api', 'pub', 'pusher', 'messenger'
			];
	}

	public function getAcceptableUsernamesForAppId()
	{
		return
			[
				'email'   =>
					[
						'type'      	=> 'email',
						'name'          => 'Email'
					],
				'username'    =>
					[
						'type'      	=> 'username',
						'name'          => 'Username',
					]
			];
	}
}