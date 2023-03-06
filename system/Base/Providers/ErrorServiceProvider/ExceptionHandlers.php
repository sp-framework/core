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

		return redirect($exception->getPath());
	}

	public function handleCsrfTokenException()
	{
		$this->flash->now('warning', 'Session expired, please login again.');

		return redirect('/auth/login');
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