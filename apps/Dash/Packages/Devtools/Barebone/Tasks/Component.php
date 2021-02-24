<?php

namespace Apps\Ecom\Admin\Packages\Barebone\Tasks;

use Apps\Ecom\Admin\Packages\Barebone\Copy;
use Apps\Ecom\Admin\Packages\Barebone\Modify;
use Apps\Ecom\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class Component extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if ($this->modules->components
				->getNamedComponentForApp($postData['componentName'], $postData['app_id'])
		) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Component ' . ucfirst($postData['componentName']) . ' already exists for this app.' .
				' Please choose another name.' ;

			return false;
		}

		if (!ctype_alpha($postData['componentName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Component name cannot have spaces, special characters or numbers';

			return false;

		} else {

			$names = [];
			$names['appName'] =
				$this->apps->getIdApp($postData['app_id'])['name'];

			$names['componentName'] = ucfirst(strtolower($postData['componentName']));
			$names['componentRoute'] = strtolower($postData['componentName']);

			$installedFiles = $copy->copyModuleStructure('components', $names, $postData);

			$modify->modifyModuleFiles('components', $names, $postData);

			$newComponent =
				$register->registerModule('components', $postData['app_id'], $installedFiles, $names, $postData);

			if (is_array($newComponent)) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone component ' . $names['componentName'] . ' installed.';

				$this->packagesData->bareboneModule = $newComponent;

				return true;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'Barebone component ' . $names['componentName'] . ' not installed.';

				return false;
			}
		}
	}
}