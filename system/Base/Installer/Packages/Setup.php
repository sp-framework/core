<?php

namespace System\Base\Installer\Packages;

use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Register\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\App\Type as RegisterAppType;
use System\Base\Installer\Packages\Setup\Register\App as RegisterApp;
use System\Base\Installer\Packages\Setup\Register\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Address\Type as RegisterAddressType;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Countries as RegisterCountries;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Account as RegisterRootAdminAccount;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Profile as RegisterRootAdminProfile;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Role as RegisterRootAdminRole;
use System\Base\Installer\Packages\Setup\Register\Modules\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Modules\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Modules\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Modules\Repository as RegisterRepository;
use System\Base\Installer\Packages\Setup\Register\Modules\View as RegisterView;
use System\Base\Installer\Packages\Setup\Schema\Core;
use System\Base\Installer\Packages\Setup\Schema\Apps\Types as AppsTypes;
use System\Base\Installer\Packages\Setup\Schema\Apps;
use System\Base\Installer\Packages\Setup\Schema\Domains;
use System\Base\Installer\Packages\Setup\Schema\Cache;
use System\Base\Installer\Packages\Setup\Schema\Logs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Address\Book as AddressBook;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Address\Types as AddressTypes;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\EmailServices;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Filters;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Countries;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Timezones;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Menus;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages\StoragesLocal;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Profiles;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Roles;
use System\Base\Installer\Packages\Setup\Schema\Modules\Components;
use System\Base\Installer\Packages\Setup\Schema\Modules\Middlewares;
use System\Base\Installer\Packages\Setup\Schema\Modules\Packages;
use System\Base\Installer\Packages\Setup\Schema\Modules\Repositories;
use System\Base\Installer\Packages\Setup\Schema\Modules\Views;
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
		$this->db->createTable('apps_types', $dbName, (new AppsTypes)->columns());
		$this->db->createTable('apps', $dbName, (new Apps)->columns());
		$this->db->createTable('domains', $dbName, (new Domains)->columns());
		$this->db->createTable('modules_components', $dbName, (new Components)->columns());
		$this->db->createTable('modules_packages', $dbName, (new Packages)->columns());
		$this->db->createTable('modules_middlewares', $dbName, (new Middlewares)->columns());
		$this->db->createTable('modules_views', $dbName, (new Views)->columns());
		$this->db->createTable('modules_repositories', $dbName, (new Repositories)->columns());
		$this->db->createTable('basepackages_cache', $dbName, (new Cache)->columns());
		$this->db->createTable('basepackages_logs', $dbName, (new Logs)->columns());
		$this->db->createTable('basepackages_email_services', $dbName, (new EmailServices)->columns());
		$this->db->createTable('basepackages_users_accounts', $dbName, (new Accounts)->columns());
		$this->db->createTable('basepackages_users_profiles', $dbName, (new Profiles)->columns());
		$this->db->createTable('basepackages_users_roles', $dbName, (new Roles)->columns());
		$this->db->createTable('basepackages_menus', $dbName, (new Menus)->columns());
		$this->db->createTable('basepackages_filters', $dbName, (new Filters)->columns());
		$this->db->createTable('basepackages_geo_countries', $dbName, (new Countries)->columns());
		$this->db->createTable('basepackages_geo_timezones', $dbName, (new Timezones)->columns());
		$this->db->createTable('basepackages_geo_states', $dbName, (new States)->columns());
		$this->db->createTable('basepackages_geo_cities', $dbName, (new Cities)->columns());
		$this->db->createTable('basepackages_address_book', $dbName, (new AddressBook)->columns());
		$this->db->createTable('basepackages_address_types', $dbName, (new AddressTypes)->columns());
		$this->registerAddressTypes();
		$this->db->createTable('basepackages_storages', $dbName, (new Storages)->columns());
		$this->db->createTable('basepackages_storages_local', $dbName, (new StoragesLocal)->columns());
	}

	public function registerRepository()
	{
		(new RegisterRepository())->register($this->db);
	}

	public function registerDomain()
	{
		return (new RegisterDomain())->register($this->db, $this->request);
	}

	public function registerCore(array $baseConfig)
	{
		$installedFiles = $this->getInstalledFiles('system/');

		array_push($installedFiles['files'], 'index.php', 'core.json');

		(new RegisterCore())->register($installedFiles, $baseConfig, $this->db);
	}

	public function registerApp()
	{
		$this->registerAppTypes();

		return $this->registerAdminApp();
	}

	protected function registerAppTypes()
	{
		return (new RegisterAppType())->register($this->db);
	}

	protected function registerAdminApp()
	{
		return (new RegisterApp())->register($this->db);
	}

	public function registerModule($type)
	{
		if ($type === 'components') {

			$adminComponents = $this->localContent->listContents('apps/Dash/Components/', true);

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

			$adminPackages = $this->localContent->listContents('apps/Dash/Packages/', true);

			foreach ($adminPackages as $adminPackageKey => $adminPackage) {
				if ($adminPackage['basename'] === 'package.json') {
					if ($adminPackage['path'] !==
						'apps/Dash/Packages/Barebone/Data/apps/Barebone/Packages/Home/Install/package.json'
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

			$commonPackages = $this->localContent->listContents('apps/Ecom/Common/Packages/', true);

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

			$adminMiddlewares = $this->localContent->listContents('apps/Dash/Middlewares/', true);

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

			$commonMiddlewares = $this->localContent->listContents('apps/Ecom/Common/Middlewares/', true);

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
					$this->localContent->read('apps/Dash/Views/Default/view.json'),
					true
				);

			if (!$jsonFile) {
				throw new \Exception('Problem reading view.json');
			}

			$this->registerAdminView($jsonFile);
		}
	}

	protected function registerAdminComponent(array $componentFile, $menuId)
	{
		$installedFiles = $this->getInstalledFiles('apps/Dash/Components/' . $componentFile['name']);

		return (new RegisterComponent())->register($this->db, $componentFile, $installedFiles, $menuId);
	}

	public function updateAdminAppComponents()
	{
		return (new RegisterApp())->update($this->db);
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
		$installedFiles = $this->getInstalledFiles('apps/Dash/Packages/' . $packageFile['name']);

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles);
	}

	public function registerAdminMiddleware(array $middlewareFile)
	{
		$installedFiles = $this->getInstalledFiles('apps/Dash/Middlewares/' . $middlewareFile['name']);

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles);
	}

	protected function registerAdminView(array $viewFile)
	{
		$appInstalledFiles = $this->getInstalledFiles('apps/Dash/Views/');
		$publicInstalledFiles = $this->getInstalledFiles('public/dash/');

		$installedFiles = array_merge($appInstalledFiles, $publicInstalledFiles);

		return (new RegisterView())->register($this->db, $viewFile, $installedFiles);
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

	public function registerAdminProfile($adminAccountId)
	{
		return (new RegisterRootAdminProfile())->register($this->db, $adminAccountId);
	}

	public function registerExcludeAutoGeneratedFilters()
	{
		return (new RegisterFilter())->register($this->db);
	}

	public function registerCountries()
	{
		return (new RegisterCountries())->register($this->db, $this->localContent);
	}

	protected function registerAddressTypes()
	{
		return (new RegisterAddressType())->register($this->db);
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