<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Di\DiInterface;
use Phalcon\Helper\Arr;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Packages as PackagesModel;;
use System\Base\Providers\ModulesServiceProvider\Packages\PackagesData;

class Packages extends BasePackage
{
	// private $container;

	// protected $application;

	protected $packages;

	// protected $db;

	// public function __construct(DiInterface $container)
	// {
	// 	$this->container = $container;

	// 	$this->db = $this->container->getShared('db');

	// 	$this->application = $this->container->getShared('applications')->getApplicationInfo();

	// 	$this->packages = $this->getAllPackages();
	// }

	public function usePackage($packageClass)
	{
		$this->application = $this->container->getShared('modules')->applications->getApplicationInfo();

		if ($this->checkPackage($packageClass)) {
			return new $packageClass($this->container);
		} else {
			throw new \Exception(
				'Package class : ' . $packageClass .
				' not available for application ' . $this->application['name']
			);
		}
	}

	protected function checkPackage($packageClass)
	{
		$packageName = Arr::last(explode('\\', $packageClass));

		$packageApplicationId =
			$this->packages[array_search($packageName, array_column($this->packages, 'name'))]['application_id'];

		if ($packageApplicationId === $this->application['id']) {
			return true;
		} else {
			return false;
		}
	}

	public function getAllPackages($conditions = null)
	{
		if (!$this->packages) {
			$this->packages = PackagesModel::find($conditions, 'packages')->toArray();
		}

		return $this;
	}
	// public function getAll($criteria = [], $sort = null, $limit = null, $offset = null)
	// {
	// 	return $this->db->getByData(PackagesModel::class, $criteria, $sort, $limit, $offset);
	// }

	// public function getById($id)
	// {
	// 	return $this->db->getById(PackagesModel::class, $id);
	// }

	// public function register(array $data)
	// {
	// 	return $this->db->addToDb(PackagesModel::class, $data);
	// }

	// public function update(array $data)
	// {
	// 	return $this->db->updateToDbById(PackagesModel::class, $data);
	// }

	// public function remove($id)
	// {
		//
	// }

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