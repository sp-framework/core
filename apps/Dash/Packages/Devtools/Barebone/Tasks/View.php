<?php

namespace Apps\Ecom\Admin\Packages\Barebone\Tasks;

use Apps\Ecom\Admin\Packages\Barebone\Copy;
use Apps\Ecom\Admin\Packages\Barebone\Modify;
use Apps\Ecom\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class View extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if (isset($postData['viewName'])) {
			if ($this->modules->views->getAppView(
					$postData['app_id'],
					$postData['viewName']
				)
			) {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'View ' . ucfirst($postData['viewName']) . ' already exists for this app.' .
					' Please choose another name.' ;

				return false;
			}

			if (!ctype_alpha($postData['viewName'])) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'View name cannot have spaces, special characters or numbers';

				return false;

			} else {

				$names = [];
				$names['appName'] =
					$this->apps->getIdApp($postData['app_id'])['name'];

				$names['viewName'] = ucfirst(strtolower($postData['viewName']));

				$installedFiles = $copy->copyModuleStructure('views', $names, $postData);

				$modify->modifyModuleFiles('views', $names, $postData);

				$newView =
					$register->registerModule('views', $postData['app_id'], $installedFiles, $names, $postData);

				if (is_array($newView)) {

					$this->packagesData->responseCode = 0;

					$this->packagesData->responseMessage =
						'Barebone view ' . $postData['viewName'] . ' installed.';

					$this->packagesData->bareboneModule = $newView;

					return true;
				} else {
					$this->packagesData->responseCode = 1;

					$this->packagesData->responseMessage =
						'Barebone view ' . $postData['viewName'] . ' not installed.';

				}
			}
		} else if (isset($postData['view_id']) &&
				   isset($postData['component_id'])
		) {

			$appName =
				$this->apps->getById($postData['app_id'])['name'];

			$viewName =
				$this->modules->views->getById($postData['view_id'])['name'];

			$componentName =
				strtolower($this->modules->components->getById($postData['component_id'])['name']);

			if (!$this->localContent
					  ->has(
						'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName
					  )
			   ) {
				$this->localContent
					 ->createDir(
						'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName
					 );
			}

			if (!$this->localContent
					  ->has(
						'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName . '/view.html'
					  ) ||
				(isset($postData['force']) && $postData['force'] === '1')
			   ) {
				$this->localContent
					 ->put(
						'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName . '/view.html',
						$appName . ' ' . $viewName . ' ' . $componentName . ' view'
					 );
			} else {
				$this->packagesData->responseCode = 2;

				$this->packagesData->responseMessage =
					'view.html file already exists at location ' .
					'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName .
					'. Overwrite?';

				return $this->packagesData;
			}

			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage =
				'New view.html file at location ' .
				'apps/' . $appName . '/Views/' . $viewName . '/html/' . $componentName .
				' added.';

			return true;
		}
	}
}