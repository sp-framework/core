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