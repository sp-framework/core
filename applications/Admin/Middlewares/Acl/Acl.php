<?php

namespace Applications\Admin\Middlewares\Acl;

use System\Base\BaseMiddleware;

class Acl extends BaseMiddleware
{
	public function process()
	{
		var_dump('Acl');
	}
}