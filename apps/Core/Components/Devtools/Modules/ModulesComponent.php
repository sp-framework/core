<?php

namespace Apps\Core\Components\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\Modules;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class ModulesComponent extends BaseComponent
{
	protected $modulesPackage;

	public function initialize()
	{
		$this->modulesPackage = $this->usePackage(Modules::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		if (isset($this->getData()['includecoremodules'])) {
			$this->view->includecoremodules = true;
		}

		$modules = [];

		$modules['core']['value'] = 'Core';
		$modules['core']['childs'][1] = $this->core->core;

		$componentsArr = msort($this->modules->components->components, 'name');
		foreach ($componentsArr as $key => &$component) {
			if (!isset($this->getData()['includecoremodules'])) {
				if ($component['app_type'] === 'core') {
					unset($componentsArr[$key]);
				}
			} else {
				$component['name'] = ucfirst($component['app_type']) . ' : ' . $component['name'];
			}
		}
		if (count($componentsArr) > 0) {
			$modules['components']['value'] = 'Components';
			$modules['components']['childs'] = $componentsArr;
		}

		$packagesArr = msort($this->modules->packages->packages, 'name');
		foreach ($packagesArr as $key => &$package) {
			if (!isset($this->getData()['includecoremodules'])) {
				if ($package['app_type'] === 'core') {
					unset($packagesArr[$key]);
				}
			} else {
				$package['name'] = ucfirst($package['app_type']) . ' : ' . $package['name'];
			}
		}
		if (count($packagesArr) > 0) {
			$modules['packages']['value'] = 'Packages';
			$modules['packages']['childs'] = $packagesArr;
		}

		$middlewaresArr = msort($this->modules->middlewares->middlewares, 'name');
		foreach ($middlewaresArr as $key => &$middleware) {
			if (!isset($this->getData()['includecoremodules'])) {
				if ($middleware['app_type'] === 'core') {
					unset($middlewaresArr[$key]);
				}
			} else {
				$middleware['name'] = ucfirst($middleware['app_type']) . ' : ' . $middleware['name'];
			}
		}
		if (count($middlewaresArr) > 0) {
			$modules['middlewares']['value'] = 'Middlewares';
			$modules['middlewares']['childs'] = $middlewaresArr;
		}

		$viewsArr = msort($this->modules->views->views, 'name');
		foreach ($viewsArr as $key => &$view) {
			if (!isset($this->getData()['includecoremodules'])) {
				if ($view['app_type'] === 'core') {
					unset($viewsArr[$key]);
				}
			} else {
				$view['name'] = ucfirst($view['app_type']) . ' : ' . $view['name'];
			}
		}
		if (count($viewsArr) > 0) {
			$modules['views']['value'] = 'Views';
			$modules['views']['childs'] = $viewsArr;
		}

		$this->view->modules = $modules;

		$modulesJson = [];

		foreach ($modules as $moduleKey => $module) {
			foreach ($module['childs'] as $childKey => $child) {
				$modulesJson[$moduleKey][$child['id']] =
					[
						'name' 		=> $child['name'],
						'version' 	=> $child['version'],
						'repo' 		=> $child['repo'],
					];
			}
		}

		$this->view->modulesJson = Json::encode($modulesJson);

		if (isset($this->getData()['id']) &&
			isset($this->getData()['module']) &&
			isset($this->getData()['type'])
		) {
			$type = strtolower($this->getData()['type']);

			$this->view->type = $type;
			$this->view->module = null;

			if ($this->getData()['id'] != 0) {
				if ($type === 'core') {
					$core = $this->core->core;

					if (is_array($core['settings'])) {
						$core['settings'] = Json::encode($core['settings'], JSON_UNESCAPED_SLASHES);
					}

					$this->view->module = $core;
				} else if ($type === 'components') {
					$component = $this->modules->components->getById($this->getData()['id']);

					$component['dependencies'] = Json::decode($component['dependencies'], true);
					$component['dependencies'] = Json::encode($component['dependencies'], JSON_UNESCAPED_SLASHES);

					$this->view->module = $component;
				} else if ($type === 'packages') {
					$package = $this->modules->packages->getById($this->getData()['id']);

					$this->view->module = $package;

				} else if ($type === 'middlewares') {
					$middleware = $this->modules->middlewares->getById($this->getData()['id']);

					$this->view->module = $middleware;
				} else if ($type === 'views') {
					$view = $this->modules->views->getById($this->getData()['id']);

					$view['dependencies'] = Json::decode($view['dependencies'], true);
					$view['dependencies'] = Json::encode($view['dependencies'], JSON_UNESCAPED_SLASHES);

					$this->view->module = $view;
				}
			}
		} else {
			$this->view->pick('modules/list');
		}
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}
			$this->modulesPackage->updateModules($this->postData());

			$this->addResponse($this->modulesPackage->packagesData->responseMessage, $this->modulesPackage->packagesData->responseCode);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}
}