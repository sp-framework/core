<?php

namespace Apps\Ecom\Admin\Packages\Module\Settings;

class Middlewares
{
	public function get()
	{
		$this->packagesData->type = 'middlewares';

		$this->packagesData->middlewares =
			$this->middlewares->getAll(['app_id' => $getData['appid']], ['sequence' => 'ASC']);

		$thisMiddleware =
			$this->middlewares->getAll(['id' => $getData['id']])[0]->getAllArr();

		$this->packagesData->middleware = $thisMiddleware;

		$this->packagesData->settings =
			$thisMiddleware['settings'] ?? json_decode($thisMiddleware['settings'], true);

		$this->localContent->setPathPrefix('middlewares/Admin/Install/' . $thisMiddleware['name'] . '/');

		if ($this->localContent->fileExists('settings.html')) {
			$this->packagesData->middlewareSettingsFileContent =
				$this->localContent->read('settings.html');
		} else {
			$this->packagesData->middlewareSettingsFileContent =
				'This Middleware does not require any settings.';
		}

		return $this->packagesData;
	}

	public function update($postData)
	{
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

			if ($update) {

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
	}
}