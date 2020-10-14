<?php

namespace System\Base\Providers\ContentServiceProvider\Local;

use Phalcon\Di\DiInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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
		return new Filesystem(new Local(base_path()));
	}
}