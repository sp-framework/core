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
		}
	}

	protected function setComponent($checkWidgets = true)
	{
		$this->reflection = new \ReflectionClass($this);

		$this->componentName =
			str_replace('Component', '', $this->reflection->getShortName());

		$this->component =
			$this->modules->components->getComponentByNameForAppId(
				$this->componentName, $this->app['id']
			);

		//Murl
		if ($this->apps->isMurl) {
			$url = explode('/', explode('/q/', trim($this->apps->isMurl->url, '/'))[0]);
		} else {
			$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);
		}

		if ($this->request->isPost()) {
			unset($url[$this->helper->lastKey($url)]);
		}

		if (isset($url[0]) && $url[0] === $this->app['route']) {
			unset($url[0]);
		}

		$this->componentRoute = implode('/', $url);

		if (!$this->component) {
			$this->component =
				$this->modules->components->getComponentByRouteForAppId(
					strtolower($this->componentRoute), $this->app['id']
				);
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
		if (!$this->component && $this->app) {
			$this->setErrorDispatcher('controllerNotFound');

			return false;
		} else if (!$this->component) {
			throw new ControllerNotFoundException('Component Not Found!');
		}

		if (!$this->isJson()) {
			$this->view->canView = false;
			$this->view->canAdd = false;
			$this->view->canUpdate = false;
			$this->view->canRemove = false;
			$this->view->canMsv = false;
			$this->view->canMsu = false;

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
					$this->view->canView = true;
					$this->view->canAdd = true;
					$this->view->canUpdate = true;
					$this->view->canRemove = true;
					$this->view->canMsv = true;
					$this->view->canMsu = true;
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
				$this->view->canView = true;
				$this->view->canAdd = true;
				$this->view->canUpdate = true;
				$this->view->canRemove = true;
				$this->view->canMsv = true;
				$this->view->canMsu = true;
			}
		}
	}

	protected function checkSettingsRoute()
	{
		if (isset($this->getData()['settings']) && $this->getData()['settings'] == 'true') {
			$this->dispatcher->forward(['action' => 'msview']);
		}
	}

	/**
	 * @acl(name=msview)
	 */
	public function msviewAction()
	{
		if ($this->view->usedModules) {
			if (isset($this->getData()['settings']) && $this->getData()['settings'] == 'true') {

				$this->view->pick($this->helper->last(explode('/', $this->component['route'])) . '/msview');
			}
		}
	}

	/**
	 * @acl(name=msupdate)
	 */
	public function msupdateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}

			if (isset($this->postData()['module_id']) &&
				$this->postData()['module_type'] === 'components'
			) {
				$this->modules->components->msupdate($this->postData());

				$this->addResponse(
					$this->modules->components->packagesData->responseMessage,
					$this->modules->components->packagesData->responseCode
				);
			} else if (isset($this->postData()['module_id']) &&
					   $this->postData()['module_type'] === 'packages'
			) {
				$this->modules->packages->msupdate($this->postData());

				$this->addResponse(
					$this->modules->packages->packagesData->responseMessage,
					$this->modules->packages->packagesData->responseCode
				);
			}
		} else {
			$this->addResponse('Method Not Allowed', 1);
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
			$url = explode('/', explode('/q/', trim($this->apps->isMurl->url, '/'))[0]);
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
			$arr = $this->helper->chunk($this->dispatcher->getParams(), 2);

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

	protected function usePackage($packageClass, $getSettings = false)
	{
		if (strpos($packageClass, '\\') === false) {
			$package = $this->basepackages->$packageClass;
		} else {
			if ($this->checkPackage($packageClass)) {
				$package = (new $packageClass())->init();
				$packageClass = $this->helper->last(explode('\\', $packageClass));
			} else {
				throw new \Exception(
					'Package class : ' . $packageClass .
					' not available for app ' . $this->app['name']
				);
			}
		}

		if ($getSettings === 'components' || $getSettings === true) {
			$thisComponent['id'] = $this->component['id'];
			$thisComponent['name'] = $this->component['name'];
			$thisComponent['settings'] = $this->helper->decode($this->component['settings'], true);

			if (!isset($usedModules['components'])) {
				$usedModules['components'] = [];
				$usedModules['components']['value'] = 'components';
				$usedModules['components']['childs'][$thisComponent['id']] = $thisComponent;
			} else {
				$usedModules['components']['childs'][$thisComponent['id']] = $thisComponent;
			}
		}

		if ($getSettings === 'packages' || $getSettings === true) {
			$packageInfo = $this->modules->packages->getPackageByName($packageClass);
			$thisPackage['id'] = $packageInfo['id'];
			$thisPackage['name'] = $packageInfo['name'];
			if (is_string($packageInfo['settings'])) {
				$thisPackage['settings'] = $this->helper->decode($packageInfo['settings'], true);
			}

			if (!isset($usedModules['packages'])) {
				$usedModules['packages'] = [];
				$usedModules['packages']['value'] = 'packages';
				$usedModules['packages']['childs'][$thisPackage['id']] = $thisPackage;
			} else {
				$usedModules['packages']['childs'][$thisPackage['id']] = $thisPackage;
			}
		}

		if ($getSettings) {
			$this->view->usedModules = $usedModules;
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
		$this->app = $this->apps->getAppInfo();

		if ($this->checkComponent($componentClass)) {
			return new $componentClass();
		} else {
			throw new \Exception(
				'Component class : ' . $componentClass .
				' not available for app ' . $this->app['name']
			);
		}
	}

	protected function checkComponent($componentClass)
	{
		return
			$this->modules->components->getComponentByNameForAppId(
				str_replace('Component', '', $this->helper->last(explode('\\', $componentClass))),
				$this->app['id']
			);
	}

	protected function useComponentWithView($componentClass, $action = 'view')
	{
		//To Use from Component - $this->useComponentWithView(HomeComponent::class);
		//This will generate 2 view variables 1) {{home}} & {{homeTemplate}}
		$this->app = $this->apps->getAppInfo();

		$component = $this->checkComponent($componentClass);

		if ($component) {
			$componentName = strtolower($component['name']);
			$componentAction = $action . 'Action';
			$componentViewName = strtolower($component['name']) . 'Template';

			var_dump($componentName,$componentAction,$componentViewName);

			$this->view->{$componentName} =
				$this->useComponent($componentClass)->{$componentAction}();

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
}