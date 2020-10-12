<?php

namespace Applications\Admin\Components;

use System\Base\BaseComponent;

class ErrorsComponent extends BaseComponent
{
	public function notfoundAction()
	{
		$this->view->pick('errors/notfound');
	}
}