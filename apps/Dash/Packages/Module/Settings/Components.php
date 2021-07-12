<?php

namespace Apps\Ecom\Admin\Packages\Module\Settings;

use Phalcon\Helper\Json;

class Components
{
	public function get($getData)
	{
			$this->packagesData->type = 'components';

			$this->packagesData->components =
				$this->components->getAll(['app_id' => $getData['appid']]);

			$thisComponent =
				$this->components->getAll(['id' => $getData['id']])[0]->getAllArr();

			$this->packagesData->component = $thisComponent;

			$this->packagesData->settings =
				$thisComponent['settings'] ?? Json::decode($thisComponent['settings'], true);

			$this->localContent->setPathPrefix('components/Admin/Install/' . $thisComponent['name'] . '/');

			if ($this->localContent->fileExists('settings.html')) {
				$this->packagesData->componentSettingsFileContent =
					$this->localContent->read('settings.html');
			} else {
				$this->packagesData->componentSettingsFileContent =
					'This Component does not require any settings.';
			}

			return $this->packagesData;
	}

	public function update()
	{
			$thisComponent =
				$this->components
					->getById($postData['id'])
					->getAllArr();

			if ($thisComponent['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Component is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			$postData['settings'] = isset($postData['settings']) ? Json::encode($postData['settings'], JSON_UNESCAPED_SLASHES) : null;

			$update = $this->components->update($postData);

			if ($update) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Component settings updated.';

				return $this->packagesData;

			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Error updating component settings.';

				return $this->packagesData;
			}


			$this->packagesData->type = 'components';

			return $this->packagesData;
	}
}