<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Request;

class RequestService
{
	public function __construct()
	{
	}

	public function init()
	{
		return new Request;
	}
}