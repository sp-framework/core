<?php

namespace Packages\Admin\Modules\Module;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use System\Base\BasePackage;

class Settings extends BasePackage
{
	protected $defaultApplication;

	public function get($getData)
	{
		if ($getData['type'] === 'applications') {

			$this->packagesData->type = 'applications';

			$this->packagesData->components =
				getAllArr(
					$this->components
					->getAll(['application_id' => $getData['id']])
				);

			$this->packagesData->views =
				getAllArr(
					$this->views
					->getAll(['application_id' => $getData['id']])
				);

			$this->packagesData->application =
				$this->applications
				->getById($getData['id'])
				->getAllArr();

			$this->packagesData->settings
				= json_decode(
					$this->applications
					->getById($getData['id'])
					->getAllArr()['settings'], true
				);

			$this->packagesData->responseCode = 0;

			return $this->packagesData;

		} else if ($getData['type'] === 'components') {

			$this->packagesData->type = 'components';

			$this->packagesData->components =
				$this->components->getAll(['application_id' => $getData['applicationid']]);

			$thisComponent =
				$this->components->getAll(['id' => $getData['id']])[0]->getAllArr();

			$this->packagesData->component = $thisComponent;

			$this->packagesData->settings =
				$thisComponent['settings'] ?? json_decode($thisComponent['settings'], true);

			$fileSystem = new Filesystem(new Local('components/Admin/Install/' . $thisComponent['name'] . '/'));

			if ($fileSystem->has('settings.html')) {
				$this->packagesData->componentSettingsFileContent =
					$fileSystem->read('settings.html');
			} else {
				$this->packagesData->componentSettingsFileContent =
					'This Component does not require any settings.';
			}

			return $this->packagesData;

		} else if ($getData['type'] === 'packages') {

			$this->packagesData->type = 'packages';

			$this->packagesData->packages =
				$this->packages->getAll(['application_id' => $getData['applicationid']]);

			$thisPackage =
				$this->packages->getAll(['id' => $getData['id']])[0]->getAllArr();

			$this->packagesData->package = $thisPackage;

			$this->packagesData->settings =
				$thisPackage['settings'] ?? json_decode($thisPackage['settings'], true);

			$fileSystem = new Filesystem(new Local('packages/Admin/Install/' . $thisPackage['name'] . '/'));

			if ($fileSystem->has('settings.html')) {
				$this->packagesData->packageSettingsFileContent =
					$fileSystem->read('settings.html');
			} else {
				$this->packagesData->packageSettingsFileContent =
					'This Package does not require any settings.';
			}

			return $this->packagesData;

		} else if ($getData['type'] === 'middlewares') {

			$this->packagesData->type = 'middlewares';

			$this->packagesData->middlewares =
				$this->middlewares->getAll(['application_id' => $getData['applicationid']], ['sequence' => 'ASC']);

			$thisMiddleware =
				$this->middlewares->getAll(['id' => $getData['id']])[0]->getAllArr();

			$this->packagesData->middleware = $thisMiddleware;

			$this->packagesData->settings =
				$thisMiddleware['settings'] ?? json_decode($thisMiddleware['settings'], true);

			$fileSystem = new Filesystem(new Local('middlewares/Admin/Install/' . $thisMiddleware['name'] . '/'));

			if ($fileSystem->has('settings.html')) {
				$this->packagesData->middlewareSettingsFileContent =
					$fileSystem->read('settings.html');
			} else {
				$this->packagesData->middlewareSettingsFileContent =
					'This Middleware does not require any settings.';
			}

			return $this->packagesData;

		} else if ($getData['type'] === 'views') {

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
	}

	public function update($postData)
	{
		if ($postData['type'] === 'applications') {

			$thisApplication =
				$this->applications
					->getById($postData['id'])
					->getAllArr();

			if ($thisApplication['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Application is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			if (!ctype_alpha($postData['route']) && $postData['route'] !== '') {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Route name cannot have spaces, special characters or numbers';

				return $this->packagesData;

			} else if ($postData['settings']['component'] === '0' ||
					   $postData['settings']['view'] === '0'
					  ) {

					if ($postData['settings']['component'] === '0') {

						$this->packagesData->responseMessage = 'Please select default component';

					} else if ($postData['settings']['view'] === '0') {

						$this->packagesData->responseMessage = 'Please select default view';

					}

					$this->packagesData->responseCode = 1;

					return $this->packagesData;

			} else {

				if ($postData['is_default'] === 'true' &&
					$postData['force'] !== '1'
				   ) {

					if ($this->checkDefaultApplication($postData)) {

						$this->packagesData->responseCode = 2;

						$this->packagesData->responseMessage =
							$this->packagesData->defaultApplication->get('name') .
							' application is already set to default. Make application ' . $postData['name'] .
							' as default?';

						return $this->packagesData;
					}

					if ($postData['route'] !== '') {
						if ($this->checkDuplicateRoute($postData)) {
							$this->packagesData->responseCode = 3;

							$this->packagesData->responseMessage =
								$this->packagesData->duplicateApplicationNameRoute->get('name') .
								' application is already set to use this route. Please change route and try again.';

							return $this->packagesData;
						}
					}
				}
			}

			if ($postData['is_default'] === 'true' &&
				$postData['force'] === '1'
				) {
				$this->removeDefaultFlag();
			}

			$settings = [];
			$settings['component'] = $postData['settings']['component'];
			$settings['view'] = $postData['settings']['view'];

			$postData['settings'] = json_encode($settings);

			$postData['is_default'] = $postData['is_default'] === 'true' ? 1 : 0;

			$update = $this->applications->update($postData);

			if (is_object($update)) {

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Application settings updated.';

				return $this->packagesData;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Error updating application settings.';

				return $this->packagesData;
			}

		} else if ($postData['type'] === 'components') {

			$thisComponent =
				$this->components
					->getById($postData['id'])
					->getAllArr();

			if ($thisComponent['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Component is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			$postData['settings'] = isset($postData['settings']) ? json_encode($postData['settings']) : null;

			$update = $this->components->update($postData);

			if (is_object($update)) {

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

		} else if ($postData['type'] === 'packages') {

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

			if (is_object($update)) {

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

		} else if ($postData['type'] === 'middlewares') {

			$thisMiddleware =
				$this->middlewares
					->getById($postData['id'])
					->getAllArr();

			if ($thisMiddleware['installed'] === 0) {

				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Middleware is not installed! Cannot edit settings.';

				return $this->packagesData;
			}

			$postData['settings'] = isset($postData['settings']) ? json_encode($postData['settings']) : null;

			$postData['enabled'] = $postData['enabled'] === 'true' ? 1 : 0;

			$middlewaresSequence = isset($postData['middlewares_sequence']) ? $postData['middlewares_sequence'] : null;

			if ($middlewaresSequence) {
				unset($postData['middlewares_sequence']);
			}

			$update = $this->middlewares->update($postData);

			if (is_object($update)) {

				if ($middlewaresSequence) {

					foreach ($middlewaresSequence as $middlewareKey => $middlewareId) {

						$this->middlewares->update(['id' => $middlewareId, 'sequence' => $middlewareKey]);

					}
				}

				$this->packagesData->responseCode = 0;

				$this->packagesData->responseMessage = 'Middleware settings updated.';

				return $this->packagesData;
			} else {
				$this->packagesData->responseCode = 1;

				$this->packagesData->responseMessage = 'Error updating middleware settings.';

				return $this->packagesData;
			}

			$this->packagesData->type = 'middlewares';

			return $this->packagesData;

		} else if ($postData['type'] === 'views') {

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

			if (is_object($update)) {

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

	protected function getDefaultApplication()
	{
		if (!$this->defaultApplication) {

			$this->defaultApplication = $this->applications->getAll(["is_default" => 1]);

			return $this->defaultApplication;
		} else {

			return $this->defaultApplication;
		}
	}

	protected function removeDefaultFlag()
	{
		$defaultApplication = $this->getDefaultApplication();

		if (count($defaultApplication) > 0) {

			$defaultApplication =
				getAllArr($defaultApplication)[0];

			$defaultApplication['is_default'] = 0;

			$this->applications->update($defaultApplication);
		}
	}

	protected function checkDefaultApplication($postData)
	{
		$defaultApplication = $this->getDefaultApplication();

		// Checking if same application is default
		if ($defaultApplication) {
			if (strval($defaultApplication[0]->get('id')) === $postData['id']) {
				return false;
			}
		} else {
			return false;
		}

		if (count($defaultApplication) > 0) {

			$this->packagesData->defaultApplication = $defaultApplication[0];

			return true;
		} else {
			return false;
		}
	}

	protected function checkDuplicateRoute($postData)
	{
		$duplicateApplicationRoute = $this->applications->getAll(["route" => $postData['route']]);
		$duplicateApplicationName = $this->applications->getAll(["name" => $postData['route']]);

		if (count($duplicateApplicationRoute) > 0 || count($duplicateApplicationName) > 0) {
			if (count($duplicateApplicationRoute) > 0) {

				if ($postData['id'] == $duplicateApplicationRoute[0]->get('id')) {
					return false;
				} else {
					$this->packagesData->duplicateApplicationNameRoute = $duplicateApplicationRoute[0];
				}
			} else if (count($duplicateApplicationName) > 0) {

				if ($postData['id'] == $duplicateApplicationName[0]->get('id')) {
					return false;
				} else {
					$this->packagesData->duplicateApplicationNameRoute = $duplicateApplicationName[0];
				}
			}

			return true;
		} else {
			return false;
		}
	}
}