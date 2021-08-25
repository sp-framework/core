<?php

namespace System\Base\Providers\ErrorServiceProvider;

use System\Base\BaseComponent;

class ExceptionHandlers extends BaseComponent
{
	// public function handleEmailException($exception)
	// {
	// 	$this->view->responseCode = 1;

	// 	$this->view->responseMessage = $exception->getMessage();

	// 	return $this->sendJson();
	// }

	public function handlePermissionDeniedException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse('Permission Denied!', 1);

			return $this->sendJson();
		}

		$this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/errors/'));

		return $this->view->partial('permissionDenied');
	}

	public function handleAppNotAllowedException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse($exception->getMessage(), 1);

			return $this->sendJson();
		}

		$this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/errors/'));

		return $this->view->partial('notFound');
	}

	public function handleIdNotFoundException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->addResponse($exception->getMessage(), 1);

			return $this->sendJson();
		}

		$this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/errors/'));

		return $this->view->partial('notFound');
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
}