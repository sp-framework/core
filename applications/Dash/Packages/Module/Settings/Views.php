<?php

namespace Applications\Ecom\Admin\Packages\Module\Settings;

class Views
{
	public function get()
	{
		$this->packagesData->type = 'views';

		$this->packagesData->view =
			$this->views->getAll(['id' => $getData['id']])[0]->getAllArr();

		$this->packagesData->settings
			= json_decode(
				$this->views->getAll(['id' => $getData['id']])[0]
				->getAllArr()['settings'], true
			);

		$this->packagesData->responseCode = 0;

		return $this->packagesData;
	}

	public function update($postData)
	{
			$thisView = $this->views->getById($postData['id'])->getAllArr();

			if ($thisView['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'View is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			$thisView['display_name'] = $postData['display_name'];
			$thisView['description'] = $postData['description'];
			$thisView['repo'] = $postData['repo'];

			$thisViewSettings = json_decode($thisView['settings'], true);

			$thisViewSettings['cache'] = $postData['settings']['cache'];
			$thisViewSettings['head']['title'] = $postData['settings']['head']['title'];

			foreach ($thisViewSettings['head']['meta'] as $metaKey => $meta) {
				if ($metaKey === 'description') {
					$thisViewSettings['head']['meta'][$metaKey] =
						$postData['settings']['head']['meta']['description'];
				}
				if ($metaKey === 'keywords') {
					$thisViewSettings['head']['meta'][$metaKey] =
						$postData['settings']['head']['meta']['keywords'];
				}
			}

			$postData['settings'] = json_encode($thisViewSettings);

			$update = $this->views->update($postData);

			if ($update) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'View settings updated.';

				return $this->packagesData;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Error updating view settings.';

				return $this->packagesData;
			}
	}
}