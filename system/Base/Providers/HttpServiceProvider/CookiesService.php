<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Http\Response\Cookies;

class CookiesService
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

	public function init()
	{
		return new Cookies();
	}
}