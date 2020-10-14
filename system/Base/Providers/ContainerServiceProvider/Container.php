<?php

namespace System\Base\Providers\ContainerServiceProvider;

class Container
{
	protected $contents;

	public function __construct($container)
	{
		$this->contents = $container;
	}

	public function __get($name)
	{
		return $this->contents;
	}
}