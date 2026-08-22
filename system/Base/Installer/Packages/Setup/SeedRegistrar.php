<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup;

use Exception;
use System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis\Repos as RegisterRepos;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Countries as RegisterCountries;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Timezones as RegisterTimezones;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Account as RegisterRootCoreAccount;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Profile as RegisterRootCoreProfile;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Role as RegisterRole;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Schedules as RegisterSchedules;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Tasks as RegisterTasks;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Workers as RegisterWorkers;
use System\Base\Installer\Packages\Setup\Register\Providers\App as RegisterCoreApp;
use System\Base\Installer\Packages\Setup\Register\Providers\App\Type as RegisterCoreAppType;
use System\Base\Installer\Packages\Setup\Register\Providers\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Providers\Domain as RegisterDomain;
use Throwable;

/**
 * Seeds initial platform records (repositories, domains, core app, roles, accounts, geo, workers, schedules, tasks).
 */
class SeedRegistrar
{
    /**
     * Database connection.
     *
     * @var mixed
     */
    protected mixed $db;

    /**
     * FlatFile connection.
     *
     * @var mixed
     */
    protected mixed $ff;

    /**
     * Setup post payload.
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
     * Helpers manager instance.
     *
     * @var mixed
     */
    protected mixed $helper;

    /**
     * Flysystem local file storage adapter.
     *
     * @var mixed
     */
    protected mixed $localContent;

    /**
     * Security service instance.
     *
     * @var mixed
     */
    protected mixed $security;

    /**
     * Progress service instance.
     *
     * @var mixed
     */
    protected mixed $progress;

    /**
     * SeedRegistrar constructor.
     *
     * @param mixed                $db           Database connection.
     * @param mixed                $ff           FlatFile connection.
     * @param array<string, mixed> $postData     Setup post payload.
     * @param mixed                $request      Request instance.
     * @param mixed                $helper       Helpers instance.
     * @param mixed                $localContent Local file adapter.
     * @param mixed                $security     Security instance.
     * @param mixed                $progress     Progress instance.
     */
    public function __construct(
        mixed $db,
        mixed $ff,
        array $postData,
        mixed $request,
        mixed $helper,
        mixed $localContent,
        mixed $security = null,
        mixed $progress = null
    ) {
        $this->db = $db;
        $this->ff = $ff;
        $this->postData = $postData;
        $this->request = $request;
        $this->helper = $helper;
        $this->localContent = $localContent;
        $this->security = $security;
        $this->progress = $progress;
    }

    /**
     * Seeds initial API repository configurations.
     *
     * @return bool True on success.
     */
    public function registerRepos(): bool
    {
        (new RegisterRepos())->register($this->db, $this->ff, $this->postData);

        return true;
    }

    /**
     * Seeds default tenant domain record.
     *
     * @return bool True on success.
     */
    public function registerDomain(): bool
    {
        (new RegisterDomain())->register($this->db, $this->ff, $this->request, $this->helper);

        return true;
    }

    /**
     * Seeds core framework registration record.
     *
     * @param array<string, mixed> $baseConfig Core base configuration array.
     *
     * @return bool True on success.
     */
    public function registerCore(array $baseConfig): bool
    {
        (new RegisterCore())->register($baseConfig, $this->db, $this->ff);

        return true;
    }

    /**
     * Seeds Core App Type definition.
     *
     * @throws Exception If type.json cannot be read.
     *
     * @return mixed Registration result.
     */
    public function registerCoreAppType(): mixed
    {
        try {
            $jsonFile = $this->helper->decode(
                $this->localContent->read('apps/Core/Install/type.json'),
                true
            );
        } catch (Throwable $e) {
            throw new Exception($e->getMessage() . '. Problem reading type.json');
        }

        return (new RegisterCoreAppType())->register($this->db, $this->ff, $jsonFile);
    }

    /**
     * Seeds default Core Application record.
     *
     * @return mixed Registration result.
     */
    public function registerCoreApp(): mixed
    {
        return (new RegisterCoreApp())->register($this->db, $this->ff, $this->helper);
    }

    /**
     * Seeds default administrator role.
     *
     * @return mixed
     */
    public function registerCoreRole(): mixed
    {
        return (new RegisterRole())->registerCoreRole($this->db, $this->ff, $this->helper);
    }

    /**
     * Seeds auxiliary user roles (Super Users, Registered Users, Guests).
     *
     * @return mixed
     */
    public function registerAdditionalRoles(): mixed
    {
        return (new RegisterRole())->registerAdditionalRoles($this->db, $this->ff, $this->helper);
    }

    /**
     * Seeds initial superadmin user account.
     *
     * @param int $workFactor Bcrypt hashing work factor.
     *
     * @return mixed
     */
    public function registerCoreAccount(int $workFactor = 12): mixed
    {
        $password = $this->postData['pass'] ?? '';

        if ($this->security && method_exists($this->security, 'hash')) {
            $password = $this->security->hash($password, ['cost' => $workFactor]);
        }

        $email = $this->postData['email'] ?? 'admin@example.com';

        return (new RegisterRootCoreAccount())->register($this->db, $this->ff, $email, $password, $this->helper);
    }

    /**
     * Seeds initial superadmin user profile.
     *
     * @return mixed
     */
    public function registerCoreProfile(): mixed
    {
        $country = $this->postData['country'] ?? '';
        $timezone = $this->postData['timezone'] ?? 'UTC';

        return (new RegisterRootCoreProfile())->register($this->db, $this->ff, $country, $timezone);
    }

    /**
     * Seeds excluded auto-generated filter rules.
     *
     * @return mixed
     */
    public function registerExcludeAutoGeneratedFilters(): mixed
    {
        return (new RegisterFilter())->register($this->db, $this->ff);
    }

    /**
     * Seeds geographic countries and timezones datasets.
     *
     * @return bool True on success.
     */
    public function processGeoData(): bool
    {
        $this->progress->updateProgress('processGeoData', null, false, 'registerCountries');

        $call1 = $this->registerCountries();

        $this->progress->updateProgress('processGeoData', $call1, false, 'registerCountries');

        $call2 = $this->registerTimezones();

        $this->progress->updateProgress('processGeoData', $call2, false, 'registerTimezones');

        return true;
    }

    /**
     * Seeds ISO countries dataset.
     *
     * @return mixed
     */
    public function registerCountries(): mixed
    {
        return (new RegisterCountries())->register($this->db, $this->ff, $this->localContent, $this->helper);
    }

    /**
     * Seeds geographic timezones dataset.
     *
     * @return mixed
     */
    public function registerTimezones(): mixed
    {
        return (new RegisterTimezones())->register($this->db, $this->ff, $this->localContent, $this->helper);
    }

    /**
     * Seeds background workers.
     *
     * @return bool True on success.
     */
    public function registerWorkers(): bool
    {
        (new RegisterWorkers())->register($this->db, $this->ff);

        return true;
    }

    /**
     * Seeds default background worker schedules.
     *
     * @return bool True on success.
     */
    public function registerSchedules(): bool
    {
        (new RegisterSchedules())->register($this->db, $this->ff, $this->helper);

        return true;
    }

    /**
     * Seeds default background tasks.
     *
     * @return bool True on success.
     */
    public function registerTasks(): bool
    {
        $dbType = $this->postData['databasetype'] ?? 'hybrid';

        (new RegisterTasks())->register($this->db, $this->ff, $dbType);

        return true;
    }
}
