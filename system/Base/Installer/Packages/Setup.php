<?php

namespace System\Base\Installer\Packages;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Register\Application as RegisterApplication;
use System\Base\Installer\Packages\Setup\Register\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\CountriesStatesCities;
use System\Base\Installer\Packages\Setup\Register\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Register\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Repository as RegisterRepository;
use System\Base\Installer\Packages\Setup\Register\Role as RegisterRootAdminRole;
use System\Base\Installer\Packages\Setup\Register\User as RegisterUser;
use System\Base\Installer\Packages\Setup\Register\View as RegisterView;
use System\Base\Installer\Packages\Setup\Schema\Applications;
use System\Base\Installer\Packages\Setup\Schema\Cache;
use System\Base\Installer\Packages\Setup\Schema\Components;
use System\Base\Installer\Packages\Setup\Schema\Core;
use System\Base\Installer\Packages\Setup\Schema\Domains;
use System\Base\Installer\Packages\Setup\Schema\EmailServices;
use System\Base\Installer\Packages\Setup\Schema\Filters;
use System\Base\Installer\Packages\Setup\Schema\Geo\Cities;
use System\Base\Installer\Packages\Setup\Schema\Geo\Countries;
use System\Base\Installer\Packages\Setup\Schema\Geo\States;
use System\Base\Installer\Packages\Setup\Schema\Logs;
use System\Base\Installer\Packages\Setup\Schema\Menus;
use System\Base\Installer\Packages\Setup\Schema\Middlewares;
use System\Base\Installer\Packages\Setup\Schema\Packages;
use System\Base\Installer\Packages\Setup\Schema\Repositories;
use System\Base\Installer\Packages\Setup\Schema\Roles;
use System\Base\Installer\Packages\Setup\Schema\Users;
use System\Base\Installer\Packages\Setup\Schema\Views;
use System\Base\Installer\Packages\Setup\Write\Configs;
use System\Base\Installer\Packages\Setup\Write\Pdo;

class Setup
{
	protected $container;

	protected $postData;

	protected $request;

	protected $db;

	protected $dbConfig;

	protected $localContent;

	public function __construct($container)
	{
		$this->container = $container;

		$this->localContent = $this->container['localContent'];

		$this->request = $this->container->getShared('request');

		$this->postData = $this->request->getPost();

		$this->validation = $this->container->getShared('validation');

		$this->security = $this->container->getShared('security');

		$this->configs = new Configs();

		if ($this->request->isPost()) {
			$this->dbConfig =
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

			$this->db = new Mysql($this->dbConfig['db']);
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
		$dbName = $this->dbConfig['db']['dbname'];

		$this->db->createTable('repositories', $dbName, (new Repositories)->columns());
		$this->db->createTable('core', $dbName, (new Core)->columns());
		$this->db->createTable('applications', $dbName, (new Applications)->columns(),);
		// $applicationsRouteUnique = new Index(
		// 	'column_UNIQUE',
		// 	[
		// 		'route',
		// 	],
		// 	'UNIQUE'
		// );
		// $this->db->addIndex('applications', $this->dbConfig['db']['dbname'], $applicationsRouteUnique);
		$this->db->createTable('components', $dbName, (new Components)->columns());
		$this->db->createTable('packages', $dbName, (new Packages)->columns());
		$this->db->createTable('middlewares', $dbName, (new Middlewares)->columns());
		$this->db->createTable('views', $dbName, (new Views)->columns());
		$this->db->createTable('cache', $dbName, (new Cache)->columns());
		$this->db->createTable('logs', $dbName, (new Logs)->columns());
		$this->db->createTable('email_services', $dbName, (new EmailServices)->columns());
		$this->db->createTable('domains', $dbName, (new Domains)->columns());
		// $domainUnique = new Index(
		// 	'column_UNIQUE',
		// 	[
		// 		'name',
		// 	],
		// 	'UNIQUE'
		// );
		// $this->db->addIndex('domains', $this->dbConfig['db']['dbname'], $domainUnique);
		$this->db->createTable('menus', $dbName, (new Menus)->columns());
		$this->db->createTable('filters', $dbName, (new Filters)->columns());
		$this->db->createTable('users', $dbName, (new Users)->columns());
		// $emailUnique = new Index(
		// 	'column_UNIQUE',
		// 	[
		// 		'email',
		// 	],
		// 	'UNIQUE'
		// );
		// $this->db->addIndex('users', $this->dbConfig['db']['dbname'], $emailUnique);
		$this->db->createTable('roles', $dbName, (new Roles)->columns());
		// $roleNameUnique = new Index(
		// 	'column_UNIQUE',
		// 	[
		// 		'name',
		// 	],
		// 	'UNIQUE'
		// );
		// $this->db->addIndex('roles', $this->dbConfig['db']['dbname'], $roleNameUnique);
		$this->db->createTable('geo_countries', $dbName, (new Countries)->columns());
		// $countriesNameUnique = new Index(
		// 	'column_UNIQUE',
		// 	[
		// 		'name',
		// 	],
		// 	'UNIQUE'
		// );
		// $this->db->addIndex('geo_countries', $this->dbConfig['db']['dbname'], $countriesNameUnique);
		$this->db->createTable('geo_states', $dbName, (new States)->columns());
		$this->db->createTable('geo_cities', $dbName, (new Cities)->columns());
	}

	public function registerRepository()//Change this to SP
	{
		(new RegisterRepository())->register($this->db);
	}

	public function registerCore(array $baseConfig)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'core.json');

		(new RegisterCore())->register($installedFiles, $baseConfig, $this->db);
	}

	public function registerModule($type, $newApplicationId)
	{

		if ($type === 'applications') {
			$jsonFile =
				json_decode(
					$this->localContent->read('applications/Admin/application.json'),
					true
				);

			if (!$jsonFile) {
				throw new \Exception('Problem reading application.json');
			}

			return $this->registerAdminApplication($jsonFile);
		} else if ($type === 'components') {

			$homeComponentId = null;

			$adminComponents = $this->localContent->listContents('applications/Admin/Components/', true);

			foreach ($adminComponents as $adminComponentKey => $adminComponent) {
				if ($adminComponent['basename'] === 'component.json') {
					$jsonFile =
						json_decode(
							$this->localContent->read($adminComponent['path']),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading component.json at location ' . $adminComponent['path']);
					}

					if ($jsonFile['menu']) {
						$menuId = $this->registerAdminMenu($jsonFile['menu'], $newApplicationId);
					} else {
						$menuId = null;
					}

					$component = $this->registerAdminComponent($jsonFile, $newApplicationId, $menuId);

					if ($component) {
						$homeComponentId = $component;
					}
				}
			}

			return $homeComponentId;

		} else if ($type === 'packages') {

			$adminPackages = $this->localContent->listContents('applications/Admin/Packages/', true);

			foreach ($adminPackages as $adminPackageKey => $adminPackage) {
				if ($adminPackage['basename'] === 'package.json') {
					if ($adminPackage['path'] !==
						'applications/Admin/Packages/Barebone/Data/applications/Barebone/Packages/Home/Install/package.json'
					) {
						$jsonFile =
							json_decode(
								$this->localContent->read($adminPackage['path']),
								true
							);

						if (!$jsonFile) {
							throw new \Exception('Problem reading package.json at location ' . $adminPackage['path']);
						}

						$this->registerAdminPackage($jsonFile, $newApplicationId);
					}
				}
			}
		} else if ($type === 'middlewares') {

			$adminMiddlewares =
				$this->localContent->listContents('applications/Admin/Middlewares/', true);

			foreach ($adminMiddlewares as $adminMiddlewareKey => $adminMiddleware) {
				if ($adminMiddleware['basename'] === 'middleware.json') {
					$jsonFile =
						json_decode(
							$this->localContent->read($adminMiddleware['path']),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading middleware.json at location ' . $adminMiddleware['path']);
					}

					$this->registerAdminMiddleware($jsonFile, $newApplicationId);
				}
			}
		} else if ($type === 'views') {
			$jsonFile =
				json_decode(
					$this->localContent->read('applications/Admin/Views/Default/view.json'),
					true
				);

			if (!$jsonFile) {
				throw new \Exception('Problem reading view.json');
			}

			$this->registerAdminView($jsonFile, $newApplicationId);
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

	protected function registerAdminComponent(array $componentFile, $newApplicationId, $menuId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Components/' . $componentFile['name']);

		return (new RegisterComponent())->register($this->db, $componentFile, $installedFiles, $newApplicationId, $menuId);
	}

	protected function registerAdminMenu(array $menu, $newApplicationId)
	{
		if (isset($menu['seq'])) {
			$sequence = $menu['seq'];
			unset($menu['seq']);
		} else {
			$sequence = 99;
		}

		return (new RegisterMenu())->register($this->db, $menu, $newApplicationId, $sequence);
	}

	protected function registerAdminPackage(array $packageFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Packages/' . $packageFile['name']);

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles, $newApplicationId);
	}

	public function registerAdminMiddleware(array $middlewareFile, $newApplicationId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Admin/Middlewares/' . $middlewareFile['name']);

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles, $newApplicationId);
	}

	protected function registerAdminView(array $viewFile, $newApplicationId)
	{
		$applicationInstalledFiles = $this->getInstalledFiles('applications/Admin/Views/');
		$publicInstalledFiles = $this->getInstalledFiles('public/Admin/');

		$installedFiles = array_merge($applicationInstalledFiles, $publicInstalledFiles);

		return (new RegisterView())->register($this->db, $viewFile, $installedFiles, $newApplicationId);
	}

	public function registerDomain($homeComponentId = null)
	{
		return (new RegisterDomain())->register($this->db, $this->request, $homeComponentId);
	}

	public function validateData()
	{
		$this->validation->add('email', Email::class, ["message" => "Please enter valid email address."]);
		$this->validation->add('pass', PresenceOf::class, ["message" => "Please enter a password."]);

		$validated = $this->validation->validate($this->postData)->jsonSerialize();

		if (count($validated) > 0) {
			$messages = 'Error: ';

			foreach ($validated as $key => $value) {
				$messages .= $value['message'] . ' ';
			}
			return $messages;
		} else {
			return true;
		}
	}

	public function registerRootAdminRole()
	{
		return (new RegisterRootAdminRole())->register($this->db);
	}

	public function registerAdminUser($newApplicationId, $adminRoleId)
	{
		$password = $this->security->hash($this->postData['pass']);

		return (new RegisterUser())->register($this->db, $this->postData['email'], $password, $newApplicationId, $adminRoleId);
	}

	public function registerCountryStatesCities()
	{
		return (new CountriesStatesCities())->register($this->db, $this->postData['country'], $this->localContent);
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
		return $this->configs->write($this->container, $this->postData, $coreJson);
	}

	public function revertBaseConfig($coreJson)
	{
		$this->configs->revert($this->container, $this->postData, $coreJson);
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

		// (new Pdo())->write($this->localContent);
	}
}