<?php

namespace Applications\Core\Admin\Packages\Module\Settings;

use System\Base\BasePackage;

class Applications extends BasePackage
{
	protected $defaultApplication;

	public function get($getData)
	{
		$this->packagesData->type = 'applications';

		$components = $this->modules->components->components;

		$this->packagesData->components =
			$this->modules->components->getComponentsForApplication($getData['id']);

		$this->packagesData->views =
			$this->modules->views->getViewsForApplication($getData['id']);

		$this->packagesData->application =
			$this->modules->applications->getById($getData['id']);

		$this->packagesData->domains = $this->modules->domains->domains;

		$this->packagesData->settings =
			json_decode(
				$this->packagesData->application['settings'], true
			);

		$this->packagesData->email =
			json_decode(
				$this->packagesData->application['settings'], true
			)['email'];

		$this->packagesData->responseCode = 0;

		return $this;
	}

	public function update(array $postData)
	{
		$thisApplication = $this->modules->applications->getById($postData['id'], false, false);

		if ($thisApplication['installed'] === '0') {

			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage =
				'Application is not installed! Cannot edit settings.';

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

			if ($this->checkDefaultApplication($postData)) {

				$this->packagesData->responseCode = 2;

				$this->packagesData->responseMessage =
					$this->defaultApplication['name'] . ' application is already set to default.' .
					' Make application ' . $postData['name'] . ' as default?';

				return false;
			}

			if ($postData['route'] !== '') {
				if ($this->checkDuplicateRoute($postData)) {
					$this->packagesData->responseCode = 3;

					$this->packagesData->responseMessage =
						$this->packagesData->duplicateApplicationNameRoute['name'] .
						' application is already set to use this route. Please change route and try again.';

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

		$update = $this->modules->applications->update($postData);

		if ($update) {

			$this->packagesData->responseCode = 0;

			$this->packagesData->responseMessage = 'Application settings updated.';

			return true;
		} else {
			$this->packagesData->responseCode = 1;

			$this->packagesData->responseMessage = 'Error updating application settings.';

			return false;
		}
	}
	protected function checkDefaultApplication($postData)
	{
		$defaultApplication = $this->modules->applications->getDefaultApplication();

		// Checking if same application is default
		if ($defaultApplication) {
			if (strval($defaultApplication['id']) === $postData['id']) {
				return false;
			}
		} else {
			return false;
		}

		if (is_array($defaultApplication)) {

			$this->packagesData->defaultApplication = $defaultApplication;

			return true;
		} else {
			return false;
		}
	}

	protected function checkDuplicateRoute($postData)
	{
		$duplicateApplicationRoute = $this->modules->applications->getRouteApplication($postData['route']);
		$duplicateApplicationName = $this->modules->applications->getNamedApplication($postData['route']);

		if (is_array($duplicateApplicationRoute) || is_array($duplicateApplicationName)) {
			if (is_array($duplicateApplicationRoute)) {

				if ($postData['id'] == $duplicateApplicationRoute['id']) {
					return false;
				} else {
					$this->packagesData->duplicateApplicationNameRoute = $duplicateApplicationRoute;
				}
			} else if (count($duplicateApplicationName) > 0) {

				if ($postData['id'] == $duplicateApplicationName['id']) {
					return false;
				} else {
					$this->packagesData->duplicateApplicationNameRoute = $duplicateApplicationName;
				}
			}

			return true;
		} else {
			return false;
		}
	}
}