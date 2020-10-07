<?php

namespace System\Base;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

abstract class BaseComponent extends Controller
{
	protected $getQueryArr = [];
	// protected $core;

	// protected $applications;

	// protected $components;

	// protected $packages;

	protected $views;

	// protected $applicationInfo;

	// protected $applicationDefaults;

	// protected $session;

	// protected $flash;

	// protected $data;

	// protected $packagesData = [];

	// protected $request;

	// protected $response;

	// protected $viewFile;

	// protected $defaultViewsName;

	// protected $defaultComponentsName;

	// protected $getData;

	// protected $postData;

	// public function __construct(DiInterface $container)
	// {
	// 	$this->request = $container->contents->get('request');

	// 	$this->requestMethod = $this->request->getMethod();

	// 	$this->requestUri = $this->request->getServerParams()['REQUEST_URI'];

	// 	$this->getData = $this->request->getQueryParams();

	// 	$this->postData = $this->request->getParsedBody();

	// 	$this->response = $container->contents->get('response');

	// 	$this->session = $container->contents->get(Session::class);

	// 	$this->flash = $container->contents->get(Flash::class);

	// 	$this->core = $container->contents->get('core');

	// 	$this->applications = $container->contents->get('applications');

	// 	$this->components = $container->contents->get('components');

	// 	$this->packages = $container->contents->get('packages');

	// 	$this->views = $container->contents->get('views');

	// 	$this->applicationInfo = $container->contents->get('applications')->getApplicationInfo();

	// 	$this->applicationDefaults = $container->contents->get('applications')->getApplicationDefaults();

	// 	$this->viewsData = $container->contents->get('views')->getViewsData();

	// 	$this->packagesData = $container->contents->get('packages')->getPackagesData();

	// 	$this->mode = $container->contents->get('config')->get('base.debug');
	// }

	protected function onConstruct()
	{
		if (!$this->isJson()) {

			$this->checkLayout();
		}
	}

	protected function afterExecuteRoute($dispatcher)
	{
		if ($this->isJson()) {

			$this->view->disable();

			$this->response->setContentType('application/json', 'UTF-8');
			$this->response->setHeader('Cache-Control', 'no-store');

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		}
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
					} else {
						$this->buildAssets();
					}
				} else {
					$this->buildAssets();
				}
			} else {
				$this->buildAssets();
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
				} else {
					$this->buildAssets();
				}
			} else {
				$this->buildAssets();
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

	protected function buildAssets()
	{
		$headLinks = $this->assets->collection('headLinks');
		$headStyle = $this->assets->collection('headStyle');
		$headJs = $this->assets->collection('headJs');
		$footerJs = $this->assets->collection('footerJs');

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


	protected function generateView()
	{
		$this->getDefaults();

		// var_dump($this->getData);
		// if ($this->getData === '') {
		// 	if ($this->requestUri === '/' ||
		// 		$this->requestUri === '/' .
		// 			strtolower($this->applicationInfo['name']) . '/' ||
		// 		$this->requestUri === '/' .
		// 			strtolower($this->applicationInfo['name']) . '/' . $this->defaultComponentsName
		// 		) {
		// 		return $this->checkViewFile('default');
		// 	} else {
		// 		$path =
		// 			str_replace(
		// 				'/' . strtolower($this->applicationInfo['name']) . '/',
		// 				'',
		// 				$this->requestUri
		// 			);
		// 		return $this->checkViewFile($path);
		// 	}
		// } else {
			$uri = explode('?', $this->requestUri);
			// dump($uri, $this->applicationName);
			if ($uri[0] === '/' ||
				$uri[0] === '/' . strtolower($this->applicationName) ||
				$uri[0] === '/' . strtolower($this->applicationName) . '/' ||
				$uri[0] === '/' . strtolower($this->applicationInfo['route']) ||
				$uri[0] === '/' . strtolower($this->applicationInfo['route']) . '/' ||
				$uri[0] === '/' . strtolower($this->applicationInfo['route']) . '/' . $this->defaultComponentsName ||
				$uri[0] === '/' . strtolower($this->applicationName) . '/' . $this->defaultComponentsName
				) {

				return $this->checkViewFile('default');
			} else {
				$explodeUri = explode('/', trim($uri[0], '/'));

				if ($explodeUri[0] === strtolower($this->applicationInfo['route'])) {
					$strToReplace =
						'/' . strtolower($this->applicationInfo['route']) . '/';

				} else if ($explodeUri[0] === strtolower($this->applicationName)) {
					$strToReplace =
						'/' . strtolower($this->applicationName) . '/';
				}

				$componentName =
					str_replace(
						$strToReplace,
						'',
						$uri[0]
					);

			}

			return $this->checkViewFile($componentName);
		// }
	}

	protected function checkViewFile($componentName)
	{
		if ($this->viewFile) {
			$viewFile = $this->viewFile;

			// return $this->modules->views->render(
			// 	$this->response,
			// 	$this->viewFile,
			// 	$this->viewsData,
			// 	$componentName
			// );
		} else {
			if ($componentName === 'default') {
				$viewFile =
					'/' . $this->applicationName . '/' . $this->defaultViewsName .
					'/html/' . $this->defaultComponentsName . '/view.html';

				$componentName = $this->defaultComponentsName;

				// return $this->modules->views->render(
				// 	$this->response,
				// 	'/' . $this->defaultViewsName . '/html/' . $this->defaultComponentsName . '/view.html',
				// 	$this->viewsData,
				// 	$this->defaultComponentsName
				// );
			} else {
				if ($this->applicationInfo['name'] === 'Base') {
					$viewFile = '/Base/Default/html/' . $componentName . '/view.html';
					// return $this->modules->views->render(
					// 	$this->response,
					// 	'/Base/html/' . $componentName . '/view.html',
					// 	$this->viewsData,
					// 	$componentName
					// );
				} else {
					$viewFile =
						'/' . $this->applicationName . '/' . $this->defaultViewsName .
						'/html/' . $componentName . '/view.html';

					// return $this->modules->views->render(
					// 	$this->response,
					// 	'/' . $this->defaultViewsName . '/html/' . $componentName . '/view.html',
					// 	$this->viewsData,
					// 	$componentName
					// );
				}
			}
		}

		return $this->modules->views->render(
			$this->response,
			$viewFile,
			$this->viewsData,
			$componentName
		);
	}

	protected function getDefaults()
	{
		// var_dump($this->applicationInfo, $this->applicationDefaults);
		if ($this->applicationInfo && $this->applicationDefaults) {

			$this->applicationName = $this->applicationDefaults['application'];


			// foreach (unserialize($this->applicationInfo['dependencies'])['views'] as $viewsKey => $view) {
			// 	if ($view['is_default'] === 'true') {
			// 		$this->defaultViewsName = ucfirst($view['name']);
			// 		break;
			// 	}
			// }
			// foreach (unserialize($this->applicationInfo['dependencies'])['components'] as $componentsKey => $component) {
			// 	if ($component['is_default'] === 'true') {
			// 		$this->defaultComponentsName = strtolower($component['name']);
			// 		break;
			// 	}
			// }
			// if (unserialize($this->applicationInfo['settings'])['view']['is_default'] === 'true') {
			// 	$this->defaultViewsName = ucfirst(unserialize($this->applicationInfo['settings'])['view']['name']);
			// }
			// if (unserialize($this->applicationInfo['settings'])['component']['is_default'] === 'true') {
			// 	$this->defaultComponentsName =
			// 		strtolower(unserialize($this->applicationInfo['settings'])['component']['name']);
			// }

				$this->defaultComponentsName = strtolower($this->applicationDefaults['component']);

				$this->defaultViewsName = $this->applicationDefaults['view'];

		} else {
			$this->applicationInfo = ['name' => 'Base'];

			$this->applicationName = 'Base';

			$this->applicationInfo['route'] = null;
		}
	}

	protected function viewFile($viewFile)
	{
		$this->viewFile = $viewFile;

		return $this;
	}
}