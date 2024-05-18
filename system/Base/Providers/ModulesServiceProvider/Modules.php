<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\Providers\ModulesServiceProvider\Installer;
use System\Base\Providers\ModulesServiceProvider\Manager;
use System\Base\Providers\ModulesServiceProvider\Queues;
use System\Base\Providers\ModulesServiceProvider\Modules\Bundles;
use System\Base\Providers\ModulesServiceProvider\Modules\Components;
use System\Base\Providers\ModulesServiceProvider\Modules\Middlewares;
use System\Base\Providers\ModulesServiceProvider\Modules\Packages;
use System\Base\Providers\ModulesServiceProvider\Modules\Repositories;
use System\Base\Providers\ModulesServiceProvider\Modules\Views;
use System\Base\Providers\ModulesServiceProvider\Modules\Views\ViewsSettings;

class Modules
{
	protected $components;

	protected $packages;

	protected $middlewares;

	protected $views;

	protected $viewsSettings;

	protected $repositories;

	protected $bundles;

	protected $manager;

	protected $installer;

	protected $queues;

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

	protected function initViewsSettings()
	{
		$this->viewsSettings = (new ViewsSettings())->init();

		return $this->viewsSettings;
	}

	protected function initRepositories()
	{
		$this->repositories = (new Repositories())->init();

		return $this->repositories;
	}

	protected function initBundles()
	{
		$this->bundles = (new Bundles())->init();

		return $this->bundles;
	}

	protected function initManager()
	{
		$this->manager = (new Manager())->init();

		return $this->manager;
	}

	protected function initInstaller()
	{
		$this->installer = (new Installer())->init();

		return $this->installer;
	}

	protected function initQueues()
	{
		$this->queues = (new Queues())->init();

		return $this->queues;
	}
}