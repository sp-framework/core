<?php

namespace System\Base\Installer;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;

class SetupPackage
{
	protected $container;

	protected $postData;

	protected $request;

	protected $db;

	protected $modelsManager;

	public function __construct($container)
	{
		$this->container = $container;

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

			$this->modelsManager = $this->container->getShared('modelsManager');
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
		$this->buildRepositorySchema();
		$this->buildCoreSchema();
		$this->buildApplicationsSchema();
		$this->buildComponentsSchema();
		$this->buildPackagesSchema();
		$this->buildMiddlewaresSchema();
		$this->buildViewsSchema();
		$this->buildCacheSchema();
	}

	protected function buildRepositorySchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_TINYINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'url',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'need_auth',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'username',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'token',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('repositories', '', $columns);
	}

	protected function buildCoreSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_TINYINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('core', '', $columns);
	}

	protected function buildApplicationsSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_TINYINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'route',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'dependencies',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'is_default',
					[
						'type'    => Column::TYPE_INTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
				new Column(
					'mode',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
			]
		];

		$this->db->createTable('applications', '', $columns);
	}

	protected function buildComponentsSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_SMALLINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'path',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'dependencies',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'application_id',
					[
						'type'    => Column::TYPE_INTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('components', '', $columns);
	}

	protected function buildPackagesSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_SMALLINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'path',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'dependencies',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'application_id',
					[
						'type'    => Column::TYPE_INTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('packages', '', $columns);
	}

	protected function buildMiddlewaresSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_SMALLINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'path',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'class',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'dependencies',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'application_id',
					[
						'type'    => Column::TYPE_INTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'sequence',
					[
						'type'    => Column::TYPE_INTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'enabled',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('middlewares', '', $columns);
	}

	protected function buildViewsSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_SMALLINTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => true,
					]
				),
				new Column(
					'display_name',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 50,
						'notNull' => false,
					]
				),
				new Column(
					'description',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => false,
					]
				),
				new Column(
					'version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => true,
					]
				),
				new Column(
					'repo',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'settings',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'dependencies',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => true,
					]
				),
				new Column(
					'application_id',
					[
						'type'    => Column::TYPE_INTEGER,
						'notNull' => true,
					]
				),
				new Column(
					'view_id',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'notNull' => false,
					]
				),
				new Column(
					'installed',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				),
				new Column(
					'files',
					[
						'type'    => Column::TYPE_TEXT,
						'notNull' => false,
					]
				),
				new Column(
					'update_available',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => false,
					]
				),
				new Column(
					'update_version',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 15,
						'notNull' => false,
					]
				),
			]
		];

		$this->db->createTable('views', '', $columns);
	}

	protected function buildCacheSchema()
	{
		$columns =
		[
		   'columns' => [
				new Column(
					'id',
					[
						'type'          => Column::TYPE_INTEGER,
						'notNull'       => true,
						'autoIncrement' => true,
						'primary'       => true,
					]
				),
				new Column(
					'key',
					[
						'type'    => Column::TYPE_VARCHAR,
						'size'    => 2048,
						'notNull' => true,
					]
				),
				new Column(
					'query',
					[
						'type'    => Column::TYPE_VARCHAR,
						'notNull' => true,
					]
				),
				new Column(
					'status',
					[
						'type'    => Column::TYPE_TINYINTEGER,
						'size'    => 1,
						'notNull' => true,
					]
				)
			]
		];

		$this->db->createTable('cache', '', $columns);
	}

	public function registerHWFRepository()
	{
		$this->db->insertAsDict(
			'repositories',
			[
				'name' 					=> 'Hello World Framework (h-w-f)',
				'description' 			=> 'Hello World Framework Repositories',
				'url'		 			=> 'https://api.github.com/orgs/h-w-f/repos',
				'need_auth'				=> 0,
				'username'				=> '',
				'token'					=> ''
			]
		);
	}

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dir'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$contents = $this->container['localContent']->listContents($directory, $sub);

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

	public function registerCore(array $coreFile)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'core.json');

		$this->db->insertAsDict(
			'core',
			[
				'name' 					=> $coreFile['name'],
				'display_name'			=> $coreFile['displayName'],
				'description' 			=> $coreFile['description'],
				'version'	 			=> $coreFile['version'],
				'repo'					=> $coreFile['repo'],
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function registerModule($type, $newApplicationId)
	{
		if ($type === 'applications') {
			return
				$this->registerAdminApplication(
					json_decode(
						$this->container['localContent']->read('applications/Admin/application.json'),
						true
					)
				);

		} else if ($type === 'components') {

			$adminComponents = $this->container['localContent']->listContents('applications/Admin/Components/Install/', true);

			foreach ($adminComponents as $adminComponentKey => $adminComponent) {
				if ($adminComponent['basename'] === 'component.json') {
					$this->registerAdminComponent(
						json_decode(
							$this->container['localContent']->read($adminComponent['path']),
							true
						),
						$newApplicationId
					);
				}
			}

		} else if ($type === 'packages') {

			$adminPackages = $this->container['localContent']->listContents('applications/Admin/Packages/Install/', true);

			foreach ($adminPackages as $adminPackageKey => $adminPackage) {
				if ($adminPackage['basename'] === 'package.json') {
					$this->registerAdminPackage(
						json_decode(
							$this->container['localContent']->read($adminPackage['path']),
							true
						),
						$newApplicationId
					);
				}
			}

		} else if ($type === 'middlewares') {

			$adminMiddlewares = $this->container['localContent']->listContents('applications/Admin/Middlewares/Install/', true);

			foreach ($adminMiddlewares as $adminMiddlewareKey => $adminMiddleware) {
				if ($adminMiddleware['basename'] === 'middleware.json') {
					$this->registerAdminMiddleware(
						json_decode(
							$this->container['localContent']->read($adminMiddleware['path']),
							true
						),
						$newApplicationId
					);
				}
			}
		} else if ($type === 'views') {

			$this->registerAdminView(
				json_decode(
					$this->container['localContent']->read('applications/Admin/Views/Default/view.json'),
					true
				),
				$newApplicationId
			);
		}
	}

	protected function registerAdminApplication(array $applicationFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/', false);

		$insertApplication = $this->db->insertAsDict(
			'applications',
			[
				'name' 					=> $applicationFile['name'],
				'display_name' 			=> $applicationFile['displayName'],
				'description' 			=> $applicationFile['description'],
				'version'				=> $applicationFile['version'],
				'repo'					=> $applicationFile['repo'],
				'settings'			 	=>
					isset($applicationFile['settings']) ?
					json_encode($applicationFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($applicationFile['dependencies']) ?
					json_encode($applicationFile['dependencies']) :
					null,
				'is_default'			=> 1,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles),
				'mode'					=> $this->postData['mode'] === 'true' ? 0 : 1
			]
		);

		if ($insertApplication) {
			return $this->db->lastInsertId();
		} else {
			return null;
		}
	}

	protected function registerAdminComponent(array $componentFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/components/');

		return $this->db->insertAsDict(
			'components',
			[
				'name' 					=> $componentFile['name'],
				'display_name' 			=> $componentFile['displayName'],
				'description' 			=> $componentFile['description'],
				'version'				=> $componentFile['version'],
				'path'					=> $componentFile['path'],
				'repo'					=> $componentFile['repo'],
				'settings'				=>
					isset($componentFile['settings']) ?
					json_encode($componentFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($componentFile['dependencies']) ?
					json_encode($componentFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	protected function registerAdminPackage(array $packageFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Packages/');

		return $this->db->insertAsDict(
			'packages',
			[
				'name' 					=> $packageFile['name'],
				'display_name'			=> $packageFile['displayName'],
				'description' 			=> $packageFile['description'],
				'version'				=> $packageFile['version'],
				'path'					=> $packageFile['path'],
				'repo'					=> $packageFile['repo'],
				'settings'				=>
					isset($packageFile['settings']) ?
					json_encode($packageFile['settings']) :
					null,
				'dependencies'		 	=>
					isset($packageFile['dependencies']) ?
					json_encode($packageFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function registerAdminMiddleware(array $middlewareFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Middlewares/');

		return $this->db->insertAsDict(
			'middlewares',
			[
				'name' 					=> $middlewareFile['name'],
				'display_name' 			=> $middlewareFile['displayName'],
				'description' 			=> $middlewareFile['description'],
				'version'				=> $middlewareFile['version'],
				'repo'		 			=> $middlewareFile['repo'],
				'path'					=> $middlewareFile['path'],
				'class'					=> $middlewareFile['class'],
				'settings'				=>
					isset($middlewareFile['settings']) ?
					json_encode($middlewareFile['settings']) :
					null,
				'dependencies'			=>
					isset($middlewareFile['dependencies']) ?
					json_encode($middlewareFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'sequence'				=> 0,
				'enabled'				=> 0,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	protected function registerAdminView(array $viewFile, $newApplicationId)
	{
		$applicationInstalledFiles = $this->getInstalledFiles('applications/Admin/Views/');
		$publicInstalledFiles = $this->getInstalledFiles('public/Admin/');

		$installedFiles = array_merge($applicationInstalledFiles, $publicInstalledFiles);

		return $this->db->insertAsDict(
			'views',
			[
				'name' 					=> $viewFile['name'],
				'display_name' 			=> $viewFile['displayName'],
				'description' 			=> $viewFile['description'],
				'version'				=> $viewFile['version'],
				'repo'		 			=> $viewFile['repo'],
				'settings'				=>
					isset($viewFile['settings']) ?
					json_encode($viewFile['settings']) :
					null,
				'dependencies'			=>
					isset($viewFile['dependencies']) ?
					json_encode($viewFile['dependencies']) :
					null,
				'application_id'		=> $newApplicationId,
				'installed'				=> 1,
				'files'					=> json_encode($installedFiles)
			]
		);
	}

	public function writeDbConfig()
	{
		$configContent =
'<?php

return
	[
		"db" =>
			[
				"host" 		=> "' . $this->postData['host'] . '",
				"dbname" 	=> "' . $this->postData['database_name'] . '",
				"username" 	=> "' . $this->postData['username'] . '",
				"password" 	=> "' . $this->postData['password'] . '",
				"port" 		=> "' . $this->postData['port'] . '",
			]
	];';

		$this->container['localContent']->put('/system/Configs/Db.php', $configContent);

		if ($this->postData['mode'] === 'production') {
			$debug = "false";
			$cache = "true";
		} else if ($this->postData['mode'] === 'development') {
			$debug = "true";
			$cache = "false";
		}

		$baseContent =
'<?php

return
	[
		"debug"					=> ' . $debug . ', //true - Development false - Production
		"cache"					=> ' . $cache . ', //Global Cache value //true - Production false - Development
		"cacheTimeout"			=> 60, //Global Cache timeout in seconds
		"cacheService"			=> "streamCache"
	];';

		$this->container['localContent']->put('/system/Configs/Base.php', $baseContent);
	}

	public function removeSetup()
	{
		//If production delete setup file and rewrite DatabaseServiceProvider/Db File.
		//Do the same when updating core via modules update
		// if ($this->postData['mode'] === 'production') {
		// 	$this->container['localContent']->delete(base_path('system/Base/Installer/Setup.php'));
		// 	$this->container['localContent']->delete(base_path('system/Base/Installer/SetupPackage.php'));
		// 	$this->redoDatabaseServiceProviderFile();
		// }
	}

	protected function redoDatabaseServiceProviderFile()
	{
		$databaseServiceProviderContent =
'<?php

namespace System\Base\Providers\DatabaseServiceProvider;

use Phalcon\Db\Adapter\PdoFactory;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Di\DiInterface;
use System\Base\Installer\Setup;

class Db
{
	private $container;

	public function __construct(DiInterface $container)
	{
		$this->container = $container;

		$this->dbConfig = $container->getShared("config")->db;
	}

	public function getPdo()
	{
		return new Mysql($this->dbConfig->toArray());
	}
}';

		$this->container['localContent']->put('/system/Base/Providers/DatabaseServiceProvider/Db.php', $databaseServiceProviderContent);
	}
}