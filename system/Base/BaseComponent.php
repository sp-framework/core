<?php

namespace System\Base;

use Apps\Ecom\Admin\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Assets\Collection;
use Phalcon\Assets\Inline;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Tag;

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

	protected $viewSettings;

	protected $assetsCollections = [];

	protected function onConstruct()
	{
		$this->domain = $this->domains->getDomain();

		$this->app = $this->apps->getAppInfo();
		if (!$this->app) {
			return;
		}

		$this->setDefaultViewResponse();

		$this->views = $this->modules->views->getViewInfo();

		$this->setComponent();

		if (!$this->isJson() || $this->request->isAjax()) {

			if ($this->views) {
				$this->viewSettings = json_decode($this->views['settings'], true);

				if (!$this->isJson() && $this->request->isGet()) {
					$this->setDefaultViewData();
				}

				$this->checkLayout();

				$this->view->setViewsDir($this->view->getViewsDir() . $this->getURI());
			}
		}
	}

	protected function setComponent()
	{
		$this->reflection = new \ReflectionClass($this);

		$this->componentName =
			str_replace('Component', '', $this->reflection->getShortName());

		$this->component =
			$this->modules->components->getNamedComponentForApp(
				$this->componentName, $this->app['id']
			);

		$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

		if ($this->request->isPost()) {
			unset($url[Arr::lastKey($url)]);
		}

		if ($url[0] === $this->app['route']) {
			unset($url[0]);
		}

		$this->componentRoute = implode('/', $url);

		if (!$this->component) {
			$this->component =
				$this->modules->components->getRouteComponentForApp(
					strtolower($this->componentRoute), $this->app['id']
				);
		}
	}

	public function beforeExecuteRoute(Dispatcher $dispatcher)
	{
		if (!$this->component && $this->app) {
			$component = $this->modules->components->getComponentById($this->app['errors_component']);

			if (isset($this->app['errors_component']) &&
				$this->app['errors_component'] != 0
			) {
				$errorClassArr = explode('\\', $component['class']);
				unset($errorClassArr[Arr::lastKey($errorClassArr)]);
				$errorComponent = ucfirst($component['route']);
				$namespace = implode('\\', $errorClassArr);
				$this->view->setViewsDir($this->modules->views->getPhalconViewPath());
			} else {
				$errorComponent = 'Errors';
				$namespace = 'System\Base\Providers\ErrorServiceProvider';
			}

			$dispatcher->forward(
				[
					'controller' => $errorComponent,
					'action'     => 'controllerNotFound',
					'namespace'  => $namespace
				]
			);

			return;
		}
	}

	protected function checkCSRF()
	{
		if ($this->request->isPost() || $this->request->isPut() || $this->request->isDelete()) {
			if (!$this->security->checkToken(null, null, false)) {
				$this->view->responseCode = 2;

				$this->view->responseMessage = 'CSRF Token Error! Please refresh page.';

				$this->sendJson();

				return false;
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

		$this->view->component = $this->component;

		$this->view->componentName = strtolower($this->componentName);

		$this->view->componentId =
			strtolower($this->view->appRoute) . '-' . strtolower($this->componentName);

		$reflection = Arr::sliceRight(explode('\\', $this->reflection->getName()), 3);

		if (count($reflection) === 1) {
			$parents = str_replace('Component', '', Arr::last($reflection));
			$this->view->parents = $parents;
			$this->view->parent = strtolower($parents);
		} else {
			$reflection[Arr::lastKey($reflection)] =
				str_replace('Component', '', Arr::last($reflection));

			$parents = $reflection;

			$this->view->parents = $parents;
			$this->view->parent = strtolower(Arr::last($parents));
		}

		$this->view->viewName = $this->views['name'];

		if ($this->app && isset($this->componentRoute)) {
			if ($this->componentRoute === '') {
				$this->view->breadcrumb = 'home';
			} else {
				$this->view->breadcrumb = $this->componentRoute;
			}
		}
	}

	protected function setDefaultViewResponse()
	{
		$this->view->responseCode = '0';

		$this->view->responseMessage = 'OK';
	}

	protected function sendJson()
	{
		$this->view->disable();

		$this->response->setContentType('application/json', 'UTF-8');
		$this->response->setHeader('Cache-Control', 'no-store');

		if ($this->response->isSent() !== true) {
			$this->response->setJsonContent($this->view->getParamsToView());

			return $this->response->send();
		}
	}

	protected function afterExecuteRoute()
	{
		$this->buildHeaderBreadcrumb();

		$this->getNewTokenAction();

		if (!$this->request->isPost() || !$this->isJson())
		if ($this->app) {
			$this->view->menus =
				$this->basepackages->menus->buildMenusForApp($this->app['id']);
		}

		if ($this->request->isAjax()) {
			$this->response->setHeader('tokenKey', $this->security->getTokenKey());
			$this->response->setHeader('token', $this->security->getToken());
		}
	}

	protected function buildHeaderBreadcrumb()
	{

		if ($this->app && isset($this->componentRoute)) {
			if ($this->componentRoute === '') {
				$this->componentRoute = 'home';
			}

			$this->response->setHeader(
				'breadcrumb',
				$this->componentRoute
			);
		}
	}

	public function getNewTokenAction()
	{
		if ($this->request->isPost() && $this->isJson()) {
			$this->view->tokenKey = $this->security->getTokenKey();

			$this->view->token = $this->security->getToken();

			return $this->sendJson();
		}
	}

	protected function getURI()
	{
		$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

		$firstKey = Arr::firstKey($url);
		$lastKey = Arr::lastKey($url);

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

				if (Arr::has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '1') {
						$this->buildAssets();
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
				$this->disableViewLevel();
				return;
			}
		} else if ($this->request->isGet()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if (Arr::has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '0') {
						$this->disableViewLevel();
						return;
					} else {
						$this->buildAssets();
						return;
					}
				} else {
					$this->buildAssets();
					return;
				}
			} else {
				$this->buildAssets();
				return;
			}
		} else if ($this->request->isPost()) {

			if (Arr::has($this->request->getPost(), 'layout')) {
				if ($this->request->getPost('layout') === '0') {
					$this->disableViewLevel();
					return;
				} else {
					$this->buildAssets();
					return;
				}
			} else {
				$this->buildAssets();
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
					View::LEVEL_ACTION_VIEW 		=> true,
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
		$arr = Arr::chunk($this->dispatcher->getParams(), 2);

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

	protected function buildAssets()
	{
		$this->buildAssetsTitle();
		$this->buildAssetsMeta();
		$this->buildAssetsHeadCss();
		$this->buildAssetsHeadStyle();
		$this->buildAssetsHeadJs();
		$this->buildAssetsBody();
		$this->buildAssetsBodyJs();
		$this->buildAssetsFooter();
		$this->buildAssetsFooterJs();
		$this->buildAssetsFooterJsInline();
	}

	protected function buildAssetsTitle()
	{
		$this->tag::setDocType(Tag::XHTML5);

		if (isset($this->viewSettings['head']['title'])) {
			Tag::setTitle($this->viewSettings['head']['title']);
		} else {
			Tag::setTitle(ucfirst($this->app['name']));
		}

		if (isset($this->componentName)) {
			Tag::appendTitle(' - ' . $this->componentName);
		}
	}

	protected function buildAssetsMeta()
	{
		$this->assetsCollections['meta'] = $this->assets->collection('meta');

		if (isset($this->viewSettings['head']['meta']['charset'])) {
			$charset = $this->viewSettings['head']['meta']['charset'];
		} else {
			$charset = 'UTF-8';
		}

		$this->assetsCollections['meta']->addInline(new Inline('charset', $charset));

		$this->assetsCollections['meta']->addInline(
			new Inline('description', $this->viewSettings['head']['meta']['description'])
		);
		$this->assetsCollections['meta']->addInline(
			new Inline('keywords', $this->viewSettings['head']['meta']['keywords'])
		);
		$this->assetsCollections['meta']->addInline(
			new Inline('author', $this->viewSettings['head']['meta']['author'])
		);
		$this->assetsCollections['meta']->addInline(
			new Inline('viewport', $this->viewSettings['head']['meta']['viewport'])
		);
	}

	protected function buildAssetsHeadCss()
	{
		$this->assetsCollections['headLinks'] = $this->assets->collection('headLinks');
		$links = $this->viewSettings['head']['link']['href'];
		if (count($links) > 0) {
			foreach ($links as $link) {
				$this->assetsCollections['headLinks']->addCss($link);
			}
		}
	}

	protected function buildAssetsHeadStyle()
	{
		$this->assetsCollections['headStyle'] = $this->assets->collection('headStyle');
		$inlineStyle = $this->viewSettings['head']['style'] ?? null;
		if ($inlineStyle) {
			$this->assets->addInlineCss($inlineStyle);
		}
	}

	protected function buildAssetsHeadJs()
	{
		$this->assetsCollections['headJs'] = $this->assets->collection('headJs');

		$scripts = $this->viewSettings['head']['script']['src'];

		if (count($scripts) > 0) {
			foreach ($scripts as $script) {
				$this->assetsCollections['headJs']->addJs($script);
			}
		}
	}

	protected function buildAssetsBody()
	{
		$this->assetsCollections['body'] = $this->assets->collection('body');
		$this->assetsCollections['body']->addInline(new Inline('bodyParams', $this->viewSettings['body']['params']));
	}

	protected function buildAssetsBodyJs()
	{
		$this->assetsCollections['body']->addInline(new Inline('bodyScript', $this->viewSettings['body']['jsscript']));
	}

	protected function buildAssetsFooter()
	{
		$this->assetsCollections['footer'] = $this->assets->collection('footer');
		$this->assetsCollections['footer']->addInline(new Inline('footerParams', $this->viewSettings['footer']['params']));
	}

	protected function buildAssetsFooterJs()
	{
		$this->assetsCollections['footerJs'] = $this->assets->collection('footerJs');
		$scripts = $this->viewSettings['footer']['script']['src'];
		if (count($scripts) > 0) {
			foreach ($scripts as $script) {
				$this->assetsCollections['footerJs']->addJs($script);
			}
		}
	}

	protected function buildAssetsFooterJsInline()
	{
		$inlineScript = $this->viewSettings['footer']['jsscript'] ?? null;
		if ($inlineScript && $inlineScript !== '') {
			$this->assets->addInlineJs($inlineScript);
		}
	}

	protected function usePackage($packageClass)
	{
		if ($this->checkPackage($packageClass)) {
			return (new $packageClass())->init();
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for app ' . $this->app['name']
			);
		}
	}

	protected function useStorage($storageType)
	{
		$storages = $this->basepackages->storages->getAppStorages();

		if ($storages && isset($storages[$storageType])) {//Assign type of storage for uploads
			$this->view->storages = $storages;
			$this->view->storage = $storages[$storageType];
		} else {
			$this->view->storages = [];
		}

		if (!isset($this->domains->domain['apps'][$this->app['id']][$storageType . 'Storage'])) {
			$this->view->storages = [];
		}
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getNamedPackageForApp(
				Arr::last(explode('\\', $packageClass)),
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
			$this->modules->components->getNamedComponentForApp(
				str_replace('Component', '', Arr::last(explode('\\', $componentClass))),
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

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$contents = $this->localContent->listContents($directory, $sub);

			foreach ($contents as $contentKey => $content) {
				if ($content['type'] === 'dir') {
					array_push($installedFiles['dir'], $content['path']);
				} else if ($content['type'] === 'file') {
					array_push($installedFiles['files'], $content['path']);
				}
			}

			return $installedFiles;
		} else {
			return null;
		}
	}

	protected function addResponse($responseMessage, $responseCode = 0, $responseData = null)
	{
		$this->view->responseMessage = $responseMessage;

		$this->view->responseCode = $responseCode;

		if ($responseData && is_array($responseData)) {
			$this->view->responseData = $responseData;
		}
	}
}