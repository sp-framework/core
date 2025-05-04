<?php

namespace System\Base\Installer\Packages;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis\Repos as RegisterRepos;
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
use System\Base\Installer\Packages\Setup\Register\Modules\External as RegisterExternal;
use System\Base\Installer\Packages\Setup\Register\Providers\App as RegisterCoreApp;
use System\Base\Installer\Packages\Setup\Register\Providers\App\Type as RegisterCoreAppType;
use System\Base\Installer\Packages\Setup\Register\Providers\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Providers\Domain as RegisterDomain;
use System\Base\Installer\Packages\Setup\Schema;
use System\Base\Installer\Packages\Setup\Write\Configs;
use System\Base\Installer\Packages\Setup\Write\Pdo;
use System\Base\Providers\DatabaseServiceProvider\Ff;

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

	protected $validation;

	protected $security;

	protected $cookies;

	protected $helper;

	protected $remoteWebContent;

	protected $onlyUpdateDb = false;

	protected $storesToIndex = [];

	public function __construct($container, $postData, $precheckFail = false, $onlyUpdateDb = false)
	{
		$this->container = $container;

		$this->request = $this->container->getShared('request');

		$this->session = $this->container->getShared('session');

		$this->postData = $postData;

		$this->validation = $this->container->getShared('validation');

		$this->security = $this->container->getShared('security');

		$this->cookies = $this->container->getShared('cookies');

		$this->helper = $this->container->getShared('helper');

		if (($this->request->isPost() &&
			 !$precheckFail &&
			 isset($this->postData['databasetype']) &&
			 $this->postData['databasetype'] !== 'ff') ||
			$onlyUpdateDb && $this->request->isPost()
		) {
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

		if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'db') {
			$reset = false;

			if ($this->postData['databasetype'] === 'hybrid') {
				$reset = true;
			}

			$this->ff = (new Ff(
				(object) [
					'cache' => (object) [
						'enabled' => false,
						'timeout' => 0
					],
					'databaseType' => $this->postData['databasetype']
				], $this->request, $this->helper))->init($reset, false);
		}

		if (!$onlyUpdateDb) {
			$this->progress = $this->basepackages->progress;
		}

		if (!$precheckFail) {
			$this->localContent = $this->container['localContent'];
			$this->remoteWebContent = $this->container['remoteWebContent'];
		}

		$this->onlyUpdateDb = $onlyUpdateDb;
	}

	public function __call($method, $arguments)
	{
		if (method_exists($this, $method)) {
			if (!$this->onlyUpdateDb) {
				$this->progress->updateProgress($method, null, false);
			}

			$call = call_user_func_array([$this, $method], $arguments);

			$callResult = $call;

			if ($call !== false) {
				$call = true;
			}

			if (!$this->onlyUpdateDb) {
				$this->progress->updateProgress($method, $call, false);
			}

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

	protected function cleanOldFfs()
	{
		$files = $this->basepackages->utils->init($this->container)->scanDir('.ff/');

		foreach ($files['files'] as $key => $file) {
			try {
				if (strpos($file, '.ff') !== false) {
					$this->localContent->delete($file);
				}
			} catch (FilesystemException | UnableToDeleteFile $exception) {
				throw $exception;
			}
		}

		foreach ($files['dirs'] as $key => $dir) {
			try {
				if (strpos($dir, '.ff') !== false) {
					$this->localContent->deleteDirectory($dir);
				}
			} catch (FilesystemException | UnableToDeleteDirectory $exception) {
				throw $exception;
			}
		}

		return true;
	}

	protected function cleanOldAPIKeys()
	{
		$files = $this->basepackages->utils->init($this->container)->scanDir('system/.api/');

		foreach ($files['files'] as $key => $file) {
			try {
				if (strpos($file, '.api') !== false) {
					$this->localContent->delete($file);
				}
			} catch (FilesystemException | UnableToDeleteFile $exception) {
				throw $exception;
			}
		}

		foreach ($files['dirs'] as $key => $dir) {
			try {
				if (strpos($dir, '.api') !== false) {
					$this->localContent->deleteDirectory($dir);
				}
			} catch (FilesystemException | UnableToDeleteDirectory $exception) {
				throw $exception;
			}
		}

		return true;
	}

	protected function cleanOldBackups()
	{
		$dirs =
			[
				'.backupsdb/',
				'.backupsff/'
			];

		foreach ($dirs as $dir) {
			$files = $this->basepackages->utils->init($this->container)->scanDir($dir);

			$this->cleanOldBackupsFiles($files);
		}

		return true;
	}

	protected function cleanOldBackupsFiles($files)
	{
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
		$cookieKey = 'SP';

		//Set cookies to 1 second so browser removes them.
		$this->cookies->set(
			$cookieKey,
			'0',
			1,
			'/',
			false,
			$this->request->getHttpHost(),
			true
		);

		$this->cookies->get($cookieKey)->setOptions(['samesite'=>'strict']);

		$this->cookies->set(
			'id',
			'0',
			1,
			'/',
			false,
			$this->request->getHttpHost(),
			true
		);

		$this->cookies->set(
			'Installer',
			'0',
			1,
			'/',
			false,
			$this->request->getHttpHost(),
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
		$databases = (new Schema)->getSchema($this->postData['dev']);

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

		if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'db') {
			$this->cleanOldFfs();

			foreach ($databases as $tableName => $tableClass) {
				if ($tableClass['model'] && $tableClass['model']->getSource()) {
					$tableName = $tableClass['model']->getSource();
				}

				$tableConfigParams = [];
				if (isset($tableClass['configParams'])) {
					$tableConfigParams = $tableClass['configParams'];
				}
				$config = $this->ff->generateConfig($tableName, $tableClass['schema'], $tableClass['model'], $tableConfigParams);
				$schema = $this->ff->generateSchema($tableName, $tableClass['schema'], $tableClass['model']);

				$this->ff->store($tableName, $config, $schema, $this->ff)->deleteStore();

				$this->ff->store($tableName, $config, $schema, $this->ff);

				if (method_exists($tableClass['schema'], 'indexes')) {
					array_push($this->storesToIndex, $tableName);
				}
			}
		}

		return true;
	}

	protected function registerRepos()
	{
		(new RegisterRepos())->register($this->db, $this->ff, $this->postData);

		return true;
	}

	protected function registerDomain()
	{
		(new RegisterDomain())->register($this->db, $this->ff, $this->request, $this->helper);

		return true;
	}

	protected function registerCore(array $baseConfig)
	{
		(new RegisterCore())->register($baseConfig, $this->db, $this->ff);

		return true;
	}

	protected function registerCoreAppType()
	{
		try {
			$jsonFile =
				$this->helper->decode(
					$this->localContent->read('apps/Core/Install/type.json'),
					true
				);
		} catch (\throwable $e) {
			throw new \Exception($e->getMessage() . '. Problem reading type.json');
		}

		return (new RegisterCoreAppType())->register($this->db, $this->ff, $jsonFile);
	}

	protected function registerCoreApp()
	{
		return (new RegisterCoreApp())->register($this->db, $this->ff, $this->helper);
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
							$this->helper->decode(
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
						$menuId = $this->registerCoreMenu($jsonFile);
					} else {
						$menuId = null;
					}

					$registeredComponentId = $this->registerCoreComponent($jsonFile, $menuId);

					if ($jsonFile['route'] === 'dashboards') {
						$this->registerCoreDashboard($jsonFile);
					}

					if (isset($jsonFile['widgets'])) {
						if (is_string($jsonFile['widgets'])) {
							$jsonFile['widgets'] = $this->helper->decode($jsonFile['widgets'], true);
						}

						if (count($jsonFile['widgets']) > 0) {
							$this->registerCoreWidgets($jsonFile, $registeredComponentId, $adminComponent);
						}
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
							$this->helper->decode(
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

					$this->registerCorePackage($jsonFile);
				}
			}
		} else if ($type === 'middlewares') {
			$adminMiddlewares = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Middlewares/', true);

			foreach ($adminMiddlewares['files'] as $adminMiddlewareKey => $adminMiddleware) {
				if (strpos($adminMiddleware, 'middleware.json')) {
					try {
						$jsonFile =
							$this->helper->decode(
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
					$this->helper->decode(
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
		} else if ($type === 'externals') {
			try {
				$composerJsonFile = $this->helper->decode(file_get_contents(base_path('external/composer.json')), true);
			} catch (\throwable $e) {
				throw new \Exception($e->getMessage() . '. Problem reading composer.json');
			}

			$this->registerCoreExternal($composerJsonFile);
		}

		return true;
	}

	protected function registerCoreComponent(array $componentFile, $menuId)
	{
		return (new RegisterComponent())->register($this->db, $this->ff, $componentFile, $menuId, $this->helper);
	}

	protected function registerCoreDashboard(array $componentFile)
	{
		return (new RegisterCoreDashboard())->register($this->db, $this->ff, $componentFile, $this->helper);
	}

	protected function registerCoreWidgets(array $componentFile, $registeredComponentId, $path)
	{
		return (new RegisterCoreWidgets())->register($this->db, $this->ff, $componentFile, $registeredComponentId, $path, $this->localContent, $this->helper);
	}

	protected function updateCoreAppComponents()
	{
		return (new RegisterCoreApp())->update($this->db, $this->ff);
	}

	protected function registerCoreMenu($componentJsonFile)
	{
		return (new RegisterMenu())->register($this->db, $this->ff, $componentJsonFile, $this->helper);
	}

	protected function registerCorePackage(array $packageFile)
	{
		return (new RegisterPackage())->register($this->db, $this->ff, $packageFile, $this->helper, $this->basepackages, $this->container, $this->postData['databasetype']);
	}

	protected function registerCoreMiddleware(array $middlewareFile)
	{
		return (new RegisterMiddleware())->register($this->db, $this->ff, $middlewareFile, $this->helper);
	}

	protected function registerCoreView(array $viewFile)
	{
		return (new RegisterView())->register($this->db, $this->ff, $viewFile, $this->helper);
	}

	protected function registerCoreExternal(array $composerJsonFile)
	{
		return (new RegisterExternal())->register($this->db, $this->ff, $composerJsonFile, $this->helper);
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
		return (new RegisterRole())->registerCoreRole($this->db, $this->ff, $this->helper);
	}

	protected function registerRegisteredUserAndGuestRoles()
	{
		return (new RegisterRole())->registerRegisteredUserAndGuestRoles($this->db, $this->ff, $this->helper);
	}

	protected function registerCoreAccount($workFactor = 12)
	{
		$password = $this->container['security']->hash($this->postData['pass'], ['cost' => $workFactor]);

		return (new RegisterRootCoreAccount())->register($this->db, $this->ff, $this->postData['email'], $password, $this->helper);
	}

	protected function registerCoreProfile()
	{
		return (new RegisterRootCoreProfile())->register($this->db, $this->ff);
	}

	protected function registerExcludeAutoGeneratedFilters()
	{
		return (new RegisterFilter())->register($this->db, $this->ff);
	}

	protected function processGeoData()
	{
		$this->progress->updateProgress('processGeoData', null, false, 'registerCountries');
		$call = $this->registerCountries();
		if ($call !== false) {
			$call = true;
		}
		$this->progress->updateProgress('processGeoData', $call, false, 'registerCountries');

		// if ($this->postData['dev'] == false) {
			$this->progress->updateProgress('processGeoData', null, false, 'downloadCountriesStateAndCities');
			$call = $this->downloadCountriesStateAndCities();
			if ($call !== false) {
				$call = true;
			}

			$this->progress->updateProgress('processGeoData', $call, false, 'downloadCountriesStateAndCities');

			if ($call) {
				$this->progress->updateProgress('processGeoData', null, false, 'registerCountriesStateAndCities');
				$call = $this->registerCountriesStateAndCities();
				if ($call !== false) {
					$call = true;
				}
				$this->progress->updateProgress('processGeoData', $call, false, 'registerCountriesStateAndCities');
			}
		// }

		$this->progress->updateProgress('processGeoData', null, false, 'registerTimezones');
		$call = $this->registerTimezones();
		if ($call !== false) {
			$call = true;
		}
		$this->progress->updateProgress('processGeoData', $call, false, 'registerTimezones');

		return true;
	}

	protected function registerCountries()
	{
		return (new RegisterCountries())->register($this->db, $this->ff, $this->localContent, $this->helper);
	}

	protected function downloadCountriesStateAndCities()
	{
		return (new RegisterCountries())->downloadSelectedCountryStatesAndCities($this->ff, $this->localContent, $this->remoteWebContent, $this->postData['country'], $this->progress);
	}

	protected function registerCountriesStateAndCities()
	{
		return (new RegisterCountries())->registerSelectedCountryStatesAndCities($this->ff, $this->localContent, $this->remoteWebContent, $this->postData['country'], $this->postData['ip2location'], $this->helper);
	}

	protected function registerTimezones()
	{
		return (new RegisterTimezones())->register($this->db, $this->ff, $this->localContent, $this->helper);
	}

	protected function registerStorages(array $packageFile)
	{
		return (new RegisterStorages())->register($this->db, $this->ff, $packageFile, $this->helper);
	}

	protected function registerWorkers()
	{
		(new RegisterWorkers())->register($this->db, $this->ff);

		return true;
	}

	protected function registerSchedules()
	{
		(new RegisterSchedules())->register($this->db, $this->ff, $this->helper);

		return true;
	}

	protected function registerTasks()
	{
		(new RegisterTasks())->register($this->db, $this->ff, $this->postData['databasetype']);

		return true;
	}

	protected function performIndexing()
	{
		if (isset($this->postData['databasetype']) &&
			$this->postData['databasetype'] !== 'db' &&
			count($this->storesToIndex) > 0
		) {
			foreach ($this->storesToIndex as $storeToIndex) {
				$store = $this->ff->store($storeToIndex);

				$store->reIndexStore();
			}
		}

		return true;
	}

	protected function writeConfigs($coreJson = null, $writeBaseFile = false, $onlyUpdateDb = false)
	{
		if (!$this->configs) {
			$this->configs = new Configs($this->container, $this->postData, $coreJson);
		}

		if ($onlyUpdateDb) {
			$coreJson = $this->configs->write($writeBaseFile);

			if (isset($coreJson['settings']['db'])) {
				unset($coreJson['settings']['db']);
			}
			if (isset($coreJson['settings']['ff'])) {
				unset($coreJson['settings']['ff']);
			}

			$this->ff = (new Ff(
				(object) [
					'cache' => (object) [
						'enabled' => false,
						'timeout' => 0
					],
					'databaseType' => $coreJson['settings']['databasetype']
				], $this->request, $this->helper))->init(false, false);

			(new RegisterCore())->onlyUpdateDb($coreJson['settings']['dbs'], $this->helper, $this->db, $this->ff);

			return $coreJson;
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

					if ($columnsArrKey != $this->helper->lastKey($columnsArr)) {
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

	protected function createNewUser()
	{
		$checkUser = $this->executeSQL("SELECT * FROM `user` WHERE `User` LIKE ?", [$this->postData['username']]);

		if ($checkUser->numRows() === 0) {
			if (!isset($this->postData['create-username']) && !isset($this->postData['create-password'])) {
				throw new \Exception('User ' . $this->postData['username'] . ' does not exist. Please enable create new user/database.');
			}

			if ($this->postData['dev'] == false) {
				$passStrength = $this->checkPwStrength($this->postData['password']);

				if ($passStrength !== false && $passStrength <= 2) {
					throw new \Exception('DB Password strength is weak!');
				}
			}

			$this->executeSQL("CREATE USER ?@'%' IDENTIFIED WITH caching_sha2_password BY ?;", [$this->postData['username'], $this->postData['password']]);
		}

		$this->executeSQL("GRANT ALL PRIVILEGES ON " . $this->postData['dbname'] . ".* TO ?@'%' WITH GRANT OPTION;", [$this->postData['username']]);

		return true;
	}

	protected function executeComposer()
	{
		try {
			putenv('COMPOSER_HOME=' . base_path('external/'));

			$stream = fopen(base_path('external/composer.install'), 'w');
			$input = new \Symfony\Component\Console\Input\StringInput('install -d ' . base_path('external/'));
			$output = new \Symfony\Component\Console\Output\StreamOutput($stream);

			$application = new \Composer\Console\Application();
			$application->setAutoExit(false); // prevent `$application->run` method from exiting the script

			$app = $application->run($input, $output);
		} catch (\throwable $e) {
			throw $e;
		}

		if ($app !== 0) {
			return false;
		}

		return true;
	}

	protected function checkPwStrength(string $pass)
	{
		$checkingTool = new \ZxcvbnPhp\Zxcvbn();

		$result = $checkingTool->passwordStrength($pass);

		if ($result && is_array($result) && isset($result['score'])) {
			return $result['score'];
		}

		return false;
	}
}