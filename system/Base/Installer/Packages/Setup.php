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
use System\Base\Installer\Packages\Setup\Register\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Repository as RegisterRepository;
use System\Base\Installer\Packages\Setup\Register\User\Account as RegisterRootAdminAccount;
use System\Base\Installer\Packages\Setup\Register\User\Role as RegisterRootAdminRole;
use System\Base\Installer\Packages\Setup\Register\View as RegisterView;
use System\Base\Installer\Packages\Setup\Schema\Addressbook;
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
use System\Base\Installer\Packages\Setup\Schema\Storages;
use System\Base\Installer\Packages\Setup\Schema\StoragesLocal;
use System\Base\Installer\Packages\Setup\Schema\Users\Accounts;
use System\Base\Installer\Packages\Setup\Schema\Users\Roles;
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

		$this->db->createTable('core', $dbName, (new Core)->columns());
		$this->db->createTable('applications', $dbName, (new Applications)->columns(),);
		$this->db->createTable('components', $dbName, (new Components)->columns());
		$this->db->createTable('packages', $dbName, (new Packages)->columns());
		$this->db->createTable('middlewares', $dbName, (new Middlewares)->columns());
		$this->db->createTable('views', $dbName, (new Views)->columns());
		$this->db->createTable('repositories', $dbName, (new Repositories)->columns());
		$this->db->createTable('cache', $dbName, (new Cache)->columns());
		$this->db->createTable('logs', $dbName, (new Logs)->columns());
		$this->db->createTable('email_services', $dbName, (new EmailServices)->columns());
		$this->db->createTable('domains', $dbName, (new Domains)->columns());
		$this->db->createTable('menus', $dbName, (new Menus)->columns());
		$this->db->createTable('filters', $dbName, (new Filters)->columns());
		$this->db->createTable('accounts', $dbName, (new Accounts)->columns());
		$this->db->createTable('roles', $dbName, (new Roles)->columns());
		$this->db->createTable('geo_countries', $dbName, (new Countries)->columns());
		$this->db->createTable('geo_states', $dbName, (new States)->columns());
		$this->db->createTable('geo_cities', $dbName, (new Cities)->columns());
		$this->db->createTable('addressbook', $dbName, (new Addressbook)->columns());
		$this->db->createTable('storages', $dbName, (new Storages)->columns());
		$this->db->createTable('storages_local', $dbName, (new StoragesLocal)->columns());
	}

	public function registerRepository()
	{
		(new RegisterRepository())->register($this->db);
	}

	public function registerCore(array $baseConfig)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'core.json');

		(new RegisterCore())->register($installedFiles, $baseConfig, $this->db);
	}

	public function registerModule($type)
	{
		if ($type === 'applications') {

			return $this->registerAdminApplication();

		} else if ($type === 'components') {

			$adminComponents = $this->localContent->listContents('applications/Ecom/Admin/Components/', true);

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
						$menuId = $this->registerAdminMenu($jsonFile['menu']);
					} else {
						$menuId = null;
					}

					$this->registerAdminComponent($jsonFile, $menuId);
				}
			}
		} else if ($type === 'packages') {

			$adminPackages = $this->localContent->listContents('applications/Ecom/Admin/Packages/', true);

			foreach ($adminPackages as $adminPackageKey => $adminPackage) {
				if ($adminPackage['basename'] === 'package.json') {
					if ($adminPackage['path'] !==
						'applications/Ecom/Admin/Packages/Barebone/Data/applications/Barebone/Packages/Home/Install/package.json'
					) {
						$jsonFile =
							json_decode(
								$this->localContent->read($adminPackage['path']),
								true
							);

						if (!$jsonFile) {
							throw new \Exception('Problem reading package.json at location ' . $adminPackage['path']);
						}

						$this->registerAdminPackage($jsonFile);
					}
				}
			}

			$commonPackages = $this->localContent->listContents('applications/Ecom/Common/Packages/', true);

			foreach ($commonPackages as $commonPackageKey => $commonPackage) {
				if ($commonPackage['basename'] === 'package.json') {
					$jsonFile =
						json_decode(
							$this->localContent->read($commonPackage['path']),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading package.json at location ' . $commonPackage['path']);
					}

					$this->registerCommonPackage($jsonFile);
				}
			}
		} else if ($type === 'middlewares') {

			$adminMiddlewares = $this->localContent->listContents('applications/Ecom/Admin/Middlewares/', true);

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

					$this->registerAdminMiddleware($jsonFile);
				}
			}

			$commonMiddlewares = $this->localContent->listContents('applications/Ecom/Common/Middlewares/', true);

			foreach ($commonMiddlewares as $commonMiddlewareKey => $commonMiddleware) {
				if ($commonMiddleware['basename'] === 'middleware.json') {
					$jsonFile =
						json_decode(
							$this->localContent->read($commonMiddleware['path']),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading middleware.json at location ' . $commonMiddleware['path']);
					}

					$this->registerCommonMiddleware($jsonFile);
				}
			}
		} else if ($type === 'views') {
			$jsonFile =
				json_decode(
					$this->localContent->read('applications/Ecom/Admin/Views/Default/view.json'),
					true
				);

			if (!$jsonFile) {
				throw new \Exception('Problem reading view.json');
			}

			$this->registerAdminView($jsonFile);
		}
	}

	protected function registerAdminApplication()
	{
		return (new RegisterApplication())->register($this->db);
	}

	public function updateAdminApplicationComponents()
	{
		return (new RegisterApplication())->update($this->db);
	}

	protected function registerAdminComponent(array $componentFile, $menuId)
	{
		$installedFiles = $this->getInstalledFiles('applications/Ecom/Admin/Components/' . $componentFile['name']);

		return (new RegisterComponent())->register($this->db, $componentFile, $installedFiles, $menuId);
	}

	protected function registerAdminMenu(array $menu)
	{
		if (isset($menu['seq'])) {
			$sequence = $menu['seq'];
			unset($menu['seq']);
		} else {
			$sequence = 99;
		}

		return (new RegisterMenu())->register($this->db, $menu, $sequence);
	}

	protected function registerAdminPackage(array $packageFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Ecom/Admin/Packages/' . $packageFile['name']);

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles);
	}

	protected function registerCommonPackage(array $packageFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Ecom/Common/Packages/' . $packageFile['name']);

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles);
	}

	public function registerAdminMiddleware(array $middlewareFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Ecom/Admin/Middlewares/' . $middlewareFile['name']);

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles);
	}

	public function registerCommonMiddleware(array $middlewareFile)
	{
		$installedFiles = $this->getInstalledFiles('applications/Ecom/Common/Middlewares/' . $middlewareFile['name']);

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles);
	}

	protected function registerAdminView(array $viewFile)
	{
		$applicationInstalledFiles = $this->getInstalledFiles('applications/Ecom/Admin/Views/');
		$publicInstalledFiles = $this->getInstalledFiles('public/Admin/');

		$installedFiles = array_merge($applicationInstalledFiles, $publicInstalledFiles);

		return (new RegisterView())->register($this->db, $viewFile, $installedFiles);
	}

	public function registerDomain()
	{
		return (new RegisterDomain())->register($this->db, $this->request);
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

	public function registerAdminAccount($adminRoleId, $workFactor = 12)
	{
		$password = $this->container['secTools']->hashPassword($this->postData['pass'], $workFactor);

		return (new RegisterRootAdminAccount())->register($this->db, $this->postData['email'], $password, $adminRoleId);
	}

	public function registerExcludeAutoGeneratedFilters()
	{
		return (new RegisterFilter())->register($this->db);
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
		return (new Configs($this->container, $this->postData, $coreJson))->write();
	}

	public function revertBaseConfig($coreJson)
	{
		return (new Configs($this->container, $this->postData, $coreJson))->revert();
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