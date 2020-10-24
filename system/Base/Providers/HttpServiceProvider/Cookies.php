<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Response\Cookies as PhalconCookies;

class Cookies
{
	public function __construct()
	{
	}

	public function init()
	{
		return new PhalconCookies();
	}
}