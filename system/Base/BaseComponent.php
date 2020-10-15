<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

abstract class BaseComponent extends Controller
{
	protected $getQueryArr = [];

	protected $views;

	protected function onConstruct()
	{
		$this->setDefaultViewResponse();

		if (!$this->isJson() || $this->request->isAjax()) {
			$this->checkLayout();
		}
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

			return $this->sendJson();
		} else {
			$this->view->setViewsDir($this->view->getViewsDir() . $this->getURI());
			// var_dump($this->view->getViewsDir());
		}
	}

	protected function getURI()
	{
		$url = explode('/', explode('/q/', trim($this->request->getURI(), '/'))[0]);

		$firstKey = Arr::firstKey($url);
		$lastKey = Arr::lastKey($url);

		unset($url[$firstKey]);
		unset($url[$lastKey]);

		return join($url, '/');
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
						$this->view->disableLevel(
										[
											View::LEVEL_LAYOUT 		=> true,
											View::LEVEL_MAIN_LAYOUT => true
										]
									);
						return;
					}
				} else {
					$this->view->disableLevel(
										[
											View::LEVEL_LAYOUT 		=> true,
											View::LEVEL_MAIN_LAYOUT => true
										]
									);
						return;
				}
			} else {
				$this->view->disableLevel(
								[
									View::LEVEL_LAYOUT 		=> true,
									View::LEVEL_MAIN_LAYOUT => true
								]
							);
				return;
			}
		}

		if ($this->request->isGet()) {
			if (count($this->dispatcher->getParams()) > 0) {
				$this->buildGetQueryParamsArr();

				if (Arr::has($this->getQueryArr, 'layout')) {
					if ($this->getQueryArr['layout'] === '0') {
						$this->view->disableLevel(
							[
								View::LEVEL_LAYOUT 		=> true,
								View::LEVEL_MAIN_LAYOUT => true
							]
						);
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
					$this->view->disableLevel(
						[
							View::LEVEL_LAYOUT 		=> true,
							View::LEVEL_MAIN_LAYOUT => true
						]
					);
					return;
				} else {
					$this->buildAssets();
					return;
				}
			} else {
				$this->buildAssets();
				return;
			}
		}
	}

	protected function buildGetQueryParamsArr()
	{
		$arr = Arr::chunk($this->dispatcher->getParams(), 2);

		foreach ($arr as $value) {
			$this->getQueryArr[$value[0]] = $value[1];
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

	protected function buildAssets()
	{
		$headLinks = $this->assets->collection('headLinks');
		$headStyle = $this->assets->collection('headStyle');
		$headJs = $this->assets->collection('headJs');
		$footerJs = $this->assets->collection('footerJs');

		if ($this->modules->views->getViewInfo()) {
			$settings = json_decode($this->modules->views->getViewInfo()['settings'], true);

			$links = $settings['head']['link']['href'];
			$scripts = $settings['head']['script']['src'];
			$inlineStyle = $settings['head']['style'] ?? null;
			if (count($links) > 0) {
				foreach ($links as $link) {
					$headLinks->addCss($link);
				}
			}

			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					$headJs->addJs($script);
				}
			}

			if ($inlineStyle) {
				$this->assets->addInlineCss($inlineStyle);
			}

			$scripts = $settings['footer']['script']['src'];
			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					$footerJs->addJs($script);
				}
			}

			$inlineScript = $settings['footer']['jsscript'] ?? null;

			if ($inlineScript && $inlineScript !== '') {
				$this->assets->addInlineJs($inlineScript);
			}
		}
	}

	protected function usePackage($packageClass)
	{
		$this->application = $this->modules->applications->getApplicationInfo();

		if ($this->checkPackage($packageClass)) {
			return new $packageClass($this->container);
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

	// protected function generateView()
	// {
	// 	$this->getDefaults();

	// 	// var_dump($this->getData);
	// 	// if ($this->getData === '') {
	// 	// 	if ($this->requestUri === '/' ||
	// 	// 		$this->requestUri === '/' .
	// 	// 			strtolower($this->applicationInfo['name']) . '/' ||
	// 	// 		$this->requestUri === '/' .
	// 	// 			strtolower($this->applicationInfo['name']) . '/' . $this->defaultComponentsName
	// 	// 		) {
	// 	// 		return $this->checkViewFile('default');
	// 	// 	} else {
	// 	// 		$path =
	// 	// 			str_replace(
	// 	// 				'/' . strtolower($this->applicationInfo['name']) . '/',
	// 	// 				'',
	// 	// 				$this->requestUri
	// 	// 			);
	// 	// 		return $this->checkViewFile($path);
	// 	// 	}
	// 	// } else {
	// 		$uri = explode('?', $this->requestUri);
	// 		// dump($uri, $this->applicationName);
	// 		if ($uri[0] === '/' ||
	// 			$uri[0] === '/' . strtolower($this->applicationName) ||
	// 			$uri[0] === '/' . strtolower($this->applicationName) . '/' ||
	// 			$uri[0] === '/' . strtolower($this->applicationInfo['route']) ||
	// 			$uri[0] === '/' . strtolower($this->applicationInfo['route']) . '/' ||
	// 			$uri[0] === '/' . strtolower($this->applicationInfo['route']) . '/' . $this->defaultComponentsName ||
	// 			$uri[0] === '/' . strtolower($this->applicationName) . '/' . $this->defaultComponentsName
	// 			) {

	// 			return $this->checkViewFile('default');
	// 		} else {
	// 			$explodeUri = explode('/', trim($uri[0], '/'));

	// 			if ($explodeUri[0] === strtolower($this->applicationInfo['route'])) {
	// 				$strToReplace =
	// 					'/' . strtolower($this->applicationInfo['route']) . '/';

	// 			} else if ($explodeUri[0] === strtolower($this->applicationName)) {
	// 				$strToReplace =
	// 					'/' . strtolower($this->applicationName) . '/';
	// 			}

	// 			$componentName =
	// 				str_replace(
	// 					$strToReplace,
	// 					'',
	// 					$uri[0]
	// 				);

	// 		}

	// 		return $this->checkViewFile($componentName);
	// 	// }
	// }

	// protected function checkViewFile($componentName)
	// {
	// 	if ($this->viewFile) {
	// 		$viewFile = $this->viewFile;

	// 		// return $this->modules->views->render(
	// 		// 	$this->response,
	// 		// 	$this->viewFile,
	// 		// 	$this->viewsData,
	// 		// 	$componentName
	// 		// );
	// 	} else {
	// 		if ($componentName === 'default') {
	// 			$viewFile =
	// 				'/' . $this->applicationName . '/' . $this->defaultViewsName .
	// 				'/html/' . $this->defaultComponentsName . '/view.html';

	// 			$componentName = $this->defaultComponentsName;

	// 			// return $this->modules->views->render(
	// 			// 	$this->response,
	// 			// 	'/' . $this->defaultViewsName . '/html/' . $this->defaultComponentsName . '/view.html',
	// 			// 	$this->viewsData,
	// 			// 	$this->defaultComponentsName
	// 			// );
	// 		} else {
	// 			if ($this->applicationInfo['name'] === 'Base') {
	// 				$viewFile = '/Base/Default/html/' . $componentName . '/view.html';
	// 				// return $this->modules->views->render(
	// 				// 	$this->response,
	// 				// 	'/Base/html/' . $componentName . '/view.html',
	// 				// 	$this->viewsData,
	// 				// 	$componentName
	// 				// );
	// 			} else {
	// 				$viewFile =
	// 					'/' . $this->applicationName . '/' . $this->defaultViewsName .
	// 					'/html/' . $componentName . '/view.html';

	// 				// return $this->modules->views->render(
	// 				// 	$this->response,
	// 				// 	'/' . $this->defaultViewsName . '/html/' . $componentName . '/view.html',
	// 				// 	$this->viewsData,
	// 				// 	$componentName
	// 				// );
	// 			}
	// 		}
	// 	}

	// 	return $this->modules->views->render(
	// 		$this->response,
	// 		$viewFile,
	// 		$this->viewsData,
	// 		$componentName
	// 	);
	// }

	// protected function getDefaults()
	// {
	// 	// var_dump($this->applicationInfo, $this->applicationDefaults);
	// 	if ($this->applicationInfo && $this->applicationDefaults) {

	// 		$this->applicationName = $this->applicationDefaults['application'];


	// 		// foreach (unserialize($this->applicationInfo['dependencies'])['views'] as $viewsKey => $view) {
	// 		// 	if ($view['is_default'] === 'true') {
	// 		// 		$this->defaultViewsName = ucfirst($view['name']);
	// 		// 		break;
	// 		// 	}
	// 		// }
	// 		// foreach (unserialize($this->applicationInfo['dependencies'])['components'] as $componentsKey => $component) {
	// 		// 	if ($component['is_default'] === 'true') {
	// 		// 		$this->defaultComponentsName = strtolower($component['name']);
	// 		// 		break;
	// 		// 	}
	// 		// }
	// 		// if (unserialize($this->applicationInfo['settings'])['view']['is_default'] === 'true') {
	// 		// 	$this->defaultViewsName = ucfirst(unserialize($this->applicationInfo['settings'])['view']['name']);
	// 		// }
	// 		// if (unserialize($this->applicationInfo['settings'])['component']['is_default'] === 'true') {
	// 		// 	$this->defaultComponentsName =
	// 		// 		strtolower(unserialize($this->applicationInfo['settings'])['component']['name']);
	// 		// }

	// 			$this->defaultComponentsName = strtolower($this->applicationDefaults['component']);

	// 			$this->defaultViewsName = $this->applicationDefaults['view'];

	// 	} else {
	// 		$this->applicationInfo = ['name' => 'Base'];

	// 		$this->applicationName = 'Base';

	// 		$this->applicationInfo['route'] = null;
	// 	}
	// }

	// protected function viewFile($viewFile)
	// {
	// 	$this->viewFile = $viewFile;

	// 	return $this;
	// }
}