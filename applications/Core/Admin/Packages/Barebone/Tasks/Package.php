<?php

namespace Applications\Core\Admin\Packages\Barebone\Tasks;

use Applications\Core\Admin\Packages\Barebone\Copy;
use Applications\Core\Admin\Packages\Barebone\Modify;
use Applications\Core\Admin\Packages\Barebone\Register;
use System\Base\BasePackage;

class Package extends BasePackage
{
	public function run($postData)
	{
		$copy = new Copy;
		$modify = new Modify;
		$register = new Register;

		if ($this->modules->packages
				->getNamedPackageForApplication($postData['packageName'], $postData['application_id'])
		) {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package ' . ucfirst($postData['packageName']) . ' already exists for this application.' .
				' Please choose another name.' ;

			return false;
		}

		if (!ctype_alpha($postData['packageName'])) {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Package name cannot have spaces, special characters or numbers';

			return false;

		} else {

			$names = [];
			$names['applicationName'] =
				$this->modules->applications->getIdApplication($postData['application_id'])['name'];

			$names['packageName'] = ucfirst(strtolower($postData['packageName']));

			$installedFiles = $copy->copyModuleStructure('packages', $names, $postData);

			$modify->modifyModuleFiles('packages', $names, $postData);

			$newPackage =
				$register->registerModule('packages', $postData['application_id'], $installedFiles, $names, $postData);

			if (is_array($newPackage)) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage =
					'Barebone package ' . $postData['packageName'] . ' installed.';

				$this->packagesData->bareboneModule = $newPackage;

				return true;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage =
					'Barebone package ' . $postData['packageName'] . ' not installed.';

				return false;
			}
		}
	}
}