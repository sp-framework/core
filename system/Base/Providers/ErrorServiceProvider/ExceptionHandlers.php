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

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Permission Denied!';

			return $this->sendJson();
		}

		$this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/errors/'));

		return $this->view->partial('permissionDenied');
	}

	public function handleApplicationNotAllowedException($exception)
	{
		if ($this->request->getBestAccept() === 'application/json') {

			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Not Found!';

			return $this->sendJson();
		}

		$this->view->setViewsDir(base_path('system/Base/Providers/ErrorServiceProvider/View/errors/'));

		return $this->view->partial('notFound');
	}

	public function handleValidationException()
	{
		$this->session->set([
			'errors' => $e->getErrors(),
			'old' => $e->getOldInput(),
		]);

		return redirect($e->getPath());
	}

	public function handleCsrfTokenException()
	{
		$this->flash->now('warning', 'Session expired, please login again.');

		return redirect('/auth/login');
	}
}