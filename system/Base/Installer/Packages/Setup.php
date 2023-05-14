<?php

namespace System\Base\Installer\Packages;

use Apps\Core\Packages\Devtools\Api\Contracts\Install\Schema\DevtoolsApiContracts;
use Apps\Core\Packages\Devtools\Api\Contracts\Model\AppsDashDevtoolsApiContracts;
use Apps\Core\Packages\Devtools\Api\Enums\Install\Schema\DevtoolsApiEnums;
use Apps\Core\Packages\Devtools\Api\Enums\Model\AppsDashDevtoolsApiEnums;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToDeleteFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Helper\Json;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Api\Apis\Repos as RegisterRepos;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Dashboard as RegisterCoreDashboard;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Countries as RegisterCountries;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Timezones as RegisterTimezones;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Storages\Storages as RegisterStorages;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Account as RegisterRootCoreAccount;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Profile as RegisterRootCoreProfile;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Role as RegisterRole;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Widgets as RegisterCoreWidgets;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Schedules as RegisterSchedules;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Tasks as RegisterTasks;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Workers as RegisterWorkers;
use System\Base\Installer\Packages\Setup\Register\Modules\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Modules\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Modules\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Modules\View as RegisterView;
use System\Base\Installer\Packages\Setup\Register\Providers\App as RegisterCoreApp;
use System\Base\Installer\Packages\Setup\Register\Providers\App\Type as RegisterCoreAppType;
use System\Base\Installer\Packages\Setup\Register\Providers\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Providers\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\ActivityLogs;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\AddressBook;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Api\Api;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Api\ApiCalls;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Api\Apis\Repos;
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
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Messenger;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notes;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Notifications;
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
use System\Base\Installer\Packages\Setup\Schema\Modules\Components;
use System\Base\Installer\Packages\Setup\Schema\Modules\Middlewares;
use System\Base\Installer\Packages\Setup\Schema\Modules\Packages;
use System\Base\Installer\Packages\Setup\Schema\Modules\Views;
use System\Base\Installer\Packages\Setup\Schema\Modules\Views\Settings;
use System\Base\Installer\Packages\Setup\Schema\Providers\Apps;
use System\Base\Installer\Packages\Setup\Schema\Providers\Apps\IpFilter;
use System\Base\Installer\Packages\Setup\Schema\Providers\Apps\Types;
use System\Base\Installer\Packages\Setup\Schema\Providers\Cache;
use System\Base\Installer\Packages\Setup\Schema\Providers\Core;
use System\Base\Installer\Packages\Setup\Schema\Providers\Domains;
use System\Base\Installer\Packages\Setup\Schema\Providers\Logs;
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

	protected $basepackages;

	protected $progress;

	protected $configs;

	public function __construct($container, $postData, $precheckFail = false)
	{
		$this->container = $container;

		$this->request = $this->container->getShared('request');

		$this->session = $this->container->getShared('session');

		$this->postData = $postData;

		$this->validation = $this->container->getShared('validation');

		$this->security = $this->container->getShared('security');

		$this->cookies = $this->container->getShared('cookies');

		if ($this->request->isPost() && !$precheckFail) {
			$this->dbConfig =
					[
						'db' =>
							[
								'host' 		=>
									isset($this->postData['host']) ?
									$this->postData['host'] :
									'',
								'dbname' 	=>
									isset($this->postData['dbname']) ?
									$this->postData['dbname'] :
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

		$this->basepackages = $this->container->getShared('basepackages');

		$this->progress = $this->basepackages->progress;

		if (!$precheckFail) {
			$this->localContent = $this->container['localContent'];
		}
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this, $method)) {
			$this->progress->updateProgress($method, null, false);

			$call = call_user_func_array([$this, $method], $arguments);

			$callResult = $call;

			if ($call !== false) {
				$call = true;
			}

			$this->progress->updateProgress($method, $call, false);

			return $callResult;
		}
	}

	protected function cleanVar()
	{
		$files = $this->basepackages->utils->init($this->container)->scanDir('var/');

		foreach ($files['files'] as $key => $file) {
			try {
				if (strpos($file, 'progress') === false &&
					strpos($file, 'opcache') === false &&
					strpos($file, 'pusher-') === false &&
					strpos($file, 'messenger-') === false
				) {
					$this->localContent->delete($file);
				}
			} catch (FilesystemException | UnableToDeleteFile $exception) {
				throw $exception;
			}
		}

		return true;
	}

	protected function cleanOldBackups()
	{
		$files = $this->basepackages->utils->init($this->container)->scanDir('.backups/');

		foreach ($files['files'] as $key => $file) {
			try {
				$this->localContent->delete($file);
			} catch (FilesystemException | UnableToDeleteFile $exception) {
				throw $exception;
			}
		}

		return true;
	}

	protected function cleanOldCookies()
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

		$this->cookies->set(
			'Installer',
			'0',
			1,
			'/',
			null,
			null,
			true
		);

		$this->cookies->send();

		return true;
	}

	protected function checkDbEmpty()
	{
		$allTables = $this->db->listTables($this->postData['dbname']);

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

	protected function buildSchema()
	{
		$dbName = $this->dbConfig['db']['dbname'];

		$this->db->createTable('service_provider_core', $dbName, (new Core)->columns());
		$this->db->createTable('service_provider_apps', $dbName, (new Apps)->columns());
		$this->db->createTable('service_provider_apps_types', $dbName, (new Types)->columns());
		$this->db->createTable('service_provider_apps_ip_filter', $dbName, (new IpFilter)->columns());
		$this->db->createTable('service_provider_domains', $dbName, (new Domains)->columns());
		$this->db->createTable('service_provider_logs', $dbName, (new Logs)->columns());
		$this->db->createTable('service_provider_cache', $dbName, (new Cache)->columns());

		$this->db->createTable('modules_components', $dbName, (new Components)->columns());
		$this->db->createTable('modules_packages', $dbName, (new Packages)->columns());
		$this->db->createTable('modules_middlewares', $dbName, (new Middlewares)->columns());
		$this->db->createTable('modules_views', $dbName, (new Views)->columns());
		$this->db->createTable('modules_views_settings', $dbName, (new Settings)->columns());

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
		$this->db->createTable('basepackages_messenger', $dbName, (new Messenger)->columns());
		$this->db->createTable('basepackages_api', $dbName, (new Api)->columns());
		$this->db->createTable('basepackages_api_calls', $dbName, (new ApiCalls)->columns());
		$this->db->createTable('basepackages_api_apis_repos', $dbName, (new Repos)->columns());

		if ($this->postData['dev'] == 'true') {
			$this->db->createTable('apps_core_devtools_api_contracts', $dbName, (new DevtoolsApiContracts)->columns());
			$this->db->createTable('apps_core_devtools_api_enums', $dbName, (new DevtoolsApiEnums)->columns());
		}

		return true;
	}

	protected function registerRepos()
	{
		(new RegisterRepos())->register($this->db);

		return true;
	}

	protected function registerDomain()
	{
		(new RegisterDomain())->register($this->db, $this->request);

		return true;
	}

	protected function registerCore(array $baseConfig)
	{
		(new RegisterCore())->register($baseConfig, $this->db);

		return true;
	}

	protected function registerCoreAppType()
	{
		return (new RegisterCoreAppType())->register($this->db);
	}

	protected function registerCoreApp()
	{
		return (new RegisterCoreApp())->register($this->db);
	}

	protected function registerModule($type)
	{
		if ($type === 'components') {
			$adminComponents = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Components/', true);

			if (!$adminComponents || count($adminComponents) === 0) {
				return false;
			}

			foreach ($adminComponents['files'] as $adminComponentKey => $adminComponent) {
				if (strpos($adminComponent, 'component.json')) {
					try {
						$jsonFile =
							Json::decode(
								$this->localContent->read($adminComponent),
								true
							);
					} catch (\throwable $e) {
						throw new \Exception($e->getMessage() . '. Problem reading component.json at location ' . $adminComponent);
					}

					if ($jsonFile['category'] === 'devtools' &&
						$this->postData['dev'] == 'false'
					) {
						continue;
					}

					if ($jsonFile['menu'] && $jsonFile['menu'] !== 'false') {
						$menuId = $this->registerCoreMenu($jsonFile['app_type'], $jsonFile['menu']);
					} else {
						$menuId = null;
					}

					$registeredComponentId = $this->registerCoreComponent($jsonFile, $menuId);

					if ($jsonFile['route'] === 'dashboards') {
						$this->registerCoreDashboard($jsonFile);
					}

					if (isset($jsonFile['widgets']) && count($jsonFile['widgets']) > 0) {
						$this->registerCoreWidgets($jsonFile, $registeredComponentId, $adminComponent);
					}
				}
			}
		} else if ($type === 'packages') {
			$adminPackages = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Packages/', true);

			$adminPackages =
				array_merge_recursive(
					$adminPackages,
					$this->basepackages->utils->init($this->container)->scanDir('system/Base/Installer/Packages/Setup/Register/Modules/Packages/', true)
				);

			if (!$adminPackages || count($adminPackages) === 0) {
				return false;
			}

			foreach ($adminPackages['files'] as $adminPackageKey => $adminPackage) {
				if (strpos($adminPackage, 'package.json')) {
					try {
						$jsonFile =
							Json::decode(
								$this->localContent->read($adminPackage),
								true
							);
					} catch (\throwable $e) {
						throw new \Exception($e->getMessage() . '. Problem reading package.json at location ' . $adminPackage);
					}

					if ($jsonFile['category'] === 'devtools' &&
						$this->postData['dev'] == 'false'
					) {
						continue;
					}

					if ($jsonFile['name'] === 'Storages') {
						$this->registerStorages($jsonFile);
					}

					$jsonFile['files'] = [];

					if ($jsonFile['name'] === 'Core') {
						$jsonFile['files'] =
							array_merge_recursive($this->basepackages->utils->init($this->container)->scanDir('system/', true), $this->basepackages->utils->init($this->container)->scanDir('apps/', true));
					}

					$this->registerCorePackage($jsonFile);
				}
			}
		} else if ($type === 'middlewares') {
			$adminMiddlewares = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Middlewares/', true);

			foreach ($adminMiddlewares['files'] as $adminMiddlewareKey => $adminMiddleware) {
				if (strpos($adminMiddleware, 'middleware.json')) {
					try {
						$jsonFile =
							Json::decode(
								$this->localContent->read($adminMiddleware),
								true
							);
					} catch (\throwable $e) {
						throw new \Exception($e->getMessage() . '. Problem reading middleware.json at location ' . $adminMiddleware);
					}

					if ($jsonFile['category'] === 'devtools' &&
						$this->postData['dev'] == 'false'
					) {
						continue;
					}

					$this->registerCoreMiddleware($jsonFile);
				}
			}
		} else if ($type === 'views') {
			try {
				$jsonFile =
					Json::decode(
						$this->localContent->read('apps/Core/Views/Default/view.json'),
						true
					);
			} catch (\throwable $e) {
				throw new \Exception($e->getMessage() . '. Problem reading view.json');
			}

			if ($jsonFile['category'] === 'devtools' &&
				$this->postData['dev'] == 'false'
			) {
				return;
			}

			$this->registerCoreView($jsonFile);
		}

		return true;
	}

	protected function registerCoreComponent(array $componentFile, $menuId)
	{
		return (new RegisterComponent())->register($this->db, $componentFile, $menuId);
	}

	protected function registerCoreDashboard(array $componentFile)
	{
		return (new RegisterCoreDashboard())->register($this->db, $componentFile);
	}

	protected function registerCoreWidgets(array $componentFile, $registeredComponentId, $path)
	{
		return (new RegisterCoreWidgets())->register($this->db, $componentFile, $registeredComponentId, $path, $this->localContent);
	}

	protected function updateCoreAppComponents()
	{
		return (new RegisterCoreApp())->update($this->db);
	}

	protected function registerCoreMenu($appType, array $menu)
	{
		return (new RegisterMenu())->register($this->db, $appType, $menu);
	}

	protected function registerCorePackage(array $packageFile)
	{
		return (new RegisterPackage())->register($this->db, $packageFile);
	}

	protected function registerCoreMiddleware(array $middlewareFile)
	{
		return (new RegisterMiddleware())->register($this->db, $middlewareFile);
	}

	protected function registerCoreView(array $viewFile)
	{
		return (new RegisterView())->register($this->db, $viewFile);
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

	protected function registerCoreRole()
	{
		return (new RegisterRole())->registerCoreRole($this->db);
	}

	protected function registerRegisteredUserAndGuestRoles()
	{
		return (new RegisterRole())->registerRegisteredUserAndGuestRoles($this->db);
	}

	protected function registerCoreAccount($adminRoleId, $workFactor = 12)
	{
		$password = $this->container['security']->hash($this->postData['pass'], $workFactor);

		return (new RegisterRootCoreAccount())->register($this->db, $this->postData['email'], $password, $adminRoleId);
	}

	protected function registerCoreProfile($adminAccountId)
	{
		return (new RegisterRootCoreProfile())->register($this->db, $adminAccountId);
	}

	protected function registerExcludeAutoGeneratedFilters()
	{
		return (new RegisterFilter())->register($this->db);
	}

	protected function registerCountries()
	{
		return (new RegisterCountries())->register($this->db, $this->localContent);
	}

	protected function registerTimezones()
	{
		return (new RegisterTimezones())->register($this->db, $this->localContent);
	}

	protected function registerStorages(array $packageFile)
	{
		return (new RegisterStorages())->register($this->db, $packageFile);
	}

	protected function registerWorkers()
	{
		(new RegisterWorkers())->register($this->db);

		return true;
	}

	protected function registerSchedules()
	{
		(new RegisterSchedules())->register($this->db);

		return true;
	}

	protected function registerTasks()
	{
		(new RegisterTasks())->register($this->db);

		return true;
	}

	protected function writeConfigs($coreJson = null, $writeBaseFile = false)
	{
		if (!$this->configs) {
			$this->configs = new Configs($this->container, $this->postData, $coreJson);
		}

		return $this->configs->write($writeBaseFile);
	}

	protected function revertBaseConfig($coreJson = null)
	{
		if (!$this->configs) {
			$this->configs = new Configs($this->container, $this->postData, $coreJson);
		}

		return $this->configs->revert();
	}

	protected function removeInstaller()
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

	protected function executeSQL(string $sql, $data = [])
	{
		try {
			return $this->db->query($sql, $data);
		} catch (\PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}

	protected function createNewDb()
	{
		$this->executeSQL(
			"CREATE DATABASE IF NOT EXISTS " . $this->postData['dbname'] . " CHARACTER SET " . $this->postData['charset'] . " COLLATE " . $this->postData['collation']
		);

		return true;
	}

	protected function checkUser($dontCreate = false)
	{
		$checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE ?", [$this->postData['username']]);

		if ($checkUser->numRows() === 0) {
			if (!isset($this->postData['create-username']) && !isset($this->postData['create-password'])) {
				throw new \Exception('User ' . $this->postData['username'] . ' does not exist. Please enable create new user/database.');
			}

			if ($dontCreate) {//We check if user dont exists for password strength
				return false;
			}

			$this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH mysql_native_password BY ?;", [$this->postData['username'], $this->postData['password']]);
		}

		$this->executeSQL("GRANT ALL PRIVILEGES ON " . $this->postData['dbname'] . ".* TO ?@'%' WITH GRANT OPTION;", [$this->postData['username']]);

		return true;
	}

	protected function executeComposer()
	{
		putenv('COMPOSER_HOME=' . base_path('external/'));

		$stream = fopen(base_path('external/composer.install'), 'w');
		$input = new \Symfony\Component\Console\Input\StringInput('install -d ' . base_path('external/'));
		$output = new \Symfony\Component\Console\Output\StreamOutput($stream);

		$application = new \Composer\Console\Application();
		$application->setAutoExit(false); // prevent `$application->run` method from exiting the script

		try {
			$app = $application->run($input, $output);
		} catch (\throwable $e) {
			throw $e;
		}

		if ($app !== 0) {
			return false;
		}

		return true;
	}
}