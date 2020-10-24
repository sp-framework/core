<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Response as PhalconResponse;

class Response
{
	public function __construct()
	{
	}

	public function init()
	{
		return new PhalconResponse;
	}
}