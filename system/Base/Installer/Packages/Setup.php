<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use PDOException;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Filter\Validation\Validator\Email;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use System\Base\Installer\Packages\Setup\Cleaner;
use System\Base\Installer\Packages\Setup\ComposerRunner;
use System\Base\Installer\Packages\Setup\DatabaseProvisioner;
use System\Base\Installer\Packages\Setup\ModuleRegistrar;
use System\Base\Installer\Packages\Setup\PasswordChecker;
use System\Base\Installer\Packages\Setup\Register\Providers\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\SchemaBuilder;
use System\Base\Installer\Packages\Setup\SeedRegistrar;
use System\Base\Installer\Packages\Setup\Write\Configs;
use System\Base\Providers\DatabaseServiceProvider\Ff;

/**
 * Main Setup package orchestrator handling database schema initialization,
 * module registration, default seeds, configuration file generation, and indexing.
 *
 * Sub-components (Cleaner, DatabaseProvisioner, SchemaBuilder, ModuleRegistrar,
 * SeedRegistrar, ComposerRunner, PasswordChecker, Configs) are lazily instantiated
 * on demand to optimize memory consumption and execution speed.
 */
class Setup
{
    /**
     * Dependency injection container.
     *
     * @var mixed
     */
    protected mixed $container;

    /**
     * POST payload received from setup controller.
     *
     * @var array<string, mixed>
     */
    protected array $postData;

    /**
     * Request service instance.
     *
     * @var mixed
     */
    protected mixed $request;

    /**
     * Session service instance.
     *
     * @var mixed
     */
    protected mixed $session;

    /**
     * Phalcon MySQL PDO connection adapter instance.
     *
     * @var mixed
     */
    protected mixed $db = null;

    /**
     * FlatFile database adapter instance.
     *
     * @var mixed
     */
    protected mixed $ff = null;

    /**
     * Database configuration credentials array.
     *
     * @var array<string, mixed>
     */
    protected array $dbConfig = [];

    /**
     * Flysystem local file storage adapter.
     *
     * @var mixed
     */
    protected mixed $localContent = null;

    /**
     * Basepackages manager service instance.
     *
     * @var mixed
     */
    protected mixed $basepackages;

    /**
     * Installation progress tracker instance.
     *
     * @var mixed
     */
    protected mixed $progress = null;

    /**
     * Configs writer instance.
     *
     * @var Configs|null
     */
    protected ?Configs $configs = null;

    /**
     * Validation service instance.
     *
     * @var mixed
     */
    protected mixed $validation;

    /**
     * Security service instance.
     *
     * @var mixed
     */
    protected mixed $security;

    /**
     * Cookies service instance.
     *
     * @var mixed
     */
    protected mixed $cookies;

    /**
     * Helpers service instance.
     *
     * @var mixed
     */
    protected mixed $helper;

    /**
     * OpCache service instance.
     *
     * @var mixed
     */
    protected mixed $opCache = null;

    /**
     * Remote web content service instance.
     *
     * @var mixed
     */
    protected mixed $remoteWebContent = null;

    /**
     * Flag indicating whether execution only updates DB configuration.
     *
     * @var bool
     */
    protected bool $onlyUpdateDb = false;

    /**
     * Cleaner helper instance.
     *
     * @var Cleaner|null
     */
    protected ?Cleaner $cleaner = null;

    /**
     * Database provisioner instance.
     *
     * @var DatabaseProvisioner|null
     */
    protected ?DatabaseProvisioner $databaseProvisioner = null;

    /**
     * Schema builder instance.
     *
     * @var SchemaBuilder|null
     */
    protected ?SchemaBuilder $schemaBuilder = null;

    /**
     * Module registrar instance.
     *
     * @var ModuleRegistrar|null
     */
    protected ?ModuleRegistrar $moduleRegistrar = null;

    /**
     * Seed registrar instance.
     *
     * @var SeedRegistrar|null
     */
    protected ?SeedRegistrar $seedRegistrar = null;

    /**
     * Composer runner instance.
     *
     * @var ComposerRunner|null
     */
    protected ?ComposerRunner $composerRunner = null;

    /**
     * Password checker instance.
     *
     * @var PasswordChecker|null
     */
    protected ?PasswordChecker $passwordChecker = null;

    /**
     * Setup constructor.
     *
     * @param mixed                $container    DI container.
     * @param array<string, mixed> $postData     Post data payload.
     * @param bool                 $precheckFail Whether system requirements failed.
     * @param bool                 $onlyUpdateDb Whether only database update is being run.
     */
    public function __construct(
        mixed $container,
        array $postData = [],
        bool $precheckFail = false,
        bool $onlyUpdateDb = false
    ) {
        $this->container = $container;
        $this->postData = $postData;
        $this->onlyUpdateDb = $onlyUpdateDb;

        $this->request = $this->getService('request');
        $this->session = $this->getService('session');
        $this->validation = $this->getService('validation');
        $this->security = $this->getService('security');
        $this->cookies = $this->getService('cookies');
        $this->helper = $this->getService('helper');
        $this->opCache = $this->getService('opCache');

        $isPost = $this->request && method_exists($this->request, 'isPost') && $this->request->isPost();
        $dbType = $this->postData['databasetype'] ?? 'hybrid';
        if (($isPost && !$precheckFail && $dbType !== 'ff') || ($onlyUpdateDb && $isPost)) {
            $this->dbConfig = [
                'db' => [
                    'host'     => $this->postData['host'] ?? '',
                    'dbname'   => $this->postData['dbname'] ?? '',
                    'username' => $this->postData['username'] ?? '',
                    'password' => $this->postData['password'] ?? '',
                    'port'     => (int) ($this->postData['port'] ?? 3306),
                ]
            ];

            if (isset($this->postData['create-username'], $this->postData['create-password'])) {
                $this->dbConfig['db']['username'] = $this->postData['create-username'];
                $this->dbConfig['db']['password'] = $this->postData['create-password'];
                $this->dbConfig['db']['dbname'] = 'mysql';
            }

            if ($this->dbConfig['db']['host'] !== '') {
                $this->db = new Mysql($this->dbConfig['db']);
            }
        }

        $this->basepackages = $this->getService('basepackages');

        if (isset($this->postData['databasetype']) && $this->postData['databasetype'] !== 'db') {
            $reset = ($this->postData['databasetype'] === 'hybrid');

            $this->ff = (new Ff(
                (object) [
                    'cache' => (object) [
                        'enabled' => false,
                        'timeout' => 0
                    ],
                    'databaseType' => $this->postData['databasetype']
                ],
                $this->request,
                $this->helper
            ))->init($reset, false);
        }

        if (!$onlyUpdateDb) {
            $this->progress = $this->basepackages->progress;
        }

        if (!$precheckFail) {
            $this->localContent = $this->getService('localContent');

            $this->remoteWebContent = $this->getService('remoteWebContent');
        }
    }

    /**
     * Lazily gets Cleaner sub-component.
     *
     * @return Cleaner
     */
    public function getCleaner(): Cleaner
    {
        if ($this->cleaner === null) {
            $this->cleaner = new Cleaner(
                $this->container,
                $this->localContent,
                $this->basepackages,
                $this->opCache,
                $this->cookies,
                $this->request
            );
        }

        return $this->cleaner;
    }

    /**
     * Lazily gets DatabaseProvisioner sub-component.
     *
     * @return DatabaseProvisioner
     */
    public function getDatabaseProvisioner(): DatabaseProvisioner
    {
        if ($this->databaseProvisioner === null) {
            $this->databaseProvisioner = new DatabaseProvisioner(
                $this->db,
                $this->postData,
                $this->getPasswordChecker()
            );
        }

        return $this->databaseProvisioner;
    }

    /**
     * Lazily gets SchemaBuilder sub-component.
     *
     * @return SchemaBuilder
     */
    public function getSchemaBuilder(): SchemaBuilder
    {
        if ($this->schemaBuilder === null) {
            $this->schemaBuilder = new SchemaBuilder(
                $this->db,
                $this->ff,
                $this->postData,
                $this->dbConfig,
                $this->getCleaner(),
                $this->getDatabaseProvisioner()
            );
        }

        return $this->schemaBuilder;
    }

    /**
     * Lazily gets ModuleRegistrar sub-component.
     *
     * @return ModuleRegistrar
     */
    public function getModuleRegistrar(): ModuleRegistrar
    {
        if ($this->moduleRegistrar === null) {
            $this->moduleRegistrar = new ModuleRegistrar(
                $this->container,
                $this->db,
                $this->ff,
                $this->postData,
                $this->localContent,
                $this->basepackages,
                $this->helper
            );
        }

        return $this->moduleRegistrar;
    }

    /**
     * Lazily gets SeedRegistrar sub-component.
     *
     * @return SeedRegistrar
     */
    public function getSeedRegistrar(): SeedRegistrar
    {
        if ($this->seedRegistrar === null) {
            $this->seedRegistrar = new SeedRegistrar(
                $this->db,
                $this->ff,
                $this->postData,
                $this->request,
                $this->helper,
                $this->localContent,
                $this->security,
                $this->progress
            );
        }

        return $this->seedRegistrar;
    }

    /**
     * Lazily gets ComposerRunner sub-component.
     *
     * @return ComposerRunner
     */
    public function getComposerRunner(): ComposerRunner
    {
        if ($this->composerRunner === null) {
            $this->composerRunner = new ComposerRunner();
        }

        return $this->composerRunner;
    }

    /**
     * Lazily gets PasswordChecker sub-component.
     *
     * @return PasswordChecker
     */
    public function getPasswordChecker(): PasswordChecker
    {
        if ($this->passwordChecker === null) {
            $this->passwordChecker = new PasswordChecker();
        }

        return $this->passwordChecker;
    }

    /**
     * Proxies dynamic setup stage invocations and tracks execution progress.
     *
     * @param string              $method    Method name.
     * @param array<int, mixed>   $arguments Arguments array.
     *
     * @return mixed Method result.
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists($this, $method)) {
            if (!$this->onlyUpdateDb && $this->progress && method_exists($this->progress, 'updateProgress')) {
                $this->progress->updateProgress($method, null, false);
            }

            $call = call_user_func_array([$this, $method], $arguments);
            $callResult = $call;

            $status = ($call !== false);

            if (!$this->onlyUpdateDb && $this->progress && method_exists($this->progress, 'updateProgress')) {
                $this->progress->updateProgress($method, $status, false);
            }

            return $callResult;
        }

        return null;
    }

    /**
     * Cleans var/ runtime cache, logs, and temporary files.
     *
     * @throws FilesystemException|UnableToDeleteFile If deletion fails.
     *
     * @return bool True on success.
     */
    protected function cleanVar(): bool
    {
        return $this->getCleaner()->cleanVar();
    }

    /**
     * Purges existing FlatFile .ff/ storage directories and files.
     *
     * @throws FilesystemException|UnableToDeleteFile|UnableToDeleteDirectory If deletion fails.
     *
     * @return bool True on success.
     */
    protected function cleanOldFfs(): bool
    {
        return $this->getCleaner()->cleanOldFfs();
    }

    /**
     * Purges legacy API key directories from system/.api/.
     *
     * @throws FilesystemException|UnableToDeleteFile|UnableToDeleteDirectory If deletion fails.
     *
     * @return bool True on success.
     */
    protected function cleanOldAPIKeys(): bool
    {
        return $this->getCleaner()->cleanOldAPIKeys();
    }

    /**
     * Purges legacy database and FlatFile backup directories.
     *
     * @return bool True on success.
     */
    protected function cleanOldBackups(): bool
    {
        return $this->getCleaner()->cleanOldBackups();
    }

    /**
     * Clears authentication session cookies on domain setup.
     *
     * @return bool True on success.
     */
    protected function cleanOldCookies(): bool
    {
        return $this->getCleaner()->cleanOldCookies();
    }

    /**
     * Checks if database is empty or drops tables if requested.
     *
     * @return bool True if empty or dropped, false if non-empty and drop=false.
     */
    protected function checkDbEmpty(): bool
    {
        return $this->getDatabaseProvisioner()->checkDbEmpty();
    }

    /**
     * Builds and applies schema tables and FlatFile stores.
     *
     * @return bool True on success.
     */
    protected function buildSchema(): bool
    {
        return $this->getSchemaBuilder()->buildSchema();
    }

    /**
     * Seeds initial API repository configurations.
     *
     * @return bool True on success.
     */
    protected function registerRepos(): bool
    {
        return $this->getSeedRegistrar()->registerRepos();
    }

    /**
     * Seeds default tenant domain record.
     *
     * @return bool True on success.
     */
    protected function registerDomain(): bool
    {
        return $this->getSeedRegistrar()->registerDomain();
    }

    /**
     * Seeds core framework registration record.
     *
     * @param array<string, mixed> $baseConfig Core base configuration array.
     *
     * @return bool True on success.
     */
    protected function registerCore(array $baseConfig): bool
    {
        return $this->getSeedRegistrar()->registerCore($baseConfig);
    }

    /**
     * Seeds Core App Type definition.
     *
     * @return mixed Registration result.
     */
    protected function registerCoreAppType(): mixed
    {
        return $this->getSeedRegistrar()->registerCoreAppType();
    }

    /**
     * Seeds default Core Application record.
     *
     * @return mixed Registration result.
     */
    protected function registerCoreApp(): mixed
    {
        return $this->getSeedRegistrar()->registerCoreApp();
    }

    /**
     * Scans and registers application modules by type (components, packages, middlewares, views, externals).
     *
     * @param string $type Module type identifier.
     *
     * @return bool True on success.
     */
    protected function registerModule(string $type): bool
    {
        return $this->getModuleRegistrar()->registerModule($type);
    }

    /**
     * Seeds component record.
     *
     * @param array<string, mixed> $componentFile Component metadata.
     * @param int|null             $menuId        Associated Menu ID.
     * @param bool                 $update        Whether this is an update.
     *
     * @return mixed Component ID.
     */
    protected function registerCoreComponent(array $componentFile, ?int $menuId = null, bool $update = false): mixed
    {
        return $this->getModuleRegistrar()->registerCoreComponent($componentFile, $menuId, $update);
    }

    /**
     * Seeds core dashboard record.
     *
     * @param array<string, mixed> $componentFile Component metadata.
     *
     * @return mixed
     */
    protected function registerCoreDashboard(array $componentFile): mixed
    {
        return $this->getModuleRegistrar()->registerCoreDashboard($componentFile);
    }

    /**
     * Seeds core widgets.
     *
     * @param array<string, mixed> $componentFile         Component metadata.
     * @param mixed                $registeredComponentId Component ID.
     * @param string               $path                  File path.
     *
     * @return mixed
     */
    protected function registerCoreWidgets(array $componentFile, mixed $registeredComponentId, string $path): mixed
    {
        return $this->getModuleRegistrar()->registerCoreWidgets($componentFile, $registeredComponentId, $path);
    }

    /**
     * Updates core app components mapping.
     *
     * @return mixed
     */
    protected function updateCoreAppComponents(): mixed
    {
        return $this->getModuleRegistrar()->updateCoreAppComponents();
    }

    /**
     * Seeds navigation menu item.
     *
     * @param array<string, mixed> $componentJsonFile     Component metadata.
     * @param mixed                $registeredComponentId Component ID.
     *
     * @return mixed Menu ID.
     */
    protected function registerCoreMenu(array $componentJsonFile, mixed $registeredComponentId): mixed
    {
        return $this->getModuleRegistrar()->registerCoreMenu($componentJsonFile, $registeredComponentId);
    }

    /**
     * Seeds package record.
     *
     * @param array<string, mixed> $packageFile Package metadata.
     *
     * @return mixed
     */
    protected function registerCorePackage(array $packageFile): mixed
    {
        return $this->getModuleRegistrar()->registerCorePackage($packageFile);
    }

    /**
     * Seeds middleware record.
     *
     * @param array<string, mixed> $middlewareFile Middleware metadata.
     *
     * @return mixed
     */
    protected function registerCoreMiddleware(array $middlewareFile): mixed
    {
        return $this->getModuleRegistrar()->registerCoreMiddleware($middlewareFile);
    }

    /**
     * Seeds view theme record.
     *
     * @param array<string, mixed> $viewFile View metadata.
     *
     * @return mixed
     */
    protected function registerCoreView(array $viewFile): mixed
    {
        return $this->getModuleRegistrar()->registerCoreView($viewFile);
    }

    /**
     * Seeds external library dependencies record.
     *
     * @param array<string, mixed> $composerJsonFile Composer metadata.
     *
     * @return mixed
     */
    protected function registerCoreExternal(array $composerJsonFile): mixed
    {
        return $this->getModuleRegistrar()->registerCoreExternal($composerJsonFile);
    }

    /**
     * Validates administrator credentials payload.
     *
     * @return bool|string True if valid, or error message string.
     */
    public function validateData(): bool|string
    {
        if (!$this->validation) {
            return true;
        }

        $this->validation->add('email', Email::class, ['message' => 'Please enter valid email address.']);
        $this->validation->add('pass', PresenceOf::class, ['message' => 'Please enter a password.']);

        $messages = $this->validation->validate($this->postData);
        $validated = (is_object($messages) && method_exists($messages, 'jsonSerialize')) ? $messages->jsonSerialize() : (array) $messages;

        if (count($validated) > 0) {
            $errorStr = 'Error: ';
            foreach ($validated as $value) {
                $msg = is_array($value) ? ($value['message'] ?? '') : (string) $value;
                $errorStr .= $msg . ' ';
            }

            return trim($errorStr);
        }

        return true;
    }

    /**
     * Seeds default administrator role.
     *
     * @return mixed
     */
    protected function registerCoreRole(): mixed
    {
        return $this->getSeedRegistrar()->registerCoreRole();
    }

    /**
     * Seeds auxiliary user roles (Super Users, Registered Users, Guests).
     *
     * @return mixed
     */
    protected function registerAdditionalRoles(): mixed
    {
        return $this->getSeedRegistrar()->registerAdditionalRoles();
    }

    /**
     * Seeds initial superadmin user account.
     *
     * @param int $workFactor Bcrypt hashing work factor.
     *
     * @return mixed
     */
    protected function registerCoreAccount(int $workFactor = 12): mixed
    {
        return $this->getSeedRegistrar()->registerCoreAccount($workFactor);
    }

    /**
     * Seeds initial superadmin user profile.
     *
     * @return mixed
     */
    protected function registerCoreProfile(): mixed
    {
        return $this->getSeedRegistrar()->registerCoreProfile();
    }

    /**
     * Seeds excluded auto-generated filter rules.
     *
     * @return mixed
     */
    protected function registerExcludeAutoGeneratedFilters(): mixed
    {
        return $this->getSeedRegistrar()->registerExcludeAutoGeneratedFilters();
    }

    /**
     * Seeds geographic countries and timezones datasets.
     *
     * @return bool True on success.
     */
    protected function processGeoData(): bool
    {
        return $this->getSeedRegistrar()->processGeoData();
    }

    /**
     * Seeds ISO countries dataset.
     *
     * @return mixed
     */
    protected function registerCountries(): mixed
    {
        return $this->getSeedRegistrar()->registerCountries();
    }

    /**
     * Seeds geographic timezones dataset.
     *
     * @return mixed
     */
    protected function registerTimezones(): mixed
    {
        return $this->getSeedRegistrar()->registerTimezones();
    }

    /**
     * Seeds default public and private file storage mounts.
     *
     * @param array<string, mixed> $packageFile Package metadata.
     *
     * @return mixed
     */
    protected function registerStorages(array $packageFile): mixed
    {
        return $this->getModuleRegistrar()->registerStorages($packageFile);
    }

    /**
     * Seeds background workers.
     *
     * @return bool True on success.
     */
    protected function registerWorkers(): bool
    {
        return $this->getSeedRegistrar()->registerWorkers();
    }

    /**
     * Seeds default background worker schedules.
     *
     * @return bool True on success.
     */
    protected function registerSchedules(): bool
    {
        return $this->getSeedRegistrar()->registerSchedules();
    }

    /**
     * Seeds default background tasks.
     *
     * @return bool True on success.
     */
    protected function registerTasks(): bool
    {
        return $this->getSeedRegistrar()->registerTasks();
    }

    /**
     * Re-indexes FlatFile document stores.
     *
     * @return bool True on success.
     */
    protected function performIndexing(): bool
    {
        return $this->getSchemaBuilder()->performIndexing();
    }

    /**
     * Generates base configurations array.
     *
     * @param array<string, mixed>|null $coreJson Core metadata array.
     *
     * @return array<string, mixed>|null
     */
    protected function writeBaseConfigs(?array $coreJson = null): ?array
    {
        if (!$this->configs) {
            $this->configs = new Configs($this->container, $this->postData, $coreJson);
        }

        return $this->configs->write(false);
    }

    /**
     * Generates and persists configuration files.
     *
     * @param array<string, mixed>|null $coreJson      Core package metadata.
     * @param bool                      $writeBaseFile Whether to write Base.php.
     * @param bool                      $onlyUpdateDb  Whether this is DB-only update.
     *
     * @return array<string, mixed>|null
     */
    protected function writeConfigs(?array $coreJson = null, bool $writeBaseFile = false, bool $onlyUpdateDb = false): ?array
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

            if (class_exists(Ff::class)) {
                $this->ff = (new Ff(
                    (object) [
                        'cache' => (object) [
                            'enabled' => false,
                            'timeout' => 0
                        ],
                        'databaseType' => $coreJson['settings']['databasetype'] ?? 'hybrid'
                    ],
                    $this->request,
                    $this->helper
                ))->init(false, false);
            }

            (new RegisterCore())->onlyUpdateDb($coreJson['settings']['dbs'] ?? [], $this->helper, $this->db, $this->ff);

            return $coreJson;
        }

        return $this->configs->write($writeBaseFile);
    }

    /**
     * Reverts Base.php config to setup mode.
     *
     * @param array<string, mixed>|null $coreJson Core metadata.
     *
     * @return array<string, mixed>|null
     */
    protected function revertBaseConfig(?array $coreJson = null): ?array
    {
        if (!$this->configs) {
            $this->configs = new Configs($this->container, $this->postData, $coreJson);
        }

        return $this->configs->revert();
    }

    /**
     * Executes SQL statement against connected database.
     *
     * @param string            $sql  SQL query string.
     * @param array<int, mixed> $data Prepared statement parameters.
     *
     * @return mixed Query result.
     */
    protected function executeSQL(string $sql, array $data = []): mixed
    {
        return $this->getDatabaseProvisioner()->executeSQL($sql, $data);
    }

    /**
     * Creates new database schema if it does not exist.
     *
     * @return bool True on success.
     */
    protected function createNewDb(): bool
    {
        return $this->getDatabaseProvisioner()->createNewDb();
    }

    /**
     * Provisions new MySQL user and grants privileges.
     *
     * @return bool True on success.
     */
    protected function createNewUser(): bool
    {
        return $this->getDatabaseProvisioner()->createNewUser();
    }

    /**
     * Executes composer install for external vendor dependencies.
     *
     * @return bool True on success, false on non-zero exit code.
     */
    protected function executeComposer(): bool
    {
        return $this->getComposerRunner()->executeComposer();
    }

    /**
     * Checks password strength via zxcvbn analyzer.
     *
     * @param string $pass Password string.
     *
     * @return int|false Score from 0 to 4, or false on error.
     */
    public function checkPwStrength(string $pass): int|false
    {
        return $this->getPasswordChecker()->checkPwStrength($pass);
    }

    /**
     * Resolves service from DI container whether array or object.
     *
     * @param string $name Service name.
     *
     * @return mixed Service instance or null.
     */
    protected function getService(string $name): mixed
    {
        if (is_array($this->container) && isset($this->container[$name])) {
            return $this->container[$name];
        }

        if (is_object($this->container)) {
            if (method_exists($this->container, 'getShared')) {
                return $this->container->getShared($name);
            }
            if (method_exists($this->container, 'get')) {
                return $this->container->get($name);
            }
            if (isset($this->container->{$name})) {
                return $this->container->{$name};
            }
        }

        return null;
    }
}