<?php

namespace System\Base\Installer\Packages;

use Apps\Dash\Packages\Devtools\Api\Contracts\Install\Schema\DevtoolsApiContracts;
use Apps\Dash\Packages\Devtools\Api\Enums\Install\Schema\DevtoolsApiEnums;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApi;
use Apps\Dash\Packages\System\Api\Install\Schema\SystemApiCalls;
use Apps\Dash\Packages\System\Messenger\Install\Schema\SystemMessenger;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Register\App as RegisterApp;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Countries as RegisterCountries;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Timezones as RegisterTimezones;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Storages\Storages as RegisterStorages;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Account as RegisterRootAdminAccount;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Profile as RegisterRootAdminProfile;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Role as RegisterRole;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Schedules as RegisterSchedules;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Tasks as RegisterTasks;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Workers as RegisterWorkers;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Dashboard as RegisterAdminDashboard;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Widgets as RegisterAdminWidgets;
use System\Base\Installer\Packages\Setup\Register\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Register\Modules\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Modules\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Modules\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Modules\Repository as RegisterRepository;
use System\Base\Installer\Packages\Setup\Register\Modules\View as RegisterView;
use System\Base\Installer\Packages\Setup\Schema\Apps;
use System\Base\Installer\Packages\Setup\Schema\Apps\IpFilter;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ActivityLogs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\AddressBook;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards\Widgets as DashboardsWidgets;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\EmailQueue;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\EmailServices;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Filters;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Cities;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Countries;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\States;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Timezones;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ImportExport;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Menus;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notes;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notifications;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Settings;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages\StoragesLocal;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Templates;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Agents;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\CanLogin;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Identifiers;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Security;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Sessions;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts\Tunnels;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Profiles;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Roles;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Widgets;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Jobs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Schedules;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Tasks;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers\Workers;
use System\Base\Installer\Packages\Setup\Schema\Cache;
use System\Base\Installer\Packages\Setup\Schema\Core;
use System\Base\Installer\Packages\Setup\Schema\Domains;
use System\Base\Installer\Packages\Setup\Schema\Logs;
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

	protected $session;

	protected $db;

	protected $dbConfig;

	protected $localContent;

	public function __construct($container, $postData)
	{
		$this->container = $container;

		$this->localContent = $this->container['localContent'];

		$this->request = $this->container->getShared('request');

		$this->session = $this->container->getShared('session');

		$this->postData = $postData;

		$this->validation = $this->container->getShared('validation');

		$this->security = $this->container->getShared('security');

		$this->cookies = $this->container->getShared('cookies');

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
			if (isset($this->postData['create-username']) && isset($this->postData['create-password'])) {
				$this->dbConfig['db']['username'] = $this->postData['create-username'];
				$this->dbConfig['db']['password'] = $this->postData['create-password'];
				$this->dbConfig['db']['dbname'] = 'mysql';
			}

			$this->db = new Mysql($this->dbConfig['db']);
		}
	}

	public function cleanVar()
	{
		$files = $this->getInstalledFiles('var/');

		foreach ($files['files'] as $key => $file) {
			try {
				$this->localContent->delete($file);
			} catch (FilesystemException | UnableToDeleteFile $exception) {
				throw $exception;
			}
		}
	}

	public function cleanOldCookies()
	{
		$cookieKey = 'Bazaari';

		//Set cookies to 1 second so browser removes them.
		$this->cookies->set(
			$cookieKey,
			'0',
			1,
			'/',
			null,
			null,
			true
		);

		$this->cookies->get($cookieKey)->setOptions(['samesite'=>'strict']);

		$this->cookies->set(
			'id',
			'0',
			1,
			'/',
			null,
			null,
			true
		);

		$this->cookies->send();
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
		$this->db->createTable('apps', $dbName, (new Apps)->columns());
		$this->db->createTable('apps_ip_filter', $dbName, (new IpFilter)->columns());
		$this->db->createTable('domains', $dbName, (new Domains)->columns());
		$this->db->createTable('modules_components', $dbName, (new Components)->columns());
		$this->db->createTable('modules_packages', $dbName, (new Packages)->columns());
		$this->db->createTable('modules_middlewares', $dbName, (new Middlewares)->columns());
		$this->db->createTable('modules_views', $dbName, (new Views)->columns());
		$this->db->createTable('modules_repositories', $dbName, (new Repositories)->columns());
		$this->db->createTable('basepackages_cache', $dbName, (new Cache)->columns());
		$this->db->createTable('basepackages_logs', $dbName, (new Logs)->columns());
		$this->db->createTable('basepackages_email_services', $dbName, (new EmailServices)->columns());
		$this->db->createTable('basepackages_email_queue', $dbName, (new EmailQueue)->columns());
		$this->db->createTable('basepackages_users_accounts', $dbName, (new Accounts)->columns());
		$this->db->createTable('basepackages_users_accounts_security', $dbName, (new Security)->columns());
		$this->db->createTable('basepackages_users_accounts_canlogin', $dbName, (new CanLogin)->columns());
		$this->db->createTable('basepackages_users_accounts_sessions', $dbName, (new Sessions)->columns());
		$this->db->createTable('basepackages_users_accounts_identifiers', $dbName, (new Identifiers)->columns());
		$this->db->createTable('basepackages_users_accounts_agents', $dbName, (new Agents)->columns());
		$this->db->createTable('basepackages_users_accounts_tunnels', $dbName, (new Tunnels)->columns());
		$this->db->createTable('basepackages_users_profiles', $dbName, (new Profiles)->columns());
		$this->db->createTable('basepackages_users_roles', $dbName, (new Roles)->columns());
		$this->db->createTable('basepackages_menus', $dbName, (new Menus)->columns());
		$this->db->createTable('basepackages_filters', $dbName, (new Filters)->columns());
		$this->db->createTable('basepackages_geo_countries', $dbName, (new Countries)->columns());
		$this->addIndex('basepackages_geo_countries', (new Countries)->indexes());
		$this->db->createTable('basepackages_geo_states', $dbName, (new States)->columns());
		$this->addIndex('basepackages_geo_states', (new States)->indexes());
		$this->db->createTable('basepackages_geo_cities', $dbName, (new Cities)->columns());
		$this->addIndex('basepackages_geo_cities', (new Cities)->indexes());
		$this->db->createTable('basepackages_geo_timezones', $dbName, (new Timezones)->columns());
		// $this->db->createTable('basepackages_geo_ipaddresses', $dbName, (new IpAddresses)->columns());
		$this->db->createTable('basepackages_address_book', $dbName, (new AddressBook)->columns());
		$this->db->createTable('basepackages_storages', $dbName, (new Storages)->columns());
		$this->db->createTable('basepackages_storages_local', $dbName, (new StoragesLocal)->columns());
		$this->db->createTable('basepackages_activity_logs', $dbName, (new ActivityLogs)->columns());
		$this->db->createTable('basepackages_notes', $dbName, (new Notes)->columns());
		$this->db->createTable('basepackages_notifications', $dbName, (new Notifications)->columns());
		$this->db->createTable('basepackages_workers_workers', $dbName, (new Workers)->columns());
		$this->db->createTable('basepackages_workers_schedules', $dbName, (new Schedules)->columns());
		$this->db->createTable('basepackages_workers_tasks', $dbName, (new Tasks)->columns());
		$this->db->createTable('basepackages_workers_jobs', $dbName, (new Jobs)->columns());
		$this->db->createTable('basepackages_import_export', $dbName, (new ImportExport)->columns());
		$this->db->createTable('basepackages_templates', $dbName, (new Templates)->columns());
		$this->db->createTable('basepackages_dashboards', $dbName, (new Dashboards)->columns());
		$this->db->createTable('basepackages_dashboards_widgets', $dbName, (new DashboardsWidgets)->columns());
		$this->db->createTable('basepackages_widgets', $dbName, (new Widgets)->columns());
		$this->db->createTable('system_api_calls', $dbName, (new SystemApiCalls)->columns());
		$this->db->createTable('system_api', $dbName, (new SystemApi)->columns());
		$this->db->createTable('system_messenger', $dbName, (new SystemMessenger)->columns());

		if ($this->postData['development-tools'] == 'true') {
			$this->db->createTable('devtools_api_contracts', $dbName, (new DevtoolsApiContracts)->columns());
			$this->db->createTable('devtools_api_enums', $dbName, (new DevtoolsApiEnums)->columns());
		}
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
		$installedFiles = [];

		$installedFiles =
			array_merge_recursive($this->getInstalledFiles('system/Base', true), $this->getInstalledFiles('system/Configs', true));

		array_push($installedFiles['files'], 'index.php', 'core.json', 'system/bootstrap.php');

		(new RegisterCore())->register($installedFiles, $baseConfig, $this->db);
	}

	public function registerApp()
	{
		return $this->registerAdminApp();
	}

	protected function registerAdminApp()
	{
		return (new RegisterApp())->register($this->db);
	}

	public function registerModule($type)
	{
		if ($type === 'components') {

			$adminComponents = $this->getInstalledFiles('apps/Dash/Components/', true);

			if (!$adminComponents || count($adminComponents) === 0) {
				return false;
			}

			foreach ($adminComponents['files'] as $adminComponentKey => $adminComponent) {
				if (strpos($adminComponent, 'component.json')) {
					$jsonFile =
						json_decode(
							$this->localContent->read($adminComponent),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading component.json at location ' . $adminComponent);
					}

					if ($jsonFile['sub_category'] === 'devtools' &&
						$this->postData['development-tools'] == 'false'
					) {
						continue;
					}

					if ($jsonFile['menu'] && $jsonFile['menu'] !== 'false') {
						$menuId = $this->registerAdminMenu($jsonFile['app_type'], $jsonFile['menu']);
					} else {
						$menuId = null;
					}

					$registeredComponentId = $this->registerAdminComponent($jsonFile, $menuId);

					if ($jsonFile['route'] === 'dashboards') {
						$this->registerAdminDashboard($jsonFile);
					}

					if (isset($jsonFile['widgets']) && count($jsonFile['widgets']) > 0) {
						$this->registerAdminWidgets($jsonFile, $registeredComponentId, $adminComponent);
					}
				}
			}
		} else if ($type === 'packages') {

			$adminPackages = $this->getInstalledFiles('apps/Dash/Packages/', true);

			$adminPackages =
				array_merge_recursive(
					$adminPackages,
					$this->getInstalledFiles('system/Base/Installer/Packages/Setup/Register/Modules/Packages/', true)
				);

			if (!$adminPackages || count($adminPackages) === 0) {
				return false;
			}

			foreach ($adminPackages['files'] as $adminPackageKey => $adminPackage) {
				if (strpos($adminPackage, 'package.json')) {
					$jsonFile =
						json_decode(
							$this->localContent->read($adminPackage),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading package.json at location ' . $adminPackage);
					}

					if ($jsonFile['sub_category'] === 'devtools' &&
						$this->postData['development-tools'] == 'false'
					) {
						continue;
					}

					$this->registerAdminPackage($jsonFile);
				}
			}
		} else if ($type === 'middlewares') {
			$adminMiddlewares = $this->getInstalledFiles('apps/Dash/Middlewares/', true);

			foreach ($adminMiddlewares['files'] as $adminMiddlewareKey => $adminMiddleware) {
				if (strpos($adminMiddleware, 'middleware.json')) {
					$jsonFile =
						json_decode(
							$this->localContent->read($adminMiddleware),
							true
						);

					if (!$jsonFile) {
						throw new \Exception('Problem reading middleware.json at location ' . $adminMiddleware);
					}

					if ($jsonFile['sub_category'] === 'devtools' &&
						$this->postData['development-tools'] == 'false'
					) {
						continue;
					}

					$this->registerAdminMiddleware($jsonFile);
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

			if ($jsonFile['sub_category'] === 'devtools' &&
				$this->postData['development-tools'] == 'false'
			) {
				return;
			}

			$this->registerAdminView($jsonFile);
		}
	}

	protected function registerAdminComponent(array $componentFile, $menuId)
	{
		$installedFiles = $this->getInstalledFiles('apps/Dash/Components/' . $componentFile['name'], true);

		return (new RegisterComponent())->register($this->db, $componentFile, $installedFiles, $menuId);
	}

	protected function registerAdminDashboard(array $componentFile)
	{
		return (new RegisterAdminDashboard())->register($this->db, $componentFile);
	}

	protected function registerAdminWidgets(array $componentFile, $registeredComponentId, $path)
	{
		return (new RegisterAdminWidgets())->register($this->db, $componentFile, $registeredComponentId, $path, $this->localContent);
	}

	public function updateAdminAppComponents()
	{
		return (new RegisterApp())->update($this->db);
	}

	protected function registerAdminMenu($appType, array $menu)
	{
		return (new RegisterMenu())->register($this->db, $appType, $menu);
	}

	protected function registerAdminPackage(array $packageFile)
	{
		$installedFiles = $this->getInstalledFiles('apps/Dash/Packages/' . $packageFile['name'], true);

		return (new RegisterPackage())->register($this->db, $packageFile, $installedFiles);
	}

	public function registerAdminMiddleware(array $middlewareFile)
	{
		$installedFiles = $this->getInstalledFiles('apps/Dash/Middlewares/' . $middlewareFile['name'], true);

		return (new RegisterMiddleware())->register($this->db, $middlewareFile, $installedFiles);
	}

	protected function registerAdminView(array $viewFile)
	{
		$appInstalledFiles = $this->getInstalledFiles('apps/Dash/Views/', true);
		$publicInstalledFiles = $this->getInstalledFiles('public/dash/', true);

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
		return (new RegisterRole())->registerAdminRole($this->db);
	}

	public function registerRegisteredUserAndGuestRoles()
	{
		return (new RegisterRole())->registerRegisteredUserAndGuestRoles($this->db);
	}

	public function registerAdminAccount($adminRoleId, $workFactor = 12)
	{
		$password = $this->container['security']->hash($this->postData['pass'], $workFactor);

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

	public function registerTimezones()
	{
		return (new RegisterTimezones())->register($this->db, $this->localContent);
	}

	public function registerStorages()
	{
		return (new RegisterStorages())->register($this->db);
	}

	protected function getInstalledFiles($directory = null, $sub = true)
	{
		$installedFiles = [];
		$installedFiles['dirs'] = [];
		$installedFiles['files'] = [];

		if ($directory) {
			$installedFiles['files'] =
				$this->localContent->listContents($directory, $sub)
				->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
				->map(fn (StorageAttributes $attributes) => $attributes->path())
				->toArray();

			$installedFiles['dirs'] =
				$this->localContent->listContents($directory, $sub)
				->filter(fn (StorageAttributes $attributes) => $attributes->isDir())
				->map(fn (StorageAttributes $attributes) => $attributes->path())
				->toArray();

			return $installedFiles;
		} else {
			return null;
		}
	}

	public function registerWorkers()
	{
		(new RegisterWorkers())->register($this->db);
	}

	public function registerSchedules()
	{
		(new RegisterSchedules())->register($this->db);
	}

	public function registerTasks()
	{
		(new RegisterTasks())->register($this->db);
	}

	public function writeConfigs($coreJson = null)
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

	protected function addIndex(string $table, array $index, $schemaName = '')
	{
		foreach ($index as $idx) {
			$columnsArr = $idx->getColumns();

			if (count($columnsArr) > 1) {
				$columns = '';

				foreach ($columnsArr as $columnsArrKey => $column) {
					$columns .= '`' . $column . '`';

					if ($columnsArrKey != Arr::lastKey($columnsArr)) {
						$columns .= ',';
					}
				}
			} else {
				$columns = '`' . $columnsArr[0] . '`';
			}

			$this->executeSQL(
				'ALTER TABLE `' . $table . '` ADD ' . strtoupper($idx->getType()) . ' `' . $idx->getName() . '` (' . $columns . ')'
			);
		}
	}

	protected function executeSQL(string $sql)
	{
		try {
			return $this->db->query($sql);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function createNewDb()
	{
		// var_dump($this->db);die();
		// $db = new \PDO(
		// 	"mysql:host=" . $this->postData['host'] . ";dbname=mysql", $this->postData['create-username'], $this->postData['create-password']
		// );

		try {
			$this->executeSQL('CREATE DATABASE ' . $this->postData['database_name'] . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
			$this->checkUser();
		} catch (\Exception $e) {
			throw $e;
		}

		return false;
	}

	protected function checkUser()
	{
		$checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE '" . $this->postData['username'] . "'");

		if ($checkUser->numRows() === 0) {
			$this->executeSQL("CREATE USER '" . $this->postData['username'] . "'@'%' IDENTIFIED WITH mysql_native_password BY '" . $this->postData['password'] . "';");
		}
		$this->executeSQL("GRANT ALL PRIVILEGES ON `" . $this->postData['database_name'] . "`.* TO '" . $this->postData['username'] . "'@'%' WITH GRANT OPTION;");

		return true;
	}
}