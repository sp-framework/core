<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use System\Base\Exceptions\ControllerNotFoundException;
use System\Base\Exceptions\IdNotFoundException;
use System\Base\Providers\ErrorServiceProvider\Exceptions\IncorrectCSRF;
use System\Base\Providers\ErrorServiceProvider\Exceptions\IncorrectRequestType;

abstract class BaseComponent extends Controller
{
	protected $getQueryArr = [];

	protected $componentName;

	protected $componentRoute;

	protected $domain;

	protected $app;

	protected $component;

	protected $views;

	protected $viewName;

	protected $tokenKey = null;

	protected $token = null;

	protected $apiResponse = [];

	protected $showModuleSettings = false;

	protected $showModuleSettingsData = [];

	protected $usedModules = [];

	public $widgets;

	protected function onConstruct()
	{
		$this->domain = $this->domains->getDomain();

		$this->app = $this->apps->getAppInfo();
		if (!$this->app) {
			return;
		}

		if (!$this->api->isApi()) {
			$this->addResponse('Ok');//Default Response

			$this->views = $this->modules->views->getViewInfo();

			$this->setComponent();

			if (!$this->isJson() || $this->request->isAjax()) {
				$this->checkLayout();

				if (!$this->isJson() && $this->request->isGet()) {
					$this->setDefaultViewData();
				}

				if ($this->modules->views->getPhalconViewPath() === $this->view->getViewsDir()) {
					$this->view->setViewsDir($this->view->getViewsDir() . $this->getURI());

					$this->viewSimple->setViewsDir($this->view->getViewsDir() . $this->getURI());
				}
			}
		} else if ($this->api->isApi()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();
			}

			$this->setComponent();

			if (!$this->component && $this->app) {
				throw new ControllerNotFoundException('Component Not Found!');
			}
		}
	}

	protected function setComponent($checkWidgets = true)
	{
		$this->reflection = new \ReflectionClass($this);

		$this->componentName =
			str_replace('Component', '', $this->reflection->getShortName());

		$this->component =
			$this->modules->components->getComponentByClassForAppId(
				$this->reflection->getName(), $this->app['id']
			);

		//Murl
		if ($this->apps->isMurl) {
			$url = explode('/', explode('/q/', trim($this->apps->isMurl['url'], '/'))[0]);
		} else {
			$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);
		}

		if ($this->api->isApi()) {
			if ($url[0] === 'api') {
				unset($url[0]);
			}

			$url = array_values($url);

			if ($this->api->isApiCheckVia === 'pub') {
				if (isset($url[0]) &&
					$url[0] === 'pub'
				) {
					unset($url[0]);
				}
			}
			$url = array_values($url);
		}

		if ($this->request->isPost()) {
			unset($url[$this->helper->lastKey($url)]);
		}
		$url = array_values($url);

		if (isset($url[0]) && $url[0] === $this->app['route']) {
			unset($url[0]);
		}
		$url = array_values($url);

		$this->componentRoute = implode('/', $url);

		$componentByRoute =
			$this->modules->components->getComponentByRouteForAppId(
				strtolower($this->componentRoute), $this->app['id']
			);

		if (!$this->component) {
			$this->component = $componentByRoute;
		} else {
			if ($this->component['route'] !== $componentByRoute['route']) {//Incorrect component captured due to same shortname grabbed via reflection
				$this->component = $componentByRoute;
			}
		}

		if ($checkWidgets) {
			$this->checkComponentWidgets();
		}
	}

	protected function checkComponentWidgets()
	{
		$namespace = $this->reflection->getNamespaceName();

		$widgetsClass = '\\' . $namespace . '\\Widgets';

		try {
			if (class_exists($widgetsClass)) {
				$route = str_replace('apps/' . $this->app['app_type'] . '/components/', '', strtolower(str_replace('\\', '/', $namespace)));

				$component = $this->modules->components->getComponentByRouteForAppId($route, $this->app['id']);

				$this->widgets = (new $widgetsClass())->init($this, $component);
			}
		} catch (\Exception $e) {
			throw $e;
		}
	}

	protected function requestIsPost($checkCSRF = true)
	{
		if (!$this->request->isPost()) {
			throw new IncorrectRequestType('post');
		}

		if (!$this->checkCSRF()) {
			return false;
		}
	}

	public function beforeExecuteRoute()
	{
		$this->checkSettingsRoute();

		if (!$this->component && $this->app) {
			$this->setErrorDispatcher('controllerNotFound');

			return false;
		} else if (!$this->component) {
			throw new ControllerNotFoundException('Component Not Found!');
		}

		if (!$this->isJson()) {
			$this->setViewPermissions(false, false, false, false, false, false);

			$middlewares =
				msort(
					$this->modules->middlewares->getMiddlewaresForAppType(
						$this->app['app_type'],
						$this->app['id']
					), 'sequence');

			$this->view->appAuth = false;

			foreach ($middlewares as $key => $middleware) {
				if ($middleware['name'] === 'Auth' && $middleware['enabled'] === true) {
					$this->view->appAuth = true;
				}

				if ($middleware['name'] === 'Acl' && $middleware['enabled'] === false) {
					$this->setViewPermissions();

					return;
				}
			}

			$permissions = $this->checkPermissions();

			if (is_array($permissions)) {
				if (isset($permissions['view']) && $permissions['view'] == 1) {
					$this->view->canView = true;
				}
				if (isset($permissions['add']) && $permissions['add'] == 1) {
					$this->view->canAdd = true;
				}
				if (isset($permissions['update']) && $permissions['update'] == 1) {
					$this->view->canUpdate = true;
				}
				if (isset($permissions['remove']) && $permissions['remove'] == 1) {
					$this->view->canRemove = true;
				}
				if (isset($permissions['msview']) && $permissions['msview'] == 1) {
					$this->view->canMsv = true;
				}
				if (isset($permissions['msupdate']) && $permissions['msupdate'] == 1) {
					$this->view->canMsu = true;
				}
			} else if ($permissions === 'sysAdmin') {
				$this->setViewPermissions();
			}
		}
	}

	protected function setViewPermissions(
		$canView = true,
		$canAdd = true,
		$canUpdate = true,
		$canRemove = true,
		$canMsv = true,
		$canMsu = true
	) {
		$this->view->canView = $canView;
		$this->view->canAdd = $canAdd;
		$this->view->canUpdate = $canUpdate;
		$this->view->canRemove = $canRemove;
		$this->view->canMsv = $canMsv;
		$this->view->canMsu = $canMsu;
	}

	protected function checkSettingsRoute()
	{
		if ($this->dispatcher->wasForwarded()) {
			return;
		}

		if (isset($this->getData()['settings']) &&
			$this->getData()['settings'] == 'true'
		) {
			$this->dispatcher->forward(['action' => 'msview']);
		}
	}

	/**
	 * @acl(name=msview)
	 */
	public function msviewAction()
	{
		if (isset($this->getData()['settings']) && $this->getData()['settings'] == 'true') {
			$this->view->pick($this->helper->last(explode('/', $this->component['route'])) . '/msview');
		}
	}

	/**
	 * @acl(name=msupdate)
	 */
	public function msupdateAction()
	{
		$this->requestIsPost();

		if (isset($this->postData()['id']) &&
			$this->postData()['module_type'] === 'components'
		) {
			$this->modules->components->msupdate($this->postData());

			$this->addResponse(
				$this->modules->components->packagesData->responseMessage,
				$this->modules->components->packagesData->responseCode
			);
		} else if (isset($this->postData()['id']) &&
				   $this->postData()['module_type'] === 'packages'
		) {
			$this->modules->packages->msupdate($this->postData());

			$this->addResponse(
				$this->modules->packages->packagesData->responseMessage,
				$this->modules->packages->packagesData->responseCode
			);
		}
	}

	public function checkPwStrengthAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if ($this->basepackages->utils->checkPwStrength($this->postData()['pass']) !== false) {
				$this->view->responseData = $this->basepackages->utils->packagesData->responseData;
			}

			$this->addResponse(
				$this->basepackages->utils->packagesData->responseMessage,
				$this->basepackages->utils->packagesData->responseCode
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	public function generatePwAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			$this->basepackages->utils->generateNewPassword($this->postData());

			$this->addResponse(
				$this->basepackages->utils->packagesData->responseMessage,
				$this->basepackages->utils->packagesData->responseCode,
				$this->basepackages->utils->packagesData->responseData
			);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}

	protected function checkPermissions()
	{
		if ($this->auth->account()) {
			if ($this->auth->account()['security']['override_role'] == '1') {
				if (is_string($this->auth->account()['security']['permissions']) &&
					$this->auth->account()['security']['permissions'] !== ''
				) {
					$permissions = $this->helper->decode($this->auth->account()['security']['permissions'], true);
				} else {
					$permissions = $this->auth->account()['security']['permissions'];
				}
			}

			if (!isset($permissions) ||
				isset($permissions) && count($permissions) === 0
			) {
				if ($this->auth->account()['role']['id'] == '1') {
					return 'sysAdmin';
				}

				if (is_string($this->auth->account()['role']['permissions']) &&
					$this->auth->account()['role']['permissions'] !== ''
				) {
					$permissions = $this->helper->decode($this->auth->account()['role']['permissions'], true);
				} else {
					$permissions = $this->auth->account()['role']['permissions'];
				}
			}

			if (is_array($permissions) && count($permissions) > 0) {
				if (isset($permissions[$this->app['id']][$this->component['id']])) {
					return $permissions[$this->app['id']][$this->component['id']];
				}
			}
		}

		return false;
	}

	protected function checkCSRF()
	{
		if ($this->request->isPost() || $this->request->isPut() || $this->request->isDelete()) {
			if (!$this->security->checkToken(null, null, false)) {
				$this->view->responseCode = 2;

				$this->view->responseMessage = 'CSRF Token Error! Please refresh page.';

				return $this->sendJson();
			}
		}

		return true;
	}

	protected function setDefaultViewData()
	{
		$this->view->breadcrumb = '';

		$this->view->widget = $this->widget;

		$this->view->appName = $this->app['name'];

		$this->view->appRoute = $this->app['route'];

		if (isset($this->app['route']) && $this->app['route'] !== '') {
			$this->view->route = strtolower($this->app['route']);
		} else {
			$this->view->route = strtolower($this->app['name']);
		}

		$this->view->app = $this->app;

		$this->view->component = $this->component;

		$this->view->componentName = strtolower($this->componentName);

		$this->view->componentId =
			strtolower($this->view->appRoute) . '-' . strtolower($this->componentName);

		$reflection = $this->helper->sliceRight(explode('\\', $this->reflection->getName()), 3);

		if (count($reflection) === 1) {
			$parents = str_replace('Component', '', $this->helper->last($reflection));
			$this->view->parents = $parents;
			$this->view->parent = strtolower($parents);
		} else {
			$reflection[$this->helper->lastKey($reflection)] =
				str_replace('Component', '', $this->helper->last($reflection));

			$parents = $reflection;

			$this->view->parents = $parents;
			$this->view->parent = strtolower($this->helper->last($parents));
		}

		$this->view->viewName = $this->views['name'];

		$this->view->activeLayout = $this->modules->views->getActiveLayout();

		if ($this->app && isset($this->componentRoute)) {
			if ($this->componentRoute === '') {
				$this->view->breadcrumb = 'home';
			} else {
				$this->view->breadcrumb = $this->componentRoute;
			}
		}

		$this->view->version = $this->core->getVersion();
	}

	protected function sendJson()
	{
		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setHeader('Cache-Control', 'no-store');

		if ($this->response->isSent() !== true) {
			if ($this->api->isApi()) {
				$this->response->setJsonContent($this->apiResponse);
			} else {
				$this->view->disable();

				$this->response->setJsonContent($this->view->getParamsToView());
			}

			return $this->response->send();
		}
	}

	protected function afterExecuteRoute()
	{
		$this->buildHeaderBreadcrumb();

		if ((isset($this->getData()['id']) ||
			(isset($this->getData()['csrf']) && $this->getData()['csrf'] == true)) ||
			((isset($this->postData()['csrf']) && $this->postData()['csrf'] == true))
		) {
			$this->getNewToken();
		}

		if ($this->tokenKey && $this->token) {
			$this->response->setHeader('tokenKey', $this->tokenKey);
			$this->response->setHeader('token', $this->token);
		}

		if ($this->request->isPost() && $this->isJson()) {
			return $this->sendJson();
		}

		if ($this->app && $this->view->componentName !== 'auth') {
			if (!$this->app['menu_structure']) {
				$this->view->menus =
					$this->basepackages->menus->buildMenusForApp($this->app['id']);
			} else {
				if (is_string($this->app['menu_structure'])) {
					$this->view->menus =
						$this->helper->decode($this->app['menu_structure'], true);
				} else {
					$this->view->menus = $this->app['menu_structure'];
				}
			}
		}

		//Murl - update Hits
		if ($this->apps->isMurl) {
			if (!isset($this->apps->isMurl['vMurl'])) {
				if ($this->apps->isMurl['hits'] === null) {
					$this->apps->isMurl['hits'] = 1;
				} else {
					$this->apps->isMurl['hits'] = (int) ($this->apps->isMurl['hits'] + 1);
				}

				$this->basepackages->murls->updateMurl($this->apps->isMurl);
			}
		}

		if ($this->showModuleSettings) {
			$usedModules = [];
			$usedModules['components'] = [];
			$usedModules['packages'] = [];
			$usedModules['components']['value'] = 'components';
			$usedModules['packages']['value'] = 'packages';
			//Components
			$thisComponent['id'] = $this->component['id'];
			$thisComponent['name'] = $this->component['name'];
			$thisComponent['settings'] = $this->component['settings'];
			if (is_string($thisComponent['settings'])) {
				$thisComponent['settings'] = $this->helper->decode($thisComponent['settings'], true);
			}

			if (isset($thisComponent['settings']['mandatory'])) {//Remove all crucial only dev settings
				unset($thisComponent['settings']['mandatory']);
			}
			if (isset($thisComponent['settings']['needAuth'])) {//Remove all crucial only dev settings
				unset($thisComponent['settings']['needAuth']);
			}

			if (count($thisComponent['settings']) > 0) {
				$usedModules['components']['childs'][$thisComponent['id']] = $thisComponent;
			}

			if (isset($this->usedModules['components']) &&
				is_array($this->usedModules['components']) &&
				count($this->usedModules['components']) > 0
			) {
				foreach ($this->usedModules['components'] as $usedModulesComponent) {
					$componentInfo = $this->modules->components->getComponentByClassForAppId($usedModulesComponent);
					if (!$componentInfo) {
						continue;
					}
					$thisComponent['id'] = $componentInfo['id'];
					$thisComponent['name'] = $componentInfo['name'];
					$thisComponent['settings'] = $componentInfo['settings'];
					if (is_string($thisComponent['settings'])) {
						$thisComponent['settings'] = $this->helper->decode($thisComponent['settings'], true);
					}
					if (isset($thisComponent['settings']['mandatory'])) {//Remove all crucial only dev settings
						unset($thisComponent['settings']['mandatory']);
					}
					if (isset($thisComponent['settings']['needAuth'])) {//Remove all crucial only dev settings
						unset($thisComponent['settings']['needAuth']);
					}
					if (count($thisComponent['settings']) > 0) {
						$usedModules['components']['childs'][$thisComponent['id']] = $thisComponent;
					}
				}
			}
			//packages
			if (isset($this->usedModules['packages']) &&
				is_array($this->usedModules['packages']) &&
				count($this->usedModules['packages']) > 0
			) {
				foreach ($this->usedModules['packages'] as $usedModulesPackage) {
					if (str_contains($usedModulesPackage, '\\')) {
						$packageInfo = $this->modules->packages->getPackageByClassForAppId($usedModulesPackage);
					} else {
						$packageInfo = $this->modules->packages->getPackageByNameForAppId($usedModulesPackage);
					}

					if (!$packageInfo) {
						continue;
					}

					$thisPackage['id'] = $packageInfo['id'];
					$thisPackage['display_name'] = $packageInfo['display_name'];
					$thisPackage['name'] = $packageInfo['name'];
					$thisPackage['settings'] = $packageInfo['settings'];
					if (is_string($thisPackage['settings'])) {
						$thisPackage['settings'] = $this->helper->decode($thisPackage['settings'], true);
					}
					if (isset($thisPackage['settings']['componentRoute'])) {//Remove all crucial only dev settings
						unset($thisPackage['settings']['componentRoute']);
					}
					if (count($thisPackage['settings']) > 0) {
						$usedModules['packages']['childs'][$thisPackage['id']] = $thisPackage;
					}
				}
			}

			$this->view->usedModules = $usedModules;

			if (count($this->showModuleSettingsData) > 0) {
				foreach ($this->showModuleSettingsData as $dataKey => $dataValue) {
					$this->view->{$dataKey} = $dataValue;
				}
			}
		}
	}

	protected function buildHeaderBreadcrumb()
	{
		if ($this->component['route'] === 'errors') {
			$this->componentRoute = 'Errors';
		}

		if ($this->app && isset($this->componentRoute)) {
			if ($this->componentRoute === '') {
				$this->componentRoute = 'home';
			}

			$this->response->setHeader(
				'breadcrumb',
				$this->componentRoute
			);

			if (isset($this->getData()['id'])) {
				$this->response->setHeader(
					'currentId',
					$this->getData()['id']
				);
			}
		}
	}

	protected function getNewToken()
	{
		$this->tokenKey = $this->security->getTokenKey();

		$this->token = $this->security->getToken();

		// $this->logger->log->debug('----------' . $this->componentName . '----------' . $this->tokenKey . ':' . $this->token . '----------');
	}

	protected function getURI()
	{
		if ($this->apps->isMurl) {
			$url = explode('/', explode('/q/', trim($this->apps->isMurl['url'], '/'))[0]);
			if ($url[0] !== $this->app['route']) {
				array_unshift($url, $this->app['route']);
			}
		} else {
			$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);
		}

		$firstKey = $this->helper->firstKey($url);
		$lastKey = $this->helper->lastKey($url);

		if (isset($this->domain['exclusive_to_default_app']) &&
			$this->domain['exclusive_to_default_app'] == 1
		) {
			unset($url[$lastKey]);
		} else {
			unset($url[$firstKey]);
			unset($url[$lastKey]);
		}

		return implode('/', $url);
	}

	protected function isJson()
	{
		if ($this->request->getBestAccept() === 'application/json') {
			return true;
		}
		return false;
	}

	protected function checkLayout()
	{
		if ($this->request->isAjax()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if ($this->helper->has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '1') {
						$this->modules->views->buildAssets($this->componentName);
						return;
					} else {
						$this->disableViewLevel();
						return;
					}
				} else {
					$this->disableViewLevel();
					return;
				}
			} else {
				$this->modules->views->buildAssets($this->componentName);
				$this->disableViewLevel();
				return;
			}
		} else if ($this->request->isGet()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if ($this->helper->has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '0') {
						$this->disableViewLevel();
						return;
					} else {
						$this->modules->views->buildAssets($this->componentName);
						return;
					}
				} else {
					$this->modules->views->buildAssets($this->componentName);
					return;
				}
			} else {
				$this->modules->views->buildAssets($this->componentName);
				return;
			}
		} else if ($this->request->isPost()) {
			if ($this->helper->has($this->request->getPost(), 'layout')) {
				if ($this->request->getPost('layout') === '0') {
					$this->disableViewLevel();
					return;
				} else {
					$this->modules->views->buildAssets($this->componentName);
					return;
				}
			} else {
				$this->modules->views->buildAssets($this->componentName);
				return;
			}
		} else {
			$this->disableViewLevel();
			return;
		}
	}

	protected function disableViewLevel($level = 0)
	{
		if ($level === 1) {
			$disableLevel =
				[
					View::LEVEL_ACTION_VIEW	=> true
				];
		} else if ($level === 3) {
			$disableLevel =
				[
					View::LEVEL_LAYOUT => true
				];
		} else if ($level === 5) {
			$disableLevel =
				[
					View::LEVEL_MAIN_LAYOUT => true
				];
		} else {
			$disableLevel =
				[
					View::LEVEL_LAYOUT 		=> true,
					View::LEVEL_MAIN_LAYOUT => true
				];
		}

		$this->view->disableLevel($disableLevel);
	}

	protected function buildGetQueryParamsArr()
	{
		if ($this->request->isGet()) {
			//Murl
			if ($this->apps->isMurl) {
				$arr = $this->helper->chunk(
					explode('/', explode('/q/', trim($this->apps->isMurl['url'], '/'))[1]),
					2
				);
			} else {
				$arr = $this->helper->chunk($this->dispatcher->getParams(), 2);
			}

			foreach ($arr as $value) {
				if (isset($value[1])) {
					$this->getQueryArr[$value[0]] = $value[1];
				} else {
					$this->getQueryArr[$value[0]] = 0; //Value not set, so default to 0
				}
			}

			// getQuery - /admin/setup/q/id/2/filter/4/search//layout/0
			// Will Result to
			// array (size=4)
			//   'id' => string '2' (length=1)
			//   'filter' => string '4' (length=1)
			//   'search' => string '' (length=0)
			//   'layout' => string '0' (length=1)
		}
	}

	protected function getData()
	{
		return $this->getQueryArr;
	}

	protected function postData()
	{
		return $this->request->getPost();
	}

	protected function putData()
	{
		return $this->request->getPut();
	}

	protected function usePackage($packageClass)
	{
		$packageType = null;

		if (strpos($packageClass, '\\') === false) {
			try {
				$package = $this->basepackages->$packageClass;
			} catch (\Exception $e) {
				if (str_contains($e->getMessage(), 'Undefined property')) {
					$package = $this->modules->$packageClass;
				}
			}

			$reflection = new \ReflectionClass($package);

			$packageClass = $reflection->getName();

			$usedModulesPackageClass = $packageClass;

			if (str_contains($packageClass, 'BasepackagesServiceProvider')) {
				$packageClass = str_replace('System\Base\Providers\BasepackagesServiceProvider\Packages\\', '', $packageClass);
				$packageType = 'basepackages';
			} else if (str_contains($packageClass, 'ModulesServiceProvider')) {
				$packageClass = str_replace('System\Base\Providers\ModulesServiceProvider\\', '', $packageClass);
				$packageType = 'modules';
			}

			$packageClass = explode('\\', $packageClass);

			if (count($packageClass) === 1) {
				$packageName = $packageClass[0];
			} else {
				$packageName = implode('', $packageClass);
			}
		} else {
			if ($this->checkPackage($packageClass)) {
				$package = (new $packageClass())->init();
				$packageName = $this->helper->last(explode('\\', $packageClass));
			} else {
				throw new \Exception(
					'Package class : ' . $packageClass .
					' not available for app ' . $this->app['name']
				);
			}
		}

		if (!isset($this->usedModules['packages'])) {
			$this->usedModules['packages'] = [];
		}

		if ($packageType && ($packageType === 'basepackages' || $packageType === 'modules')) {
			array_push($this->usedModules['packages'], $usedModulesPackageClass);
		} else {
			array_push($this->usedModules['packages'], $packageName);
		}

		return $package;
	}

	protected function initStorages()
	{
		$storages = $this->basepackages->storages->getAppStorages();

		$this->view->storages = false;

		if ($storages) {
			$this->view->storages = $storages;

			return $storages;
		}

		return false;
	}

	protected function useStorage($storageType = null, array $overrideSettings = null)
	{
		$storages = $this->initStorages();

		if (!$storages) {
			$this->view->storage = false;

			return false;
		}

		if ($storageType && isset($storages[$storageType])) {//Assign type of storage for uploads
			$storage = &$storages[$storageType];

			if ($overrideSettings) {//add settings condition as needed
				if (isset($storage['allowed_file_mime_types']) &&
					isset($overrideSettings['allowed_file_mime_types'])
				) {
					$storage['allowed_file_mime_types'] = $overrideSettings['allowed_file_mime_types'];
				}
			}
		}

		$this->view->storages = $storages;

		if (isset($storage)) {
			$this->view->storage = $storage;

			return $storage;
		}
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getPackageByNameForAppId(
				$this->helper->last(explode('\\', $packageClass)),
				$this->app['id']
			);
	}

	protected function useComponent($componentClass)
	{
		if ($this->checkComponent($componentClass)) {
			$component = new $componentClass();

			$componentName = $this->helper->last(explode('\\', $componentClass));
		} else {
			throw new \Exception(
				'Component class : ' . $componentClass .
				' not available for app ' . $this->app['name']
			);
		}

		if (!isset($this->usedModules['components'])) {
			$this->usedModules['components'] = [];
		}

		array_push($this->usedModules['components'], $componentName);

		return $component;
	}

	protected function checkComponent($componentClass)
	{
		return
			$this->modules->components->getComponentByClassForAppId(
				$componentClass,
				$this->app['id']
			);
	}

	protected function useComponentWithView($componentClass, $action = 'view', $args = null)
	{
		//To Use from Component - $this->useComponentWithView(HomeComponent::class);
		//This will generate 2 view variables 1) {{home}} & {{homeTemplate}}
		//This can be used for dashboard component
		//From dashboard you can make a call to a component widgetAction and grab the template content from it.
		//The template data can be then passed to view for widget rendering.
		//Need further investigation.
		$this->app = $this->apps->getAppInfo();

		$component = $this->checkComponent($componentClass);

		if ($component) {
			$componentName = strtolower($component['name']);
			$componentAction = $action . 'Action';
			$componentViewName = strtolower($component['name']) . 'Template';

			// var_dump($componentName,$componentAction,$componentViewName);

			$this->view->{$componentName} =
				$this->useComponent($componentClass)->{$componentAction}($args);

			$this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

			$this->view->{$componentViewName} =
				$this->view->render($componentName, $action)->getContent();

			$this->view->setRenderLevel(View::LEVEL_MAIN_LAYOUT);
		}
	}

	protected function addResponse($responseMessage, int $responseCode = 0, $responseData = null)
	{
		if ($this->api->isApi()) {
			$this->apiResponse['responseMessage'] = $responseMessage;
			$this->apiResponse['responseCode'] = $responseCode;
			if ($responseData !== null) {
				$this->apiResponse['responseData'] = $responseData;
			}

			return $this->sendJson();
		}

		$this->view->responseMessage = $responseMessage;

		$this->view->responseCode = $responseCode;

		if ($responseData !== null) {
			$this->view->responseData = $responseData;
		}
	}

	protected function throwIdNotFound()
	{
		if ($this->app) {
			$this->setErrorDispatcher('idNotFound');

			return false;
		}

		throw new IdNotFoundException('ID Not Found!');
	}

	protected function setErrorDispatcher($action)
	{
		if ($this->app) {
			$component = $this->modules->components->getComponentById($this->app['errors_component']);

			if (isset($this->app['errors_component']) &&
				$this->app['errors_component'] != 0
			) {
				$errorClassArr = explode('\\', $component['class']);
				unset($errorClassArr[$this->helper->lastKey($errorClassArr)]);
				$errorComponent = ucfirst($component['route']);
				$namespace = implode('\\', $errorClassArr);
				$this->view->setViewsDir($this->modules->views->getPhalconViewPath());
			} else {
				$errorComponent = 'Errors';
				$namespace = 'System\Base\Providers\ErrorServiceProvider';
			}

			$this->dispatcher->forward(
				[
					'controller' => $errorComponent,
					'action'     => $action,
					'namespace'  => $namespace
				]
			);
		}
	}

	protected function formatNumbers($number, $saperator = '-')
	{
		$number = $this->extractNumbers($number);

		if (strlen($number) === 10) {
			$split = str_split($number, 4);
			$digits = $split[0];
			unset($split[0]);
			$split = implode('', $split);
			$split = str_split($split, 3);
			$split = implode($saperator, $split);
			$digits .= $saperator . $split;

			$number = $digits;
		} else if (strlen($number) === 11) {//Incl Country Code (AU)
			$split = str_split($number, 2);
			$digits = $split[0];
			unset($split[0]);
			$split = implode('', $split);
			$split = str_split($split, 1);
			$digits .= $saperator . $split[0];
			unset($split[0]);
			$split = implode('', $split);
			// $split = str_split($split, 4);
			// $split = implode($saperator, $split);
			$digits .= $saperator . $split;

			$number = $digits;
		}

		return $number;
	}

	protected function extractNumbers($string)
	{
		return preg_replace('/[^0-9]/', '', $string);
	}

	public function setModuleSettings(bool $setting)
	{
		$this->showModuleSettings = $setting;
	}

	public function setModuleSettingsData(array $data = [])
	{
		if (isset($this->getData()['settings']) &&
			$this->getData()['settings'] == 'true'
		) {
			$this->showModuleSettingsData = $data;
		}
	}

	protected function addToNotification($subscriptionType, $messageTitle, $messageDetails = null, $last = null)
	{
		if ($this->component['notification_subscriptions']) {
			if (!is_array($this->component['notification_subscriptions'])) {
				$this->component['notification_subscriptions'] = $this->helper->decode($this->component['notification_subscriptions'], true);
			}

			if (count($this->component['notification_subscriptions']) === 0) {
				return;
			}

			foreach ($this->component['notification_subscriptions'] as $appId => $subscriptions) {
				if ($subscriptionType === 'add' || $subscriptionType === 'update' || $subscriptionType === 'remove') {
					$notificationType = 0;
				} else if ($subscriptionType === 'warning') {
					$notificationType = 1;
				} else if ($subscriptionType === 'error') {
					$notificationType = 2;
				}

				if (isset($subscriptions[$subscriptionType]) &&
					is_array($subscriptions[$subscriptionType]) &&
					count($subscriptions[$subscriptionType]) > 0
				) {
					foreach ($subscriptions[$subscriptionType] as $key => $aId) {
						$this->basepackages->notifications->addNotification(
							$messageTitle,
							$messageDetails,
							$appId,
							$aId,
							null,
							$this->component['name'],
							$last ? $last['id'] : null,
							$notificationType
						);
					}
				}

				if (isset($subscriptions['email']) && count($subscriptions['email']) > 0) {
					$domainId = '1';//Default Domain for system generated Notifications (like API)

					if ($this->domains && $this->domains->getDomain()) {
						$domainId = $this->domains->getDomain()['id'];
					}

					$this->basepackages->notifications->emailNotification(
						$subscriptions['email'],
						$messageTitle,
						$messageDetails,
						$domainId,
						$appId,
						null,
						$this->component['name'],
						$last ? $last['id'] : null,
						$notificationType
					);
				}
			}
		}
	}
}