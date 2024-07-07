<?php

namespace System\Base\Installer\Components;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\View\Simple;
use System\Base\Installer\Packages\Setup as SetupPackage;
use System\Base\Providers\BasepackagesServiceProvider\Basepackages;
use System\Base\Providers\CacheServiceProvider\OpCache;
use System\Base\Providers\ContentServiceProvider\Local\Content as LocalContent;
use System\Base\Providers\ContentServiceProvider\RemoteWeb\Content as RemoteWebContent;
use System\Base\Providers\SecurityServiceProvider\Crypt;
use System\Base\Providers\SecurityServiceProvider\Random;
use System\Base\Providers\SecurityServiceProvider\Security;
use System\Base\Providers\SessionServiceProvider\Session;
use System\Base\Providers\SupportServiceProvider\Helper;
use System\Base\Providers\ValidationServiceProvider\Validation;
use System\Base\Providers\WebSocketServiceProvider\Wss;

Class Setup
{
	private $container;

	private $setupPackage;

	private $view;

	private $localContent;

	protected $coreJson;

	protected $config;

	protected $request;

	protected $response;

	protected $postData;

	protected $security;

	protected $random;

	protected $session;

	protected $cookies;

	protected $basepackages;

	protected $helper;

	protected $progress;

	public function __construct($session, $configsObj, $onlyUpdateDb = false)
	{
		try {
			$container = new FactoryDefault();

			$container->setShared(
				'view',
				function () {
					$view = new Simple();

					$view->setViewsDir(base_path('system/Base/Installer/View/'));

					return $view;
				}
			);

			$container->setShared(
				'basepackages',
				function () {
					return new Basepackages();
				}
			);

			$container->setShared(
				'validation',
				function () {
					return (new Validation())->init();
				}
			);

			$container->setShared(
				'security',
				function () {
					return (new Security())->init();
				}
			);

			$container->setShared(
				'crypt',
				function () {
					return (new Crypt())->init();
				}
			);

			$container->setShared(
				'random',
				function () {
					return (new Random())->init();
				}
			);

			$container->setShared(
				'cookies',
				function () {
					return new Cookies();
				}
			);

			if (extension_loaded('Zend OPcache')) {
				$container->setShared(
					'opCache',
					function () {
						return (new OpCache())->init();
					}
				);
			} else {
				$container->setShared(
					'opCache',
					function () {
						return false;
					}
				);
			}

			$container->setShared(
				'helper',
				function () {
					return (new Helper())->init();
				}
			);

			$container->setShared(
				'wss',
				function () use ($configsObj, $container) {
					return (new Wss($configsObj, $container->getShared('helper')))->init();
				}
			);

			$container->setShared(
				'localContent',
				function () {
					return (new LocalContent())->init();
				}
			);

			$container->setShared(
				'remoteWebContent',
				function () {
					return (new RemoteWebContent())->init();
				}
			);

			$container->setShared('session', $session);

			$this->container = $container;

			$this->response = $this->container->getShared('response');
			$this->response->setContentType('application/json', 'UTF-8');
			$this->response->setHeader('Cache-Control', 'no-store');

			$this->request = $this->container->getShared('request');
			$this->postData = $this->request->getPost();

			$this->security = $this->container->getShared('security');
			$this->random = $this->container->getShared('random');
			$this->session = $this->container->getShared('session');
			$this->cookies = $this->container->getShared('cookies');

			$this->view = $this->container->getShared('view');

			$this->basepackages = $this->container->getShared('basepackages');

			$this->helper = $this->container->getShared('helper');

			if ($onlyUpdateDb === false) {
				$this->progress = $this->basepackages->progress->init($this->container);
			}

			$this->config = $configsObj->toArray();

			$this->localContent = $this->container->getShared('localContent');
		} catch (\throwable $e) {
			if (strpos($e->getMessage(), 'Class') !== false) {
				if ($this->request->isGet()) {
					$this->populateComposerJsonFile();
				}

				$this->renderView(true);

				exit;
			}

			throw $e;
		}
	}

	public function run($onlyUpdateDb = false, $message = null)
	{
		try {
			if (!isset($this->postData['session'])) {
				$this->setupPackage = new SetupPackage($this->container, $this->postData, false, $onlyUpdateDb);

				if (!$onlyUpdateDb) {
					if ($this->progress->checkProgressFile()) {
						$this->progress->deleteProgressFile();
					}

					$this->registerProgressMethods();
				}
			}
		} catch (\Exception $e) {
			if (!$onlyUpdateDb) {
				$this->progress->preCheckComplete(false);

				$this->progress->resetProgress();
			}

			$this->view->responseCode = 1;

			$message = $e->getMessage();

			if (str_contains($message, "SQLSTATE[HY000] [2002] No such file or directory")) {
				$message = 'Database not available on entered Host : ' . $e->getMessage();
			} else if (str_contains($message, "SQLSTATE[HY000] [2002] Connection refused")) {
				$message = 'Database not available on entered Port : ' . $e->getMessage();
			} else if (str_contains($message, "SQLSTATE[HY000] [1044]")) {
				$message = 'Database not available on server : ' . $e->getMessage();
			} else if (str_contains($message, "SQLSTATE[HY000] [1045]")) {
				$message = 'Authentication Error : ' . $e->getMessage();
			}

			$this->view->responseMessage = $message;

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		}

		if ($this->request->isPost() && !isset($this->postData['session'])) {
			if (isset($this->postData['dev']) && $this->postData['dev'] != 'true') {
				$passStrength = $this->checkPwStrength($this->postData['pass']);

				if ($passStrength !== false && $passStrength <= 2) {
					$this->view->responseCode = 1;

					$this->view->responseMessage = 'User Password strength is weak!';

					$this->progress->resetProgress();

					if ($this->response->isSent() !== true) {
						$this->response->setJsonContent($this->view->getParamsToView());

						return $this->response->send();
					}
				}
			} else if (isset($this->postData['dev']) && $this->postData['dev'] == 'true') {
				// $this->progress->unregisterMethods(['downloadCountriesStateAndCities', 'registerCountriesStateAndCities']);
			}

			if (!$onlyUpdateDb) {
				$validateData = $this->setupPackage->validateData();

				if ($validateData !== true) {
					$this->progress->preCheckComplete(false);

					$this->view->responseCode = 1;

					$this->view->responseMessage = $validateData;

					if ($this->response->isSent() !== true) {
						$this->response->setJsonContent($this->view->getParamsToView());

						return $this->response->send();
					}

					return;
				}

				$createNewDb = true;
				$createNewUser = true;

				if (!isset($this->postData['create-db']) ||
					(isset($this->postData['create-db']) && $this->postData['create-db'] == 'false')
				) {
					$this->progress->unregisterMethods(['createNewDb']);
					$createNewDb = false;
				}

				if (!isset($this->postData['create-user']) ||
					(isset($this->postData['create-user']) && $this->postData['create-user'] == 'false')
				) {
					$this->progress->unregisterMethods(['createNewUser']);
					$createNewUser = false;
				}

				if ($createNewDb || $createNewUser) {
					if (isset($this->postData['create-username']) &&
						isset($this->postData['create-password'])
					) {
						$this->progress->preCheckComplete();

						try {
							if ($createNewDb) {
								$this->setupPackage->createNewDb();
							}
							if ($createNewUser) {
								$this->setupPackage->createNewUser();
							}

							unset($this->postData['create-username']);
							unset($this->postData['create-password']);

							$this->setupPackage = new SetupPackage($this->container, $this->postData, false, $onlyUpdateDb);
						} catch (\Exception $e) {
							$this->progress->preCheckComplete(false);

							$this->progress->resetProgress();

							$this->view->responseCode = 1;
							$this->view->responseMessage = $e->getMessage();

							if ($this->response->isSent() !== true) {
								$this->response->setJsonContent($this->view->getParamsToView());

								return $this->response->send();
							}
						}
					} else {
						$this->progress->resetProgress();

						$this->view->responseCode = 1;
						$this->view->responseMessage = 'Database username and password with create permission not provided.';

						if ($this->response->isSent() !== true) {
							$this->response->setJsonContent($this->view->getParamsToView());

							return $this->response->send();
						}
					}
				}
			}

			$this->coreJson =
				$this->helper->decode(
					$this->localContent->read('system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json'),
					true
				);

			if ($onlyUpdateDb) {
				if ($this->config) {
					$this->coreJson['settings'] = array_replace($this->coreJson['settings'], $this->config);
				}

				$this->setupPackage->writeConfigs($this->coreJson, true, true);

				$this->view->responseCode = 0;

				$this->view->responseMessage = 'Configuration Updated.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}

				return;
			}

			try {
				if (!$this->setupPackage->checkDbEmpty()) {
					$this->view->responseCode = 1;

					$this->view->responseMessage =
						'Database <strong>' . $this->postData['dbname'] . '</strong> not empty!' .
						' Use drop existing tables checkbox to drop existing tables.';

					$this->progress->resetProgress();

					if ($this->response->isSent() !== true) {
						$this->response->setJsonContent($this->view->getParamsToView());

						return $this->response->send();
					}
				}

				$this->progress->preCheckComplete();

				$this->setupPackage->buildSchema();

				$this->setupPackage->registerRepos();

				$this->setupPackage->registerDomain();

				$baseConfig = $this->setupPackage->writeConfigs($this->coreJson);

				$this->setupPackage->registerCore($baseConfig);

				$this->setupPackage->registerCoreAppType();

				$this->setupPackage->registerCoreApp();

				$this->setupPackage->registerModule('components');

				$this->setupPackage->updateCoreAppComponents();

				$this->setupPackage->registerModule('packages');

				$this->setupPackage->registerModule('middlewares');

				$this->setupPackage->registerModule('views');

				$this->setupPackage->registerCoreRole();

				$this->setupPackage->registerRegisteredUserAndGuestRoles();

				$this->setupPackage->registerCoreAccount($baseConfig['settings']['security']['passwordWorkFactor']);

				$this->setupPackage->registerCoreProfile();

				$this->setupPackage->registerExcludeAutoGeneratedFilters();

				$this->setupPackage->processGeoData();

				$this->setupPackage->registerWorkers();

				$this->setupPackage->registerSchedules();

				$this->setupPackage->registerTasks();

				$this->setupPackage->performIndexing();

				// $this->setupPackage->removeInstaller();

				$this->setupPackage->cleanOldAPIKeys();

				$this->setupPackage->cleanVar();

				$this->setupPackage->cleanOldBackups();

				$this->setupPackage->cleanOldCookies();

				$this->setupPackage->writeConfigs(null, true);

				$this->view->responseCode = 0;

				$this->view->responseMessage = 'Framework installed.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}
			} catch (\Exception $e) {
				$this->progress->resetProgress();

				$this->setupPackage->revertBaseConfig();

				if (isset($this->postData['dev']) && $this->postData['dev'] == 'true') {
					throw $e;
				}

				$this->view->responseCode = 1;

				$this->view->responseMessage = 'Framework installation error. Contact developers.';

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}
			}
		} else if ($this->request->isPost() && isset($this->postData['session'])) {
			if (isset($this->postData['checkPwStrength']) && isset($this->postData['pass'])) {
				$strength = $this->checkPwStrength($this->postData['pass']);

				if ($strength !== false) {
					$this->view->responseCode = 0;
					$this->view->responseMessage = 'Ok';
					if ($strength === 0) {
						$strength = 1;
					}
					$this->view->responseData = $strength;
				} else {
					$this->view->responseCode = 1;
					$this->view->responseMessage = 'Error retrieving strength.';
				}
			} else if (isset($this->postData['generatePw'])) {
				$newPass = $this->random->base62(12);

				if ($newPass) {
					$this->view->responseCode = 0;
					$this->view->responseMessage = 'Ok';
					$this->view->responseData = $newPass;
				} else {
					$this->view->responseCode = 1;
					$this->view->responseMessage = 'Error retrieving new pass.';
				}
			} else {
				$progress = $this->progress->getProgress($this->postData['session'], true);

				if ($progress) {
					$this->view->responseCode = 0;
					$this->view->responseData = $progress;
				}
			}

			$this->response->setContentType('application/json', 'UTF-8');
			$this->response->setHeader('Cache-Control', 'no-store');

			if ($this->response->isSent() !== true) {
				$this->response->setJsonContent($this->view->getParamsToView());

				return $this->response->send();
			}
		} else {
			$this->renderView(false, $onlyUpdateDb, $message);
		}
	}

	protected function registerProgressMethods()
	{
		$this->progress->registerMethods(
			[
				[
					'method'	=> 'createNewDb',
					'text'		=> 'Creating new database...'
				],
				[
					'method'	=> 'createNewUser',
					'text'		=> 'Checking database user...'
				],
				[
					'method'	=> 'checkDbEmpty',
					'text'		=> 'Checking if db is empty...'
				],
				[
					'method'	=> 'buildSchema',
					'text'		=> 'Building database schema...'
				],
				[
					'method'	=> 'registerRepos',
					'text'		=> 'Registering repositories...'
				],
				[
					'method'	=> 'registerDomain',
					'text'		=> 'Registering domain...'
				],
				[
					'method'	=> 'writeConfigs',
					'text'		=> 'Writing base configurations...'
				],
				[
					'method'	=> 'registerCore',
					'text'		=> 'Registering core...'
				],
				[
					'method'	=> 'registerCoreAppType',
					'text'		=> 'Registering core app type...'
				],
				[
					'method'	=> 'registerCoreApp',
					'text'		=> 'Registering core app...'
				],
				[
					'method'	=> 'registerModule',
					'text'		=> 'Registering components modules...'
				],
				[
					'method'	=> 'updateCoreAppComponents',
					'text'		=> 'Updating core app components...'
				],
				[
					'method'	=> 'registerModule',
					'text'		=> 'Registering packages modules...'
				],
				[
					'method'	=> 'registerModule',
					'text'		=> 'Registering middlewares modules...'
				],
				[
					'method'	=> 'registerModule',
					'text'		=> 'Registering views modules...'
				],
				[
					'method'	=> 'registerCoreRole',
					'text'		=> 'Registering core role...'
				],
				[
					'method'	=> 'registerCoreAccount',
					'text'		=> 'Registering core account...'
				],
				[
					'method'	=> 'registerCoreProfile',
					'text'		=> 'Registering core profile...'
				],
				[
					'method'	=> 'registerRegisteredUserAndGuestRoles',
					'text'		=> 'Registering additional roles...'
				],
				[
					'method'	=> 'registerExcludeAutoGeneratedFilters',
					'text'		=> 'Registering filters...'
				],
				[
					'method'	=> 'processGeoData',
					'text'		=> 'Processing geo location...',
					'childs'	=>
					[
						[
							'method'	=> 'registerCountries',
							'text'		=> 'Registering geo location: countries...'
						],
						[
							'method'	=> 'downloadCountriesStateAndCities',
							'text'		=> 'Downloading geo location: selected country\'s states and cities...',
							'remoteWeb' => true
						],
						[
							'method'	=> 'registerCountriesStateAndCities',
							'text'		=> 'Registering geo location: selected country\'s states and cities...'
						],
						[
							'method'	=> 'registerTimezones',
							'text'		=> 'Registering timezones...'
						]
					]
				],
				[
					'method'	=> 'registerWorkers',
					'text'		=> 'Registering workers...'
				],
				[
					'method'	=> 'registerSchedules',
					'text'		=> 'Registering schedules...'
				],
				[
					'method'	=> 'registerTasks',
					'text'		=> 'Registering tasks...'
				],
				[
					'method'	=> 'performIndexing',
					'text'		=> 'Indexing Database...'
				],
				[
					'method'	=> 'cleanVar',
					'text'		=> 'Cleaning variable directory...'
				],
				[
					'method'	=> 'cleanOldBackups',
					'text'		=> 'Cleaning old backups...'
				],
				[
					'method'	=> 'cleanOldCookies',
					'text'		=> 'Cleaning old cookies...'
				]
			]
		);
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

	protected function renderView($precheckFail = false, $onlyUpdateDb = false, $message = null)
	{
		if ($precheckFail) {
			if ($this->request->isPost()) {
				if (isset($this->postData['composer'])) {
					$callResult = $this->progress->getCallResult('executeComposer');

					$progress = [];

					if ($callResult === false) {
						$progress = array_merge($progress, ['composer_error' => true]);
					} else {
						$progress = $this->progress->getProgress($this->postData['session'], true);
					}

					try {
						$composerInstall = file_get_contents(base_path('external/composer.install'));

						if (strpos($composerInstall, 'curl error') !== false) {
							$progress = array_merge($progress, ['composer' => $composerInstall, 'composer_error' => true]);
						} else if (strpos($composerInstall, 'requirements could not be resolved') !== false) {
							$progress = array_merge($progress, ['composer' => $composerInstall, 'composer_error' => true]);
						} else {
							$progress = array_merge($progress, ['composer' => $composerInstall]);
						}

						if ($callResult) {
							$this->view->responseCode = 0;
							$this->view->responseMessage = 'External packages installation success!';
						} else {
							$this->view->responseCode = 1;
							$this->view->responseMessage = 'External packages installation error!';
						}

						$this->view->responseData = $progress;
					} catch (\throwable $exception) {
						$progress = array_merge($progress, ['composer' => 'Error retrieving external packages installer information...']);

						$this->view->responseCode = 1;
						$this->view->responseMessage = 'External packages installation error!';
					}
				} else {
					$this->setupPackage = new SetupPackage($this->container, $this->postData, $precheckFail);

					$this->setupPackage->executeComposer();

					do {
						$callResult = $this->progress->getCallResult('executeComposer');

						if ($callResult === false) {
							$this->progress->resetProgress();

							$this->view->responseCode = 3;

							$this->view->responseMessage = 'External packages installation error!';

							if ($this->response->isSent() !== true) {
								$this->response->setJsonContent($this->view->getParamsToView());

								return $this->response->send();
							}
						} else {
							$this->view->responseCode = 0;

							$this->view->responseMessage = 'External packages installation success!';
						}

						sleep(1);
					} while ($callResult === null);
				}

				$this->response->setContentType('application/json', 'UTF-8');
				$this->response->setHeader('Cache-Control', 'no-store');

				if ($this->response->isSent() !== true) {
					$this->response->setJsonContent($this->view->getParamsToView());

					return $this->response->send();
				}
			} else {
				if ($this->progress->checkProgressFile()) {
					$this->progress->deleteProgressFile();
				}

				$this->progress->registerMethods(
					[
						[
							'method'	=> 'executeComposer',
							'text'		=> 'Downloading & installing external packages...'
						]
					]
				);

				$this->progress->preCheckComplete();
			}
		}

		$this->cookies->useEncryption(false);

		$this->cookies->set(
			'Installer',
			$this->session->getId(),
			time() + 600,
			'/',
			null,
			null,
			true,
			[
				'samesite'	=> 'Strict'
			]
		);

		$this->cookies->send();

		$this->cookies->useEncryption(true);

		if (!$precheckFail) {
			$this->view->countries =
				$this->helper->decode(
					$this->localContent->read('/system/Base/Providers/BasepackagesServiceProvider/Packages/Geo/Data/AllCountries.json'),
					true
				);
		}

		echo $this->container->getShared('view')->render(
			'setup',
			[
				'precheckFail'	=> $precheckFail,
				'onlyUpdateDb' 	=> $onlyUpdateDb,
				'message' 		=> $message,
				'request'		=> $this->request,
				'security'		=> $this->security,
				'session'		=> $this->session
			]
		);
	}

	protected function populateComposerJsonFile()
	{
		$this->view->responseCode = 0;

		if (file_exists(base_path('external/composer.lock'))) {
			unlink(base_path('external/composer.lock'));
		}

		try {
			$composerJsonFile = $this->helper->decode(file_get_contents(base_path('external/composer.json')), true);
		} catch (\throwable $exception) {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Error reading composer json file. Please download Core again from repository.';
		}

		try {
			$coreJsonFile = $this->helper->decode(file_get_contents(base_path('system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json')), true);

			foreach ($coreJsonFile['dependencies']['composer']['require'] as $composer => $version) {
				if (!isset($composerJsonFile['require'][$composer])) {
					$composerJsonFile['require'][$composer] = $version;
				}
			}

			if (isset($coreJsonFile['dependencies']['composer']['config'])) {
				$composerJsonFile['config'] = $coreJsonFile['dependencies']['composer']['config'];
			}

			if (isset($coreJsonFile['dependencies']['composer']['extra'])) {
				if (isset($coreJsonFile['dependencies']['composer']['extra']['patches']) &&
					is_array($coreJsonFile['dependencies']['composer']['extra']['patches'])
				) {
					foreach ($coreJsonFile['dependencies']['composer']['extra']['patches'] as $package => &$patchDetails) {
						if (is_array($patchDetails)) {
							foreach ($patchDetails as $description => &$location) {
								$location = base_path($location);
							}
						}
					}
				}

				$composerJsonFile['extra'] = $coreJsonFile['dependencies']['composer']['extra'];
			}

			file_put_contents(base_path('external/composer.json'), $this->helper->encode($composerJsonFile, JSON_PRETTY_PRINT));
		} catch (\throwable $exception) {
			$this->view->responseCode = 1;

			$this->view->responseMessage = 'Error reading Core Json File. Please download Core again from repository.';
		}
	}
}