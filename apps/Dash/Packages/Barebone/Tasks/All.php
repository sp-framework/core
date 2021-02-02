<?php

namespace Apps\Ecom\Admin\Packages\Barebone\Tasks;

use Apps\Ecom\Admin\Packages\Barebone\Copy;
use Apps\Ecom\Admin\Packages\Barebone\Modify;
use Apps\Ecom\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class All extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if ($this->apps->getNamedApp($postData['appName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'App ' . ucfirst($postData['appName']) . ' already exists. Please choose another name.' ;

			return false;
		}

		if (!ctype_alpha($postData['appName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'App name cannot have spaces, special characters or numbers';

			return false;

		} else {

			// if ($postData['default'] === 'true' && $postData['force'] !== '1') {

			// 	$this->packagesData->defaultApp
			// 		= $this->apps->getDefaultApp();

			// 	if ($this->packagesData->defaultApp) {

			// 		$this->packagesData->responseCode = 2;

			// 		$this->packagesData->responseMessage =
			// 			$this->packagesData->defaultApp['name'] .
			// 			' app is already set to default. Make app ' .
			// 			$postData['appName'] .
			// 			' as default?';

			// 		return false;
			// 	}
			// }

			$names = [];
			$names['appName'] = ucfirst(strtolower($postData['appName']));
			$names['appRoute'] = strtolower($postData['appName']);
			$names['componentName'] = 'Home';
			$names['componentRoute'] = 'home';
			$names['packageName'] = 'Home';
			$names['middlewareName'] = 'Home';
			$names['viewName'] = 'Default';

			$installedFiles = $copy->copyModuleStructure('apps', $names, $postData);
			$modify->modifyModuleFiles('apps', $names, $postData);
			$newApp = $register->registerModule('apps', null, $installedFiles, $names, $postData);

			if (is_array($newApp)) {

				$installedFiles = $copy->copyModuleStructure('components', $names, $postData);
				$modify->modifyModuleFiles('components', $names, $postData);
				$register->registerModule('components', $newApp['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('packages', $names, $postData);
				$modify->modifyModuleFiles('packages', $names, $postData);
				$register->registerModule('packages', $newApp['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('middlewares', $names, $postData);
				$modify->modifyModuleFiles('middlewares', $names, $postData);
				$register->registerModule('middlewares', $newApp['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('views', $names, $postData);
				$modify->modifyModuleFiles('views', $names, $postData);
				$register->registerModule('views', $newApp['id'], $installedFiles, $names, $postData);

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone app ' . $names['appName'] . ' & its dependencies installed.';

				$this->packagesData->bareboneModule = $newApp;

				// if ($postData['default'] === 'true' && $postData['force'] === '1') {
				// 	$this->apps->removeDefaultFlag();
				// }

				return true;

			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'Barebone app ' . $names['appName'] . ' not installed!';

				return false;
			}
		}
	}
}