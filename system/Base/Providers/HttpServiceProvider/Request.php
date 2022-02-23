<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Request as PhalconRequest;

class Request
{
	public function __construct()
	{
	}

	public function init()
	{
		return new PhalconRequest;
	}
}