<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Response\Cookies;

class CookiesService
{
	public function __construct()
	{
	}

	public function init()
	{
		return new Cookies();
	}
}