<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\Providers\ModulesServiceProvider\Installer;
use System\Base\Providers\ModulesServiceProvider\Modules\Applications;
use System\Base\Providers\ModulesServiceProvider\Modules\Components;
use System\Base\Providers\ModulesServiceProvider\Modules\Core;
use System\Base\Providers\ModulesServiceProvider\Modules\Domains;
use System\Base\Providers\ModulesServiceProvider\Modules\Menus;
use System\Base\Providers\ModulesServiceProvider\Modules\Middlewares;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages;
use System\Base\Providers\ModulesServiceProvider\Modules\Repositories;
use System\Base\Providers\ModulesServiceProvider\Modules\Views;

class Modules
{
	protected $core;

	protected $applications;

	protected $components;

	protected $packages;

	protected $middlewares;

	protected $views;

	protected $repositories;

	protected $domains;

	protected $menus;

	public function __construct()
	{
	}

	public function __get($name)
	{
		if (!isset($this->{$name})) {
			if (method_exists($this, $method = "init" . ucfirst("{$name}"))) {
				$this->{$name} = $this->{$method}();
			}
		}

		return $this->{$name};
	}

	protected function initCore()
	{
		$this->core = (new Core())->init();

		return $this->core;
	}

	protected function initApplications()
	{
		$this->applications = (new Applications())->init();

		return $this->applications;
	}

	protected function initComponents()
	{
		$this->components = (new Components())->init();

		return $this->components;
	}

	protected function initPackages()
	{
		$this->packages = (new Packages())->init();

		return $this->packages;
	}

	protected function initMiddlewares()
	{
		$this->middlewares = (new Middlewares())->init();

		return $this->middlewares;
	}

	protected function initViews()
	{
		$this->views = (new Views())->init();

		return $this->views;
	}

	protected function initRepositories()
	{
		$this->repositories = (new Repositories())->init();

		return $this->repositories;
	}

	protected function initDomains()
	{
		$this->domains = (new Domains())->init();

		return $this->domains;
	}

	protected function initMenus()
	{
		$this->menus = (new Menus())->init();

		return $this->menus;
	}

	protected function initInstaller()
	{
		$this->installer = (new Installer())->init();

		return $this->installer;
	}
}