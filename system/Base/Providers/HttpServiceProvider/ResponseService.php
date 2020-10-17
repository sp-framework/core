<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Response;

class ResponseService
{
	public function __construct()
	{
	}

	public function init()
	{
		return new Response;
	}
}