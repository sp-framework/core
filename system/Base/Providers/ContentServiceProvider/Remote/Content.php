<?php

namespace System\Base\Providers\ContentServiceProvider\Remote;

use GuzzleHttp\Client;
use Phalcon\Di\DiInterface;

class Content
{
	private $container;

	public function __construct(DiInterface $container)
	{
		include (__DIR__ . '/vendor/autoload.php');

		$this->container = $container;
	}

	public function init()
	{
		return new Client();
	}
}