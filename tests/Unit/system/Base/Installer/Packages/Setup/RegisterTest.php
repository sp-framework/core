<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace Tests\Unit\System\Base\Installer\Packages\Setup;

use Codeception\Test\Unit;
use System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis\Repos as RegisterRepos;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Dashboard as RegisterDashboard;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Filter as RegisterFilter;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Geo\Timezones as RegisterTimezones;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Storages\Storages as RegisterStorages;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Account as RegisterAccount;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Profile as RegisterProfile;
use System\Base\Installer\Packages\Setup\Register\Basepackages\User\Role as RegisterRole;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Schedules as RegisterSchedules;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Tasks as RegisterTasks;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Workers\Workers as RegisterWorkers;
use System\Base\Installer\Packages\Setup\Register\Modules\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Modules\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Modules\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Modules\View as RegisterView;
use System\Base\Installer\Packages\Setup\Register\Providers\App as RegisterApp;
use System\Base\Installer\Packages\Setup\Register\Providers\App\Type as RegisterAppType;
use System\Base\Installer\Packages\Setup\Register\Providers\Core as RegisterCore;
use System\Base\Installer\Packages\Setup\Register\Providers\Domain as RegisterDomain;

/**
 * Unit test suite for Setup Register seeders.
 *
 * @package Tests\Unit\System\Base\Installer\Packages\Setup
 */
class RegisterTest extends Unit
{
    protected array $dbInserted = [];
    protected array $ffInserted = [];

    protected function _before(): void
    {
        $this->dbInserted = [];
        $this->ffInserted = [];
    }

    /**
     * Creates mock DB adapter.
     */
    protected function createMockDb(): object
    {
        $dbInserted = &$this->dbInserted;

        return new class ($dbInserted) {
            public $inserted;
            public function __construct(&$inserted) { $this->inserted = &$inserted; }
            public function insertAsDict(string $table, array $data): void
            {
                $this->inserted[$table][] = $data;
            }
            public function lastInsertId(): int { return 1; }
            public function fetchAll(string $sql, int $mode = 2, array $bind = []): array
            {
                return [['id' => 1, 'name' => 'Core', 'class' => 'Apps\\Core\\Components\\Home\\HomeComponent', 'settings' => '{}', 'route' => 'home']];
            }
            public function updateAsDict(string $table, array $data, string $where): void {}
        };
    }

    /**
     * Creates mock FlatFile manager.
     */
    protected function createMockFf(): object
    {
        $ffInserted = &$this->ffInserted;

        return new class ($ffInserted) {
            public $inserted;
            public function __construct(&$inserted) { $this->inserted = &$inserted; }
            public function store(string $tableName): object
            {
                $inserted = &$this->inserted;
                return new class ($tableName, $inserted) {
                    public $table;
                    public $ins;
                    public function __construct($table, &$ins) { $this->table = $table; $this->ins = &$ins; }
                    public function updateOrInsert(array $data): void { $this->ins[$this->table][] = $data; }
                    public function getLastInsertedId(): int { return 1; }
                    public function findById(string $id): array { return ['id' => 1, 'settings' => []]; }
                    public function findOneBy(array $query): array { return ['id' => 1, 'class' => 'Apps\\Core\\Components\\Home\\HomeComponent']; }
                    public function findBy(array $query): array { return [['id' => 1, 'name' => 'ProcessEmailQueue']]; }
                    public function setValidateData(bool $flag): void {}
                };
            }
        };
    }

    /**
     * Creates mock Helper.
     */
    protected function createMockHelper(): object
    {
        return new class {
            public function encode(mixed $val): string { return (string) json_encode($val); }
            public function decode(string $val, bool $assoc = true): mixed { return json_decode($val, $assoc); }
        };
    }

    /**
     * Tests App, Type, Core, and Domain provider registration.
     */
    public function testProviderRegistrations(): void
    {
        $db = $this->createMockDb();
        $ff = $this->createMockFf();
        $helper = $this->createMockHelper();

        (new RegisterApp())->register($db, $ff, $helper);
        $this->assertArrayHasKey('service_provider_apps', $this->dbInserted);
        $this->assertArrayHasKey('service_provider_apps', $this->ffInserted);

        (new RegisterAppType())->register($db, $ff, ['name' => 'Core', 'app_type' => 'core']);
        $this->assertArrayHasKey('service_provider_apps_types', $this->dbInserted);

        (new RegisterCore())->register(['name' => 'Core', 'version' => '1.0.0'], $db, $ff);
        $this->assertArrayHasKey('service_provider_core', $this->dbInserted);

        $mockRequest = new class {
            public function setStrictHostCheck(bool $strict): void {}
            public function getHttpHost(): string { return 'localhost'; }
            public function getServer(string $k): string { return '127.0.0.1'; }
        };
        (new RegisterDomain())->register($db, $ff, $mockRequest, $helper);
        $this->assertArrayHasKey('service_provider_domains', $this->dbInserted);
    }

    /**
     * Tests Module registration classes (Component, Middleware, Package, View).
     */
    public function testModuleRegistrations(): void
    {
        $db = $this->createMockDb();
        $ff = $this->createMockFf();
        $helper = $this->createMockHelper();

        $compResult = (new RegisterComponent())->register($db, $ff, [
            'name'        => 'Home',
            'route'       => 'home',
            'module_type' => 'components',
            'class'       => 'Apps\\Core\\Components\\Home\\HomeComponent',
        ], 1, $helper);
        $this->assertSame(1, $compResult);
        $this->assertArrayHasKey('modules_components', $this->dbInserted);

        (new RegisterMiddleware())->register($db, $ff, ['name' => 'Auth', 'class' => 'Apps\\Core\\Middlewares\\Auth'], $helper);
        $this->assertArrayHasKey('modules_middlewares', $this->dbInserted);

        (new RegisterView())->register($db, $ff, ['name' => 'Default'], $helper);
        $this->assertArrayHasKey('modules_views', $this->dbInserted);
        $this->assertArrayHasKey('modules_views_settings', $this->dbInserted);
    }

    /**
     * Tests Basepackages User Account, Profile, and Role registration.
     */
    public function testUserRegistrations(): void
    {
        $db = $this->createMockDb();
        $ff = $this->createMockFf();
        $helper = $this->createMockHelper();

        (new RegisterAccount())->register($db, $ff, 'admin@example.com', 'hashedPass', $helper);
        $this->assertArrayHasKey('basepackages_users_accounts', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_users_accounts_security', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_users_accounts_env', $this->dbInserted);

        (new RegisterProfile())->register($db, $ff, 'USA', 'America/New_York');
        $this->assertArrayHasKey('basepackages_users_profiles', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_contact_book', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_address_book', $this->dbInserted);

        (new RegisterRole())->registerCoreRole($db, $ff, $helper);
        (new RegisterRole())->registerAdditionalRoles($db, $ff, $helper);
        $this->assertArrayHasKey('basepackages_users_roles', $this->dbInserted);
    }

    /**
     * Tests Workers, Schedules, and Tasks registration.
     */
    public function testWorkersRegistrations(): void
    {
        $db = $this->createMockDb();
        $ff = $this->createMockFf();
        $helper = $this->createMockHelper();

        (new RegisterWorkers())->register($db, $ff);
        $this->assertArrayHasKey('basepackages_workers_workers', $this->dbInserted);
        $this->assertCount(100, $this->dbInserted['basepackages_workers_workers']);

        (new RegisterSchedules())->register($db, $ff, $helper);
        $this->assertArrayHasKey('basepackages_workers_schedules', $this->dbInserted);

        (new RegisterTasks())->register($db, $ff, 'hybrid');
        $this->assertArrayHasKey('basepackages_workers_tasks', $this->dbInserted);
    }

    /**
     * Tests Storages, Repos, Dashboard, Filter, Menu, and Timezones registration.
     */
    public function testAuxiliaryBasepackagesRegistrations(): void
    {
        $db = $this->createMockDb();
        $ff = $this->createMockFf();
        $helper = $this->createMockHelper();

        (new RegisterStorages())->register($db, $ff, [
            'settings' => [
                'allowedImageMimeTypes' => [['id' => 'image/png']],
                'allowedImageSizes'     => [['id' => 'medium']],
                'allowedFileMimeTypes'  => [['id' => 'application/pdf']],
            ]
        ], $helper);
        $this->assertArrayHasKey('basepackages_storages', $this->dbInserted);

        (new RegisterRepos())->register($db, $ff, ['dev' => 'true']);
        $this->assertArrayHasKey('basepackages_api_client_services_apis_repos', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_api_client_services', $this->dbInserted);

        (new RegisterDashboard())->register($db, $ff, ['settings' => []], $helper);
        $this->assertArrayHasKey('basepackages_dashboards', $this->dbInserted);
        $this->assertArrayHasKey('basepackages_dashboards_widgets', $this->dbInserted);

        (new RegisterFilter())->register($db, $ff);
        $this->assertArrayHasKey('basepackages_filters', $this->dbInserted);

        $menuId = (new RegisterMenu())->register($db, $ff, [
            'menu'     => ['Dashboard' => ['route' => 'dashboards']],
            'app_type' => 'core',
            'route'    => 'dashboards',
        ], $helper, 1);
        $this->assertSame(1, $menuId);
        $this->assertArrayHasKey('basepackages_menus', $this->dbInserted);

        $mockLocalContent = new class {
            public function read(string $path): string
            {
                return (string) json_encode([
                    ['zoneName' => 'UTC', 'tzName' => 'UTC', 'gmtOffset' => '0', 'gmtOffsetName' => 'UTC', 'abbreviation' => 'UTC']
                ]);
            }
        };
        (new RegisterTimezones())->register($db, $ff, $mockLocalContent, $helper);
        $this->assertArrayHasKey('basepackages_geo_timezones', $this->dbInserted);
    }
}
