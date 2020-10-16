<?php

namespace System\Base\Exceptions;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
	public function notfoundAction()
	{
		$this->view->setViewsDir(base_path('system/Base/Exceptions/Error/'));

		$this->view->pick('notfound');
	}
}