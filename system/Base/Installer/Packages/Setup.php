<?php

namespace System\Base\Installer\Packages;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;
use System\Base\Installer\Packages\Setup\Schema\Repositories;
use System\Base\Installer\Packages\Setup\Schema\Core;
use System\Base\Installer\Packages\Setup\Schema\Applications;
use System\Base\Installer\Packages\Setup\Schema\Components;
use System\Base\Installer\Packages\Setup\Schema\Packages;
use System\Base\Installer\Packages\Setup\Schema\Middlewares;
use System\Base\Installer\Packages\Setup\Schema\Views;
use System\Base\Installer\Packages\Setup\Schema\Cache;
use System\Base\Installer\Packages\Setup\Schema\Logs;
use System\Base\Installer\Packages\Setup\Schema\Domains;
use System\Base\Installer\Packages\Setup\Register\Repository;
use System\Base\Installer\Packages\Setup\Register\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Application as RegisterApplication;
use System\Base\Installer\Packages\Setup\Register\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\View as RegisterView;
use System\Base\Installer\Packages\Setup\Register\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Write\Configs;
use System\Base\Installer\Packages\Setup\Write\DatabaseServiceProvider;

class Setup
{
	protected $container;

	protected $postData;

	protected $request;

	protected $db;

	protected $localContent;

	public function __construct($container)
	{
		$this->container = $container;

		$this->localContent = $this->container['localContent'];

		$this->request = $this->container->getShared('request');

		$this->postData = $this->request->getPost();

		if ($this->request->isPost()) {
			$conn =
					[
						'db' =>
							[
								'host' 		=>
									isset($this->postData['host']) ?
									$this->postData['host'] :
									'',
								'dbname' 	=>
									isset($this->postData['database_name']) ?
									$this->postData['database_name'] :
									'',
								'username'	=>
									isset($this->postData['username']) ?
									$this->postData['username'] :
									'',
								'password' 	=>
									isset($this->postData['password']) ?
									$this->postData['password'] :
									'',
								'port' 		=>
									isset($this->postData['port']) ?
									$this->postData['port'] :
									3306,
							]
					];

			$this->db = new Mysql($conn['db']);
		}
	}

	public function checkDbEmpty()
	{
		$allTables =
			$this->db->listTables($this->postData['database_name']);

		if (count($allTables) > 0) {
			if ($this->postData['drop'] === 'false') {
				return false;
			} else {
				foreach ($allTables as $tableKey => $tableValue) {
					$this->db->dropTable($tableValue);
				}
				return true;
			}
		}
		return true;
	}

	public function buildSchema()
	{
		$this->db->createTable('repositories', '', (new Repositories)->columns());
		$this->db->createTable('core', '', (new Core)->columns());
		$this->db->createTable('applications', '', (new Applications)->columns());
		$this->db->createTable('components', '', (new Components)->columns());
		$this->db->createTable('packages', '', (new Packages)->columns());
		$this->db->createTable('middlewares', '', (new Middlewares)->columns());
		$this->db->createTable('views', '', (new Views)->columns());
		$this->db->createTable('cache', '', (new Cache)->columns());
		$this->db->createTable('logs', '', (new Logs)->columns());
		$this->db->createTable('domains', '', (new Domains)->columns());
	}

	public function registerRepository()//Change this to SP
	{
		(new Repository())->register($this->db);
	}

	public function registerCore(array $coreFile)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'core.json');

		(new RegisterCore())->register($installedFiles, $coreFile, $this->db, $this->postData);
	}

	public function registerModule($type, $newApplicationId)
	{
		if ($type === 'applications') {
			return
				$this->registerAdminApplication(
					json_decode(
						$this->localContent->read('applications/Admin/application.json'),
						true
					)
				);
		} else if ($type === 'components') {

			$adminComponents = $this->localContent->listContents('applications/Admin/Components/Install/', true);

			foreach ($adminComponents as $adminComponentKey => $adminComponent) {
				if ($adminComponent['basename'] === 'component.json') {
					$this->registerAdminComponent(
						json_decode(
							$this->localContent->read($adminComponent['path']),
							true
						),
						$newApplicationId
					);
				}
			}

		} else if ($type === 'packages') {

			$adminPackages = $this->localContent->listContents('applications/Admin/Packages/Install/', true);

			foreach ($adminPackages as $adminPackageKey => $adminPackage) {
				if ($adminPackage['basename'] === 'package.json') {
					$this->registerAdminPackage(
						json_decode(
							$this->localContent->read($adminPackage['path']),
							true
						),
						$newApplicationId
					);
				}
			}
		} else if ($type === 'middlewares') {

			$adminMiddlewares =
				$this->localContent->listContents('applications/Admin/Middlewares/Install/', true);

			foreach ($adminMiddlewares as $adminMiddlewareKey => $adminMiddleware) {
				if ($adminMiddleware['basename'] === 'middleware.json') {
					$this->registerAdminMiddleware(
						json_decode(
							$this->localContent->read($adminMiddleware['path']),
							true
						),
						$newApplicationId
					);
				}
			}
		} else if ($type === 'views') {

			$this->registerAdminView(
				json_decode(
					$this->localContent->read('applications/Admin/Views/Default/view.json'),
					true
				),
				$newApplicationId
			);
		}
	}

	protected function registerAdminApplication(array $applicationFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/', false);

		return
			(new RegisterApplication())->register(
				$this->db,
				$applicationFile,
				$installedFiles,
				$this->postData['mode']
			);
	}

	protected function registerAdminComponent(array $componentFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Components/');

		return (new RegisterComponent())->register($this->db, $componentFile, $installedFiles, $newApplicationId);
	}

	protected function registerAdminPackage(array $packageFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Packages/');

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles, $newApplicationId);
	}

	public function registerAdminMiddleware(array $middlewareFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Middlewares/');

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles, $newApplicationId);
	}

	protected function registerAdminView(array $viewFile, $newApplicationId)
	{
		$applicationInstalledFiles = $this->getInstalledFiles('applications/Admin/Views/');
		$publicInstalledFiles = $this->getInstalledFiles('public/Admin/');

		$installedFiles = array_merge($applicationInstalledFiles, $publicInstalledFiles);

		return (new RegisterView())->register($this->db, $viewFile, $installedFiles, $newApplicationId);
	}

	public function registerDomain()
	{
		return (new RegisterDomain())->register($this->db, $this->request);
	}

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$contents = $this->localContent->listContents($directory, $sub);

			foreach ($contents as $contentKey => $content) {
				if ($content['type'] === 'dir') {
					array_push($installedFiles['dir'], $content['path']);
				} else if ($content['type'] === 'file') {
					array_push($installedFiles['files'], $content['path']);
				}
			}

			return $installedFiles;
		} else {
			return null;
		}
	}

	public function writeConfigs($coreJson)
	{
		(new Configs())->write($this->container, $this->postData, $coreJson);
	}

	public function removeInstaller()
	{
		// $installerContents = $this->localContent->listContents(base_path('system/Base/Installer/'));

		// foreach ($installerContents as $fileContent) {
			//Remove All Files
		// }

		// foreach ($installerContents as $dirContent) {
			//Remove All Dirs
		// }

		// (new DatabaseServiceProviderFile())->write($this->localContent);
	}
}