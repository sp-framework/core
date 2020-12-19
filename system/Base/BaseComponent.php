<?php

namespace System\Base;

use Applications\Ecom\Admin\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Assets\Collection;
use Phalcon\Assets\Inline;
use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use Phalcon\Tag;

abstract class BaseComponent extends Controller
{
	protected $getQueryArr = [];

	protected $componentName;

	protected $application;

	protected $component;

	protected $views;

	protected $viewName;

	protected $viewSettings;

	protected $assetsCollections = [];

	protected function onConstruct()
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if (!$this->application) {
			return;
		}

		$this->setDefaultViewResponse();

		$this->views = $this->modules->views->getViewInfo();

		$this->reflection = new \ReflectionClass($this);

		$this->componentName =
			str_replace('Component', '', $this->reflection->getShortName());

		$this->component =
			$this->modules->components->getNamedComponentForApplication(
				$this->componentName, $this->application['id']
			);

		if (!$this->component) {
			$this->componentRoute =
				str_replace('Component', '', $this->reflection->getShortName());

			$this->component =
				$this->modules->components->getRouteComponentForApplication(
					strtolower($this->componentRoute), $this->application['id']
				);
		} else {
			$this->componentRoute =
				str_replace('Component', '', $this->reflection->getShortName());
		}


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

	protected function checkCSRF()
	{
		if ($this->request->isPost() || $this->request->isPut() || $this->request->isDelete()) {
			if (!$this->security->checkToken()) {
				$this->view->responseCode = 2;

				$this->view->responseMessage = 'CSRF TOKEN ERROR';

				$this->sendJson();

				return false;
			}
		}
		return true;
	}

	protected function setDefaultViewData()
	{
		$this->view->widget = $this->widget;

		$this->view->applicationName = $this->application['name'];

		$this->view->applicationRoute = $this->application['route'];

		if (isset($this->application['route']) && $this->application['route'] !== '') {
			$this->view->route = strtolower($this->application['route']);
		} else {
			$this->view->route = strtolower($this->application['name']);
		}

		$this->view->component = $this->component;

		$this->view->componentName = strtolower($this->componentName);

		$this->view->componentId =
			strtolower($this->view->applicationRoute) . '-' . strtolower($this->componentRoute);

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
	}

	protected function setDefaultViewResponse()
	{
		$this->view->responseCode = '0';

		$this->view->responseMessage = 'Default Response Message';
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
		if ($this->isJson()) {
			$this->view->tokenKey = $this->security->getTokenKey();

			$this->view->token = $this->security->getToken();

			return $this->sendJson();
		}

		if (!$this->isJson() || $this->request->isAjax()) {
			if ($this->application) {
				$this->view->menus =
					$this->basepackages->menus->getMenusForApplication($this->application['id']);
			}

			$this->response->setHeader('tokenKey', $this->security->getTokenKey());
			$this->response->setHeader('token', $this->security->getToken());
		}
	}

	protected function getURI()
	{
		$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

		$firstKey = Arr::firstKey($url);
		$lastKey = Arr::lastKey($url);

		unset($url[$firstKey]);
		unset($url[$lastKey]);

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

	protected function disableViewLevel()
	{
		$this->view->disableLevel(
			[
				View::LEVEL_LAYOUT 		=> true,
				View::LEVEL_MAIN_LAYOUT => true
			]
		);
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

			if (isset($this->componentName)) {
				Tag::appendTitle(' - ' . $this->componentName);
			}
		} else {
			Tag::setTitle('Title Missing In Application Configuration');
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
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		return
			$this->modules->packages->getNamedPackageForApplication(
				Arr::last(explode('\\', $packageClass)),
				$this->application['id']
			);
	}

	protected function useComponent($componentClass)
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if ($this->checkComponent($componentClass)) {
			return new $componentClass();
		} else {
			throw new \Exception(
				'Component class : ' . $componentClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkComponent($componentClass)
	{
		return
			$this->modules->components->getNamedComponentForApplication(
				str_replace('Component', '', Arr::last(explode('\\', $componentClass))),
				$this->application['id']
			);
	}

	protected function useComponentWithView($componentClass, $action = 'view')
	{
		//To Use from Component - $this->useComponentWithView(HomeComponent::class);
		//This will generate 2 view variables 1) {{home}} & {{homeTemplate}}
		$this->application = $this->modules->applications->getApplicationInfo();

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
}