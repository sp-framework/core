<?php

namespace Applications\Admin\Packages\Module\Settings;

class Packages
{
	public function get()
	{
			$this->packagesData->type = 'packages';

			$this->packagesData->packages =
				$this->packages->getAll(['application_id' => $getData['applicationid']]);

			$thisPackage =
				$this->packages->getAll(['id' => $getData['id']])[0]->getAllArr();

			$this->packagesData->package = $thisPackage;

			$this->packagesData->settings =
				$thisPackage['settings'] ?? json_decode($thisPackage['settings'], true);

			$this->localContent->setPathPrefix('packages/Admin/Install/' . $thisPackage['name'] . '/');

			if ($this->localContent->has('settings.html')) {
				$this->packagesData->packageSettingsFileContent =
					$this->localContent->read('settings.html');
			} else {
				$this->packagesData->packageSettingsFileContent =
					'This Package does not require any settings.';
			}

			return $this->packagesData;
	}

	public function update($postData)
	{
			$thisPackage =
				$this->packages
					->getById($postData['id'])
					->getAllArr();

			if ($thisPackage['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Package is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			$postData['settings'] = isset($postData['settings']) ? json_encode($postData['settings']) : null;

			$update = $this->packages->update($postData);

			if ($update) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Package settings updated.';

				return $this->packagesData;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Error updating package settings.';

				return $this->packagesData;
			}

			$this->packagesData->type = 'packages';

			return $this->packagesData;
	}
}