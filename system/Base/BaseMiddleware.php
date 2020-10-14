<?php

namespace System\Base;

use System\Base\Providers\ContainerServiceProvider\Container;

abstract class BaseMiddleware
{
	private $em;

	protected $core;

	protected $applications;

	protected $components;

	protected $packages;

	protected $views;

	protected $middlewares;

	protected $packagesData;

	public $mode;

	public function __construct(Container $container)
	{
		$this->mode = $container->contents->get('config')->get('base.debug');

		$this->applications = $container->contents->get('applications');

		$this->components = $container->contents->get('components');

		$this->packages = $container->contents->get('packages');

		$this->middlewares = $container->contents->get('middlewares');

		$this->views = $container->contents->get('views');
	}
}