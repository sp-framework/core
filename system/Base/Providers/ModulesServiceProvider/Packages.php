<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Doctrine\ORM\Tools\SchemaTool;
use League\Container\Container;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Route\Router;
use System\Base\Providers\ContainerServiceProvider\Container as SystemContainer;
use System\Base\Providers\ModulesServiceProvider\Model\Packages as PackagesModel;
use System\Base\Providers\ModulesServiceProvider\ModulesInterface;
use System\Base\Providers\ModulesServiceProvider\Packages\PackagesData;

class Packages implements ModulesInterface
{
	private $container;

	protected $route;

	protected $packages = [];

	// protected $middlewares = [];

	// protected $routes = [];

	protected $db;

	protected $em;

	protected $packagesData = [];

	public function __construct(Container $container)
	{
		$this->container = $container;

		$this->route = $this->container->get(Router::class);

		$this->db = $this->container->get('db');

		$this->em = $this->container->get('em');

		$this->packagesData = new PackagesData;
	}

	public function getPackagesData()
	{
		return $this->packagesData;
	}

	public function use($packageClass)
	{
		return new $packageClass($this->container->get(SystemContainer::class));
	}

	public function getAll($criteria = [], $sort = null, $limit = null, $offset = null)
	{
		return $this->db->getByData(PackagesModel::class, $criteria, $sort, $limit, $offset);
	}

	public function getById($id)
	{
		return $this->db->getById(PackagesModel::class, $id);
	}

	public function register(array $data)
	{
		return $this->db->addToDb(PackagesModel::class, $data);
	}

	public function update(array $data)
	{
		return $this->db->updateToDbById(PackagesModel::class, $data);
	}

	public function remove($id)
	{
		//
	}

	// protected function initPackages($packages)
	// {
	// 	$packages = $this->getAllPackages();

	// 	if (count($packages) > 0 ) {
	// 		$this->initPackages($packages);
	// 	}

	// 	foreach ($packages as $key => $package) {
	// 		$apps = unserialize($package->get('apps'));
	// 		if (array_key_exists($this->container->get('apps')->getAppInfo()['id'], $apps)) {
	// 			if ($apps[$this->container->get('apps')->getAppInfo()['id']] === 1) {
	// 				$packageClass = '\\Packages\\' . str_replace('/', '\\', $package->get('path') . 'Package');
	// 				$initPackage = new $packageClass;
	// 				$this->packages[$initPackage->name] = $initPackage;
	// 				$this->addMiddleware($package->get('path'));
	// 				$this->addRoutes($package->get('path'));
	// 			}
	// 		}
	// 	}
	// }
	//
	// 	protected function addMiddleware($path)
	// {
	// 	$fileSystem = $this->initFilesystem($path);

	// 	if ($fileSystem->has('Middleware.php')) {
	// 		$middlewareClass = '\\Packages\\' . str_replace('/', '\\', $path) . 'Middleware';
	// 		array_push($this->middlewares, $middlewareClass);
	// 	}
	// }

	// protected function addRoutes($path)
	// {
	// 	$fileSystem = $this->initFilesystem($path);

	// 	if ($fileSystem->has('Routes.php')) {
	// 		$routeClass = '\\Packages\\' . str_replace('/', '\\', $path) . 'Routes';
	// 		array_push($this->routes, $routeClass);
	// 	}
	// }
	//
	// public function getMiddlewares()
	// {
	// 	return $this->middlewares;
	// }

	// public function getRoutes()
	// {
	// 	return $this->routes;
	// }

	// protected function initFilesystem($path)
	// {
	// 	return new Filesystem(new Local(base_path('packages/' . $path)));
	// }
}