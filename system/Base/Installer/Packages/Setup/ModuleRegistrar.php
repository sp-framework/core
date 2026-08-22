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
use System\Base\Installer\Packages\Setup\Register\Basepackages\Dashboard as RegisterCoreDashboard;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Menu as RegisterMenu;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Storages\Storages as RegisterStorages;
use System\Base\Installer\Packages\Setup\Register\Basepackages\Widgets as RegisterCoreWidgets;
use System\Base\Installer\Packages\Setup\Register\Modules\Component as RegisterComponent;
use System\Base\Installer\Packages\Setup\Register\Modules\External as RegisterExternal;
use System\Base\Installer\Packages\Setup\Register\Modules\Middleware as RegisterMiddleware;
use System\Base\Installer\Packages\Setup\Register\Modules\Package as RegisterPackage;
use System\Base\Installer\Packages\Setup\Register\Modules\View as RegisterView;
use System\Base\Installer\Packages\Setup\Register\Providers\App as RegisterCoreApp;
use Throwable;

/**
 * Scans directories and registers framework modules (components, packages, middlewares, views, externals).
 */
class ModuleRegistrar
{
    /**
     * DI container.
     *
     * @var mixed
     */
    protected mixed $container;

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
     * Local content flysystem adapter.
     *
     * @var mixed
     */
    protected mixed $localContent;

    /**
     * Basepackages manager instance.
     *
     * @var mixed
     */
    protected mixed $basepackages;

    /**
     * Helpers manager instance.
     *
     * @var mixed
     */
    protected mixed $helper;

    /**
     * ModuleRegistrar constructor.
     *
     * @param mixed                $container    DI container.
     * @param mixed                $db           Database connection.
     * @param mixed                $ff           FlatFile connection.
     * @param array<string, mixed> $postData     Setup post payload.
     * @param mixed                $localContent Flysystem local adapter.
     * @param mixed                $basepackages Basepackages instance.
     * @param mixed                $helper       Helper instance.
     */
    public function __construct(
        mixed $container,
        mixed $db,
        mixed $ff,
        array $postData,
        mixed $localContent,
        mixed $basepackages,
        mixed $helper
    ) {
        $this->container = $container;
        $this->db = $db;
        $this->ff = $ff;
        $this->postData = $postData;
        $this->localContent = $localContent;
        $this->basepackages = $basepackages;
        $this->helper = $helper;
    }

    /**
     * Scans and registers application modules by type.
     *
     * @param string $type Module type (components, packages, middlewares, views, externals).
     *
     * @throws Exception If metadata JSON cannot be parsed.
     *
     * @return bool True on success.
     */
    public function registerModule(string $type): bool
    {
        $dev = ($this->postData['dev'] ?? 'true') === 'true';

        if ($type === 'components') {
            $adminComponents = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Components/', true);

            if (!$adminComponents || count($adminComponents['files'] ?? []) === 0) {
                return false;
            }

            foreach ($adminComponents['files'] as $adminComponent) {
                if (str_contains((string) $adminComponent, 'component.json')) {
                    try {
                        $jsonFile = $this->helper->decode(
                            $this->localContent->read((string) $adminComponent),
                            true
                        );
                    } catch (Throwable $e) {
                        throw new Exception($e->getMessage() . '. Problem reading component.json at location ' . $adminComponent);
                    }

                    if (isset($jsonFile['category']) && $jsonFile['category'] === 'devtools' && !$dev) {
                        continue;
                    }

                    $menuId = null;
                    $registeredComponentId = $this->registerCoreComponent($jsonFile, $menuId);

                    if (isset($jsonFile['menu']) && $jsonFile['menu'] && $jsonFile['menu'] !== 'false') {
                        $menuId = $this->registerCoreMenu($jsonFile, $registeredComponentId);
                    }

                    if ($menuId) {
                        $this->registerCoreComponent($jsonFile, $menuId, true);
                    }

                    if (isset($jsonFile['route']) && $jsonFile['route'] === 'dashboards') {
                        $this->registerCoreDashboard($jsonFile);
                    }

                    if (isset($jsonFile['widgets'])) {
                        if (is_string($jsonFile['widgets'])) {
                            $jsonFile['widgets'] = $this->helper->decode($jsonFile['widgets'], true);
                        }

                        if (is_array($jsonFile['widgets']) && count($jsonFile['widgets']) > 0) {
                            $this->registerCoreWidgets($jsonFile, $registeredComponentId, (string) $adminComponent);
                        }
                    }
                }
            }
        } elseif ($type === 'packages') {
            $adminPackages = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Packages/', true);
            $installerPackages = $this->basepackages->utils->init($this->container)->scanDir('system/Base/Installer/Packages/Setup/Register/Modules/Packages/', true);

            $allFiles = array_merge($adminPackages['files'] ?? [], $installerPackages['files'] ?? []);

            if (count($allFiles) === 0) {
                return false;
            }

            foreach ($allFiles as $adminPackage) {
                if (str_contains((string) $adminPackage, 'package.json')) {
                    try {
                        $jsonFile = $this->helper->decode(
                            $this->localContent->read((string) $adminPackage),
                            true
                        );
                    } catch (Throwable $e) {
                        throw new Exception($e->getMessage() . '. Problem reading package.json at location ' . $adminPackage);
                    }

                    if (isset($jsonFile['category']) && $jsonFile['category'] === 'devtools' && !$dev) {
                        continue;
                    }

                    if (isset($jsonFile['name']) && $jsonFile['name'] === 'Storages') {
                        $this->registerStorages($jsonFile);
                    }

                    $this->registerCorePackage($jsonFile);
                }
            }
        } elseif ($type === 'middlewares') {
            $adminMiddlewares = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Middlewares/', true);

            foreach ($adminMiddlewares['files'] ?? [] as $adminMiddleware) {
                if (str_contains((string) $adminMiddleware, 'middleware.json')) {
                    try {
                        $jsonFile = $this->helper->decode(
                            $this->localContent->read((string) $adminMiddleware),
                            true
                        );
                    } catch (Throwable $e) {
                        throw new Exception($e->getMessage() . '. Problem reading middleware.json at location ' . $adminMiddleware);
                    }

                    if (isset($jsonFile['category']) && $jsonFile['category'] === 'devtools' && !$dev) {
                        continue;
                    }

                    $this->registerCoreMiddleware($jsonFile);
                }
            }
        } elseif ($type === 'views') {
            try {
                $jsonFile = $this->helper->decode(
                    $this->localContent->read('apps/Core/Views/Default/view.json'),
                    true
                );
            } catch (Throwable $e) {
                throw new Exception($e->getMessage() . '. Problem reading view.json');
            }

            if (isset($jsonFile['category']) && $jsonFile['category'] === 'devtools' && !$dev) {
                return true;
            }

            $this->registerCoreView($jsonFile);
        } elseif ($type === 'externals') {
            $externalPath = base_path('external/composer.json');
            if (file_exists($externalPath)) {
                try {
                    $composerJsonFile = $this->helper->decode((string) file_get_contents($externalPath), true);
                    $this->registerCoreExternal($composerJsonFile);
                } catch (Throwable $e) {
                    throw new Exception($e->getMessage() . '. Problem reading composer.json');
                }
            }
        }

        return true;
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
    public function registerCoreComponent(array $componentFile, ?int $menuId = null, bool $update = false): mixed
    {
        if ($update) {
            return (new RegisterComponent())->update($this->db, $this->ff, $componentFile, $menuId);
        }

        return (new RegisterComponent())->register($this->db, $this->ff, $componentFile, $menuId, $this->helper);
    }

    /**
     * Seeds core dashboard record.
     *
     * @param array<string, mixed> $componentFile Component metadata.
     *
     * @return mixed
     */
    public function registerCoreDashboard(array $componentFile): mixed
    {
        return (new RegisterCoreDashboard())->register($this->db, $this->ff, $componentFile, $this->helper);
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
    public function registerCoreWidgets(array $componentFile, mixed $registeredComponentId, string $path): mixed
    {
        return (new RegisterCoreWidgets())->register($this->db, $this->ff, $componentFile, $registeredComponentId, $path, $this->localContent, $this->helper);
    }

    /**
     * Updates core app components mapping.
     *
     * @return mixed
     */
    public function updateCoreAppComponents(): mixed
    {
        return (new RegisterCoreApp())->update($this->db, $this->ff);
    }

    /**
     * Seeds navigation menu item.
     *
     * @param array<string, mixed> $componentJsonFile     Component metadata.
     * @param mixed                $registeredComponentId Component ID.
     *
     * @return mixed Menu ID.
     */
    public function registerCoreMenu(array $componentJsonFile, mixed $registeredComponentId): mixed
    {
        return (new RegisterMenu())->register($this->db, $this->ff, $componentJsonFile, $this->helper, $registeredComponentId);
    }

    /**
     * Seeds package record.
     *
     * @param array<string, mixed> $packageFile Package metadata.
     *
     * @return mixed
     */
    public function registerCorePackage(array $packageFile): mixed
    {
        $dbType = $this->postData['databasetype'] ?? 'hybrid';

        return (new RegisterPackage())->register($this->db, $this->ff, $packageFile, $this->helper, $this->basepackages, $this->container, $dbType);
    }

    /**
     * Seeds middleware record.
     *
     * @param array<string, mixed> $middlewareFile Middleware metadata.
     *
     * @return mixed
     */
    public function registerCoreMiddleware(array $middlewareFile): mixed
    {
        return (new RegisterMiddleware())->register($this->db, $this->ff, $middlewareFile, $this->helper);
    }

    /**
     * Seeds view theme record.
     *
     * @param array<string, mixed> $viewFile View metadata.
     *
     * @return mixed
     */
    public function registerCoreView(array $viewFile): mixed
    {
        return (new RegisterView())->register($this->db, $this->ff, $viewFile, $this->helper);
    }

    /**
     * Seeds external library dependencies record.
     *
     * @param array<string, mixed> $composerJsonFile Composer metadata.
     *
     * @return mixed
     */
    public function registerCoreExternal(array $composerJsonFile): mixed
    {
        return (new RegisterExternal())->register($this->db, $this->ff, $composerJsonFile, $this->helper);
    }

    /**
     * Seeds default public and private file storage mounts.
     *
     * @param array<string, mixed> $packageFile Package metadata.
     *
     * @return mixed
     */
    public function registerStorages(array $packageFile): mixed
    {
        return (new RegisterStorages())->register($this->db, $this->ff, $packageFile, $this->helper);
    }
}
