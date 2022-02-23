<?php

namespace Apps\Dash\Components\Devtools\Matrix;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\Devtools\Matrix\Matrix;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class MatrixComponent extends BaseComponent
{
	use DynamicTable;

	protected $matrixPackage;

	public function initialize()
	{
		$this->matrixPackage = $this->usePackage(Matrix::class);
	}

	/**
	 * @acl(name=view)
	 */
	public function viewAction()
	{
		$modules = [];

		$modules['core']['value'] = 'Core';
		$modules['core']['childs'][1] = $this->core->core;
		$modules['components']['value'] = 'Components';
		$modules['components']['childs'] = msort($this->modules->components->components, 'name');
		$modules['packages']['value'] = 'Packages';
		$modules['packages']['childs'] = msort($this->modules->packages->packages, 'name');
		$modules['middlewares']['value'] = 'Middlewares';
		$modules['middlewares']['childs'] = msort($this->modules->middlewares->middlewares, 'name');
		$modules['views']['value'] = 'Views';
		$modules['views']['childs'] = $this->modules->views->views;

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

			if ($this->getData()['id'] != 0) {
				$this->view->type = $type;

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

			return;
		}
	}

	public function updateAction()
	{
		if ($this->request->isPost()) {
			if (!$this->checkCSRF()) {
				return;
			}
			$this->matrixPackage->updateMatrix($this->postData());

			$this->addResponse($this->matrixPackage->packagesData->responseMessage, $this->matrixPackage->packagesData->responseCode);
		} else {
			$this->addResponse('Method Not Allowed', 1);
		}
	}
}