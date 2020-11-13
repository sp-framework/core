<?php

namespace Applications\Admin\Middlewares\Modules;

use System\Base\BaseMiddleware;

class Modules extends BaseMiddleware
{
	public function process()
	{
		var_dump('modules');
	}
}