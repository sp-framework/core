<?php

namespace Applications\Ecom\Admin\Packages\Barebone\Tasks;

use Applications\Ecom\Admin\Packages\Barebone\Copy;
use Applications\Ecom\Admin\Packages\Barebone\Modify;
use Applications\Ecom\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class All extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if ($this->modules->applications->getNamedApplication($postData['applicationName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Application ' . ucfirst($postData['applicationName']) . ' already exists. Please choose another name.' ;

			return false;
		}

		if (!ctype_alpha($postData['applicationName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Application name cannot have spaces, special characters or numbers';

			return false;

		} else {

			// if ($postData['default'] === 'true' && $postData['force'] !== '1') {

			// 	$this->packagesData->defaultApplication
			// 		= $this->modules->applications->getDefaultApplication();

			// 	if ($this->packagesData->defaultApplication) {

			// 		$this->packagesData->responseCode = 2;

			// 		$this->packagesData->responseMessage =
			// 			$this->packagesData->defaultApplication['name'] .
			// 			' application is already set to default. Make application ' .
			// 			$postData['applicationName'] .
			// 			' as default?';

			// 		return false;
			// 	}
			// }

			$names = [];
			$names['applicationName'] = ucfirst(strtolower($postData['applicationName']));
			$names['applicationRoute'] = strtolower($postData['applicationName']);
			$names['componentName'] = 'Home';
			$names['componentRoute'] = 'home';
			$names['packageName'] = 'Home';
			$names['middlewareName'] = 'Home';
			$names['viewName'] = 'Default';

			$installedFiles = $copy->copyModuleStructure('applications', $names, $postData);
			$modify->modifyModuleFiles('applications', $names, $postData);
			$newApplication = $register->registerModule('applications', null, $installedFiles, $names, $postData);

			if (is_array($newApplication)) {

				$installedFiles = $copy->copyModuleStructure('components', $names, $postData);
				$modify->modifyModuleFiles('components', $names, $postData);
				$register->registerModule('components', $newApplication['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('packages', $names, $postData);
				$modify->modifyModuleFiles('packages', $names, $postData);
				$register->registerModule('packages', $newApplication['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('middlewares', $names, $postData);
				$modify->modifyModuleFiles('middlewares', $names, $postData);
				$register->registerModule('middlewares', $newApplication['id'], $installedFiles, $names, $postData);

				$installedFiles = $copy->copyModuleStructure('views', $names, $postData);
				$modify->modifyModuleFiles('views', $names, $postData);
				$register->registerModule('views', $newApplication['id'], $installedFiles, $names, $postData);

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone application ' . $names['applicationName'] . ' & its dependencies installed.';

				$this->packagesData->bareboneModule = $newApplication;

				// if ($postData['default'] === 'true' && $postData['force'] === '1') {
				// 	$this->modules->applications->removeDefaultFlag();
				// }

				return true;

			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'Barebone application ' . $names['applicationName'] . ' not installed!';

				return false;
			}
		}
	}
}