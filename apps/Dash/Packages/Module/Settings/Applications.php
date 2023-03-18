<?php

namespace Apps\Ecom\Admin\Packages\Module\Settings;

use System\Base\BasePackage;

class Apps extends BasePackage
{
	protected $defaultApp;

	public function get($getData)
	{
		$this->packagesData->type = 'apps';

		$components = $this->modules->components->components;

		$this->packagesData->components =
			$this->modules->components->get(['app_id' => $getData['id']]);

		$this->packagesData->views =
			$this->modules->views->get(['app_id' => $getData['id']]);

		$this->packagesData->app =
			$this->apps->getById($getData['id']);

		$this->packagesData->domains = $this->modules->domains->domains;

		$this->packagesData->settings =
			json_decode(
				$this->packagesData->app['settings'], true
			);

		$this->packagesData->email =
			json_decode(
				$this->packagesData->app['settings'], true
			)['email'];

		$this->packagesData->responseCode = 0;

		return $this;
	}

	public function update(array $postData)
	{
		$thisApp = $this->apps->getById($postData['id'], false, false);

		if ($thisApp['installed'] === '0') {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'App is not installed! Cannot edit settings.';

			return false;
		}

		if (!ctype_alpha($postData['route']) && $postData['route'] !== '') {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Route name cannot have spaces, special characters or numbers';

			return false;
		}

		if ($postData['settings']['component'] === '0' ||
			$postData['settings']['view'] === '0' ||
			$postData['settings']['errorComponent'] === '0'
		) {
			if ($postData['settings']['component'] === '0') {

				$this->packagesData->responseMessage = 'Please select default component';

			} else if ($postData['settings']['view'] === '0') {

				$this->packagesData->responseMessage = 'Please select default view';

			} else if ($postData['settings']['errorComponent'] === '0') {

				$this->packagesData->responseMessage = 'Please select default error component';

			}

			$this->packagesData->responseCode = 1;

			return false;
		}

		if ($postData['is_default'] === 'true' && $postData['force'] !== '1') {

			if ($this->checkDefaultApp($postData)) {

				$this->packagesData->responseCode = 2;

				$this->packagesData->responseMessage =
					$this->defaultApp['name'] . ' app is already set to default.' .
					' Make app ' . $postData['name'] . ' as default?';

				return false;
			}

			if ($postData['route'] !== '') {
				if ($this->checkDuplicateRoute($postData)) {
					$this->packagesData->responseCode = 3;

					$this->packagesData->responseMessage =
						$this->packagesData->duplicateAppNameRoute['name'] .
						' app is already set to use this route. Please change route and try again.';

					return false;
				}
			}
		}

		// if ($postData['is_default'] === 'true' && $postData['force'] === '1') {
		// 	$this->removeDefaultFlag();
		// }

		// $settings = [];
		// $settings['component'] = $postData['settings']['component'];
		// $settings['view'] = $postData['settings']['view'];

		$postData['settings'] = json_encode($postData['settings']);

		$postData['is_default'] = $postData['is_default'] === 'true' ? 1 : 0;

		$update = $this->apps->update($postData);

		if ($update) {

			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'App settings updated.';

			return true;
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating app settings.';

			return false;
		}
	}
	protected function checkDefaultApp($postData)
	{
		$defaultApp = $this->apps->get(['id' => '1']);

		// Checking if same app is default
		if ($defaultApp) {
			if (strval($defaultApp['id']) === $postData['id']) {
				return false;
			}
		} else {
			return false;
		}

		if (is_array($defaultApp)) {

			$this->packagesData->defaultApp = $defaultApp;

			return true;
		} else {
			return false;
		}
	}

	protected function checkDuplicateRoute($postData)
	{
		$duplicateAppRoute = $this->apps->get(['route' => $postData['route']]);
		$duplicateAppName = $this->apps->get(['name' => $postData['route']]);

		if (is_array($duplicateAppRoute) || is_array($duplicateAppName)) {
			if (is_array($duplicateAppRoute)) {

				if ($postData['id'] == $duplicateAppRoute['id']) {
					return false;
				} else {
					$this->packagesData->duplicateAppNameRoute = $duplicateAppRoute;
				}
			} else if (count($duplicateAppName) > 0) {

				if ($postData['id'] == $duplicateAppName['id']) {
					return false;
				} else {
					$this->packagesData->duplicateAppNameRoute = $duplicateAppName;
				}
			}

			return true;
		} else {
			return false;
		}
	}
}