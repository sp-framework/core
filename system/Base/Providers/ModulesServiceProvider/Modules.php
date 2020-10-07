<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Di\DiInterface;
use System\Base\Providers\ModulesServiceProvider\Modules\Applications;
use System\Base\Providers\ModulesServiceProvider\Modules\Components;
use System\Base\Providers\ModulesServiceProvider\Modules\Core;
use System\Base\Providers\ModulesServiceProvider\Modules\Middlewares;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages;
use System\Base\Providers\ModulesServiceProvider\Modules\Views;
use System\Base\Providers\ModulesServiceProvider\Modules\Repositories;

class Modules
{
	private $container;

	protected $core;

	protected $applications;

	protected $components;

	protected $packages;

	protected $middlewares;

	protected $views;

	protected $repositories;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;
	}

	public function __get($name)
	{
		if (!isset($this->{$name})) {
			if (method_exists($this, $method = "init{$name}")) {
				return $this->{$method}();
			}
		}

		return $this->{$name};
	}

	protected function initCore()
	{
		$this->core = (new Core($this->container))->getAllCores();

		return $this->core;
	}

	protected function initApplications()
	{
		$this->applications = (new Applications($this->container))->getAllApplications();

		return $this->applications;
	}

	protected function initComponents()
	{
		$this->components = (new Components($this->container))->getAllComponents();

		return $this->components;
	}

	protected function initPackages()
	{
		$this->packages = (new Packages($this->container))->getAllPackages();

		return $this->packages;
	}

	protected function initMiddlewares()
	{
		$this->middlewares = (new Middlewares($this->container))->getAllMiddlewares();

		return $this->middlewares;
	}

	protected function initViews()
	{
		$this->views = (new Views($this->container))->getAllViews();

		return $this->views;
	}

	protected function initRepositories()
	{
		$this->repositories = (new Repositories($this->container))->getAllRepositories();

		return $this->repositories;
	}
}