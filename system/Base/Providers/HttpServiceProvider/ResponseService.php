<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Http\Response;

class ResponseService
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

	public function init()
	{
		return new Response;
	}
}