<?php

namespace System\Base;

use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\Tools\SchemaTool;
use System\Base\Providers\ContainerServiceProvider\Container;
use System\Base\Providers\DatabaseServiceProvider\BaseDb;
use System\Base\Providers\ModulesServiceProvider\Packages\PackagesData;

abstract class BasePackage extends BaseDb
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
		parent::__construct($container->contents->get('em'));

		$this->mode = $container->contents->get('config')->get('base.debug');

		$this->em = $container->contents->get('em');

		$this->core = $container->contents->get('core');

		$this->repositories = $container->contents->get('repositories');

		$this->applications = $container->contents->get('applications');

		$this->components = $container->contents->get('components');

		$this->packages = $container->contents->get('packages');

		$this->middlewares = $container->contents->get('middlewares');

		$this->views = $container->contents->get('views');

		$this->packagesData = $container->contents->get('packages')->getPackagesData();
	}

	public function checkDBConnection()
	{
		try {
			$this->em->getConnection()->ping();
		} catch (ConnectionException $e) {
			//
		}

		return true;
	}

	public function getDBTables()
	{
		return $this->em->getConnection()->getSchemaManager()->listTableNames();
	}

	public function createNewSchema(array $schema)
	{
		$schemaTool = new SchemaTool($this->em);

		try {
			$schemaTool->createSchema($schema);
		} catch (ToolsException $e) {
			//
		}

		return true;
	}
}