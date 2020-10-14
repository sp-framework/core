<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Http\Request;

class RequestService
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

	public function init()
	{
		return new Request;
	}
}