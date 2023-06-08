<?php

namespace System\Base\Installer\Packages;

use Apps\Core\Packages\Devtools\Api\Contracts\Install\Schema\DevtoolsApiContracts;
use Apps\Core\Packages\Devtools\Api\Contracts\Model\AppsDashDevtoolsApiContracts;
use Apps\Core\Packages\Devtools\Api\Enums\Install\Schema\DevtoolsApiEnums;
use Apps\Core\Packages\Devtools\Api\Enums\Model\AppsDashDevtoolsApiEnums;
use Apps\Core\Packages\Devtools\Modules\Install\Schema\DevtoolsModulesBundles;
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
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\CitiesIp2LocationV4;
use System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\CitiesIp2LocationV6;
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
use System\Base\Installer\Packages\Setup\Schema\Modules\Bundles;
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
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderApps;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsIpFilter;
use System\Base\Providers\AppsServiceProvider\Model\ServiceProviderAppsTypes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\BasepackagesApi;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Api\BasepackagesApiCalls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesDashboards;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesFilters;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesImportExport;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMenus;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotes;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesNotifications;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesStorages;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesTemplates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesWidgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Dashboards\BasepackagesDashboardsWidgets;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailQueue;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email\BasepackagesEmailServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCities;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCitiesIp2locationv4;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCitiesIp2locationv6;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoCountries;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoStates;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Geo\BasepackagesGeoTimezones;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Messenger\BasepackagesMessenger;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Storages\BasepackagesStoragesLocal;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsAgents;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsCanlogin;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsIdentifiers;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSecurity;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsTunnels;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersProfiles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersRoles;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersJobs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersSchedules;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersTasks;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersWorkers;
use System\Base\Providers\CoreServiceProvider\Model\ServiceProviderCore;
use System\Base\Providers\DatabaseServiceProvider\Ff;
use System\Base\Providers\DomainsServiceProvider\Model\ServiceProviderDomains;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesBundles;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesComponents;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesMiddlewares;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesPackages;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViews;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViewsSettings;

class Setup
{
	protected $container;

	protected $postData;

	protected $request;

	protected $session;

	protected $db;

	protected $ff;

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

		if ($this->request->isPost() && !$precheckFail && isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'ff') {
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
		$this->PROPERTY = $PROPERTY;
			}

			$this->db = new Mysql($this->dbConfig['db']);
		}

		if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'mysql') {
			$this->ff = (new Ff((object) ['enabled' => false, 'timeout' => 0], $this->request))->init();
		}

		$this->basepackages = $this->container->getShared('basepackages');

		$this->progress = $this->basepackages->progress;

		if (!$precheckFail) {
			$this->localContent = $this->container['localContent'];
		}

		$this->remoteWebContent = $this->container['remoteWebContent'];
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
		if (!$this->db) {
			return true;
		}

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
		$databases = [
			'service_provider_core' 					=> [
					'schema'	=> new Core,
					'model'		=> new ServiceProviderCore,
				],
			'service_provider_apps' 					=> [
					'schema'	=> new Apps,
					'model'		=> new ServiceProviderApps,
				],
			'service_provider_apps_types' 				=> [
					'schema'	=> new Types,
					'model'		=> new ServiceProviderAppsTypes,
				],
			'service_provider_apps_ip_filter' 			=> [
					'schema'	=> new IpFilter,
					'model'		=> new ServiceProviderAppsIpFilter,
				],
			'service_provider_domains' 					=> [
					'schema'	=> new Domains,
					'model'		=> new ServiceProviderDomains,
				],
			'modules_bundles' 							=> [
					'schema'	=> new Bundles,
					'model'		=> new ModulesBundles,
				],
			'modules_components' 						=> [
					'schema'	=> new Components,
					'model'		=> new ModulesComponents,
				],
			'modules_packages' 							=> [
					'schema'	=> new Packages,
					'model'		=> new ModulesPackages,
				],
			'modules_middlewares' 						=> [
					'schema'	=> new Middlewares,
					'model'		=> new ModulesMiddlewares,
				],
			'modules_views' 							=> [
					'schema'	=> new Views,
					'model'		=> new ModulesViews,
				],
			'modules_views_settings' 					=> [
					'schema'	=> new Settings,
					'model'		=> new ModulesViewsSettings,
				],
			'basepackages_email_services' 				=> [
					'schema'	=> new EmailServices,
					'model'		=> new BasepackagesEmailServices,
				],
			'basepackages_email_queue' 					=> [
					'schema'	=> new EmailQueue,
					'model'		=> new BasepackagesEmailQueue,
				],
			'basepackages_users_accounts' 				=> [
					'schema'	=> new Accounts,
					'model'		=> new BasepackagesUsersAccounts,
				],
			'basepackages_users_accounts_security' 		=> [
					'schema'	=> new Security,
					'model'		=> new BasepackagesUsersAccountsSecurity,
				],
			'basepackages_users_accounts_canlogin' 		=> [
					'schema'	=> new CanLogin,
					'model'		=> new BasepackagesUsersAccountsCanlogin,
				],
			'basepackages_users_accounts_sessions' 		=> [
					'schema'	=> new Sessions,
					'model'		=> new BasepackagesUsersAccountsSessions,
				],
			'basepackages_users_accounts_identifiers' 	=> [
					'schema'	=> new Identifiers,
					'model'		=> new BasepackagesUsersAccountsIdentifiers,
				],
			'basepackages_users_accounts_agents' 		=> [
					'schema'	=> new Agents,
					'model'		=> new BasepackagesUsersAccountsAgents,
				],
			'basepackages_users_accounts_tunnels' 		=> [
					'schema'	=> new Tunnels,
					'model'		=> new BasepackagesUsersAccountsTunnels,
				],
			'basepackages_users_profiles' 				=> [
					'schema'	=> new Profiles,
					'model'		=> new BasepackagesUsersProfiles,
				],
			'basepackages_users_roles' 					=> [
					'schema'	=> new Roles,
					'model'		=> new BasepackagesUsersRoles,
				],
			'basepackages_menus' 						=> [
					'schema'	=> new Menus,
					'model'		=> new BasepackagesMenus,
				],
			'basepackages_filters' 						=> [
					'schema'	=> new Filters,
					'model'		=> new BasepackagesFilters,
				],
			'basepackages_geo_countries' 				=> [
					'schema'	=> new Countries,
					'model'		=> new BasepackagesGeoCountries,
				],
			'basepackages_geo_states' 					=> [
					'schema'	=> new States,
					'model'		=> new BasepackagesGeoStates,
				],
			'basepackages_geo_cities' 					=> [
					'schema'	=> new Cities,
					'model'		=> new BasepackagesGeoCities,
				],
			'basepackages_geo_cities_ip2locationv4' 	=> [
					'schema'	=> new CitiesIp2LocationV4,
					'model'		=> new BasepackagesGeoCitiesIp2locationv4,
				],
			'basepackages_geo_cities_ip2locationv6' 	=> [
					'schema'	=> new CitiesIp2LocationV6,
					'model'		=> new BasepackagesGeoCitiesIp2locationv6,
				],
			'basepackages_geo_cities' 					=> [
					'schema'	=> new Cities,
					'model'		=> new BasepackagesGeoCities,
				],
			'basepackages_geo_timezones' 				=> [
					'schema'	=> new Timezones,
					'model'		=> new BasepackagesGeoTimezones,
				],
			'basepackages_address_book' 				=> [
					'schema'	=> new AddressBook,
					'model'		=> new BasepackagesAddressBook,
				],
			'basepackages_storages' 					=> [
					'schema'	=> new Storages,
					'model'		=> new BasepackagesStorages,
				],
			'basepackages_storages_local' 				=> [
					'schema'	=> new StoragesLocal,
					'model'		=> new BasepackagesStoragesLocal,
				],
			'basepackages_activity_logs' 				=> [
					'schema'	=> new ActivityLogs,
					'model'		=> new BasepackagesActivityLogs,
				],
			'basepackages_notes' 						=> [
					'schema'	=> new Notes,
					'model'		=> new BasepackagesNotes,
				],
			'basepackages_notifications' 				=> [
					'schema'	=> new Notifications,
					'model'		=> new BasepackagesNotifications,
				],
			'basepackages_workers_workers' 				=> [
					'schema'	=> new Workers,
					'model'		=> new BasepackagesWorkersWorkers,
				],
			'basepackages_workers_schedules' 			=> [
					'schema'	=> new Schedules,
					'model'		=> new BasepackagesWorkersSchedules,
				],
			'basepackages_workers_tasks' 				=> [
					'schema'	=> new Tasks,
					'model'		=> new BasepackagesWorkersTasks,
				],
			'basepackages_workers_jobs' 				=> [
					'schema'	=> new Jobs,
					'model'		=> new BasepackagesWorkersJobs,
				],
			'basepackages_import_export' 				=> [
					'schema'	=> new ImportExport,
					'model'		=> new BasepackagesImportExport,
				],
			'basepackages_templates' 					=> [
					'schema'	=> new Templates,
					'model'		=> new BasepackagesTemplates,
				],
			'basepackages_dashboards' 					=> [
					'schema'	=> new Dashboards,
					'model'		=> new BasepackagesDashboards,
				],
			'basepackages_dashboards_widgets' 			=> [
					'schema'	=> new DashboardsWidgets,
					'model'		=> new BasepackagesDashboardsWidgets,
				],
			'basepackages_widgets' 						=> [
					'schema'	=> new Widgets,
					'model'		=> new BasepackagesWidgets,
				],
			'basepackages_messenger' 					=> [
					'schema'	=> new Messenger,
					'model'		=> new BasepackagesMessenger,
			],
			'basepackages_api' 							=> [
					'schema'	=> new Api,
					'model'		=> new BasepackagesApi,
			],
			'basepackages_api_calls' 					=> [
					'schema'	=> new ApiCalls,
					'model'		=> new BasepackagesApiCalls,
			],
			'basepackages_api_apis_repos' 				=> [
					'schema'	=> new Repos,
					'model'		=> null
			]
		];

		if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'ff') {
			foreach ($databases as $tableName => $tableClass) {
				if (method_exists($tableClass['schema'], 'columns')) {
					$this->db->createTable($tableName, $this->dbConfig['db']['dbname'], $tableClass['schema']->columns());
				}
				if (method_exists($tableClass['schema'], 'indexes')) {
					$this->addIndex($tableName, $tableClass['schema']->indexes());
				}
			}
		}

		if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'mysql') {
			foreach ($databases as $tableName => $tableClass) {
				if ($tableClass['model'] && $tableClass['model']->getSource()) {
					$tableName = $tableClass['model']->getSource();
				}

				$config = $this->ff->generateConfig($tableName, $tableClass['schema'], $tableClass['model']);
				$schema = $this->ff->generateSchema($tableName, $tableClass['schema'], $tableClass['model']);

				$this->ff->store($tableName, $config, $schema)->deleteStore();

				$this->ff->store($tableName, $config, $schema);
			}
		}

		return true;
	}

	protected function registerRepos()
	{
		(new RegisterRepos())->register($this->db, $this->ff);

		return true;
	}

	protected function registerDomain()
	{
		(new RegisterDomain())->register($this->db, $this->ff, $this->request);

		return true;
	}

	protected function registerCore(array $baseConfig)
	{
		(new RegisterCore())->register($baseConfig, $this->db, $this->ff);

		return true;
	}

	protected function registerCoreAppType()
	{
		return (new RegisterCoreAppType())->register($this->db, $this->ff);
	}

	protected function registerCoreApp()
	{
		return (new RegisterCoreApp())->register($this->db, $this->ff);
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
		return (new RegisterComponent())->register($this->db, $this->ff, $componentFile, $menuId);
	}

	protected function registerCoreDashboard(array $componentFile)
	{
		return (new RegisterCoreDashboard())->register($this->db, $this->ff, $componentFile);
	}

	protected function registerCoreWidgets(array $componentFile, $registeredComponentId, $path)
	{
		return (new RegisterCoreWidgets())->register($this->db, $this->ff, $componentFile, $registeredComponentId, $path, $this->localContent);
	}

	protected function updateCoreAppComponents()
	{
		return (new RegisterCoreApp())->update($this->db, $this->ff);
	}

	protected function registerCoreMenu($appType, array $menu)
	{
		return (new RegisterMenu())->register($this->db, $this->ff, $appType, $menu);
	}

	protected function registerCorePackage(array $packageFile)
	{
		return (new RegisterPackage())->register($this->db, $this->ff, $packageFile);
	}

	protected function registerCoreMiddleware(array $middlewareFile)
	{
		return (new RegisterMiddleware())->register($this->db, $this->ff, $middlewareFile);
	}

	protected function registerCoreView(array $viewFile)
	{
		return (new RegisterView())->register($this->db, $this->ff, $viewFile);
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
		return (new RegisterRole())->registerCoreRole($this->db, $this->ff);
	}

	protected function registerRegisteredUserAndGuestRoles()
	{
		return (new RegisterRole())->registerRegisteredUserAndGuestRoles($this->db, $this->ff);
	}

	protected function registerCoreAccount($workFactor = 12)
	{
		$password = $this->container['security']->hash($this->postData['pass'], $workFactor);

		return (new RegisterRootCoreAccount())->register($this->db, $this->ff, $this->postData['email'], $password);
	}

	protected function registerCoreProfile()
	{
		return (new RegisterRootCoreProfile())->register($this->db, $this->ff);
	}

	protected function registerExcludeAutoGeneratedFilters()
	{
		return (new RegisterFilter())->register($this->db, $this->ff);
	}

	protected function registerCountries()
	{
		return (new RegisterCountries())->register($this->db, $this->ff, $this->localContent, $this->postData['country']);
	}

	protected function downloadCountriesStateAndCities()
	{
		return (new RegisterCountries())->downloadSelectedCountryStatesAndCities($this->ff, $this->localContent, $this->remoteWebContent, $this->postData['country']);
	}

	protected function registerCountriesStateAndCities()
	{
		return (new RegisterCountries())->registerSelectedCountryStatesAndCities($this->ff, $this->localContent, $this->remoteWebContent, $this->postData['country'], $this->postData['ip2location']);
	}

	protected function registerTimezones()
	{
		return (new RegisterTimezones())->register($this->db, $this->ff, $this->localContent);
	}

	protected function registerStorages(array $packageFile)
	{
		return (new RegisterStorages())->register($this->db, $this->ff, $packageFile);
	}

	protected function registerWorkers()
	{
		(new RegisterWorkers())->register($this->db, $this->ff);

		return true;
	}

	protected function registerSchedules()
	{
		(new RegisterSchedules())->register($this->db, $this->ff);

		return true;
	}

	protected function registerTasks()
	{
		(new RegisterTasks())->register($this->db, $this->ff);

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