<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\BaseComponent;

class ExceptionHandlers extends BaseComponent
{
	protected $baseErrorDir = 'system/Base/Providers/ErrorServiceProvider/View/errors/';

	public function handlePermissionDeniedException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse('Permission Denied!', 1);

			return $this->sendJson();
		}

		return $this->setViewsDir('permissionDenied');
	}

	public function handleAppNotAllowedException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse($exception->getMessage(), 1);

			return $this->sendJson();
		}

		return $this->setViewsDir('notFound');
	}

	public function handleControllerNotFoundException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse($exception->getMessage(), 1);

			return $this->sendJson();
		}

		return $this->setViewsDir('notFound');
	}

	public function handleIdNotFoundException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse($exception->getMessage(), 1);

			return $this->sendJson();
		}

		return $this->setViewsDir('notFound');
	}

	public function handleValidationException($exception)
	{
		$this->session->set([
			'errors' => $exception->getErrors(),
			'old' => $exception->getOldInput(),
		]);

		return $this->response->redirect($exception->getPath());
	}

	public function handleCsrfTokenException()
	{
		$this->flash->now('warning', 'Session expired, please login again.');

		return $this->response->redirect('/auth/login');
	}

	public function handleInvalidDataException($exception)
	{
		$this->addResponse($exception->getMessage(), 1);

		if (str_contains($exception->getMessage(), "The data must match the 'json' format.")) {
			$this->addResponse("Json data provided is incorrect.", 1);
		}

		return $this->sendJson();
	}

	public function handleIOException($exception)
	{
		$this->addResponse($exception->getMessage(), 1);

		if (str_contains($exception->getMessage(), "Duplicate entry")) {
			$this->addResponse("Entry with same data already exists.", 1);
		}

		return $this->sendJson();
	}

	private function setViewsDir($partial)
	{
		$this->view->setViewsDir(base_path($this->baseErrorDir));

		return $this->view->partial($partial,
			[
				'route' => $this->apps->getAppInfo()['route']
			]
		);
	}
}