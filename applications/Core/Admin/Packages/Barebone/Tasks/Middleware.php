<?php

namespace Applications\Core\Admin\Packages\Barebone\Tasks;

use Applications\Core\Admin\Packages\Barebone\Copy;
use Applications\Core\Admin\Packages\Barebone\Modify;
use Applications\Core\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class Middleware extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if ($this->modules->middlewares
				->getNamedMiddlewareForApplication($postData['middlewareName'], $postData['application_id'])
		) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package ' . ucfirst($postData['middlewareName']) . ' already exists for this application.' .
				' Please choose another name.' ;

			return false;
		}

		if (!ctype_alpha($postData['middlewareName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package name cannot have spaces, special characters or numbers';

			return false;

		} else {

			$names = [];
			$names['applicationName'] =
				$this->modules->applications->getIdApplication($postData['application_id'])['name'];

			$names['middlewareName'] = ucfirst(strtolower($postData['middlewareName']));

			$installedFiles = $copy->copyModuleStructure('middlewares', $names, $postData);

			$modify->modifyModuleFiles('middlewares', $names, $postData);

			$newMiddleware =
				$register->registerModule('middlewares', $postData['application_id'], $installedFiles, $names, $postData);

			if (is_array($newMiddleware)) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone middleware ' . $postData['middlewareName'] . ' installed.';

				$this->packagesData->bareboneModule = $newMiddleware;

				return true;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'Barebone middleware ' . $postData['middlewareName'] . ' not installed.';

				return false;
			}
		}
	}
}