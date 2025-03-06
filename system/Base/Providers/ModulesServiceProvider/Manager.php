<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Installer;
use System\Base\Providers\ModulesServiceProvider\Settings;
use z4kn4fein\SemVer\Version;

class Manager extends BasePackage
{
    protected $apiClient;

    protected $apiClientConfig;

    protected $remoteModules = [];

    protected $remoteModulesJson = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $packages;

    protected $middlewares;

    protected $views;

    protected $counter = [];

    protected $settings = Settings::class;

    public function init()
    {
        return $this;
    }

    public function saveModuleSettings($data)
    {
        // $moduleMethod = 'get' . ucfirst(substr($data['module_type'], 0, -1)) . 'ById';
        $module = $this->modules->{$data['module_type']}->getById($data['module_id']);

        if ($module) {
            if ($module['app_type'] === 'core' && strtolower($module['name']) !== 'core') {
                $this->addResponse('Core package settings cannot be updated. Change settings via Core module.', 1);

                return false;
            }

            $module = array_merge($module, $data);

            try {
                $this->modules->{$data['module_type']}->update($module);
            } catch (\Exception $e) {
                $this->logger->log->debug($e->getMessage());

                $this->addResponse('Can update settings, contact administrator.', 1);
            }

            $this->addResponse('Settings updated.');
        }
    }

    public function getModuleInfo($data)
    {
        if (str_contains($data['module_type'], 'apptype')) {
            if (is_int($data['module_id'])) {
                $module = $this->apps->types->getAppTypeById($data['module_id']);
            } else if (is_string($data['module_id'])) {
                $module = $this->apps->types->getAppTypeByType($data['module_id']);
            }
        } else {
            $moduleId = $data['module_id'];

            if ($data['module_type'] === 'views' &&
                str_contains($data['module_id'], '-public')
            ) {
                $moduleId = explode('-', $data['module_id'])[0];
            }

            // $moduleMethod = 'get' . ucfirst(substr($data['module_type'], 0, -1)) . 'ById';
            $module = $this->modules->{$data['module_type']}->getById($moduleId);
        }

        if (isset($module) && is_array($module)) {
            if (isset($module['dependencies']) &&
                is_string($module['dependencies'])
            ) {
                $module['dependencies'] = $this->helper->decode($module['dependencies'], true);
            }

            if (array_key_exists('notification_subscriptions', $module)) {
                unset($module['notification_subscriptions']);
            }
            if (array_key_exists('files', $module)) {
                unset($module['files']);
            }
            if (array_key_exists('settings', $module)) {
                unset($module['settings']);
            }

            if (isset($module['installed']) &&
                $module['installed'] == '1'
            ) {
                if ($module['updated_by'] == 0) {
                    $module['updated_by'] = 'System';
                } else {
                    $module['updated_by'] = $this->basepackages->accounts->getById($module['updated_by'])['email'];
                }
            } else {
                $module['updated_by'] = 'System';
            }

            if (isset($data['sync']) && $data['sync'] == true) {
                $module = $this->updateModuleRepoDetails($module, $data);

                if (!$module) {
                    return false;
                }
            }

            if (isset($module['repo_details'])) {
                if (is_string($module['repo_details'])) {
                    try {
                        $module['repo_details'] = $this->helper->decode($module['repo_details'], true);
                    } catch (\Exception $e) {
                        $module['repo_details'] = null;
                    }
                }
            }

            //For external Composer packages
            if (isset($module['required_by'])) {
                if (is_string($module['required_by'])) {
                    try {
                        $module['required_by'] = $this->helper->decode($module['required_by'], true);

                    } catch (\Exception $e) {
                        $module['required_by'] = [];
                    }
                }

                if (count($module['required_by']) > 0) {
                    $requiredModules = [];
                    foreach ($module['required_by'] as $requiredModuleType => $requiredModuleArr) {
                        if (!isset($requiredModules[$requiredModuleType])) {
                            $requiredModules[$requiredModuleType] = [];
                        }

                        if (count($requiredModuleArr) > 0) {
                            foreach ($requiredModuleArr as $requiredModuleKey => $requiredModuleId) {
                                // $requiredModuleMethod = 'get' . ucfirst(substr($requiredModuleType, 0, -1)) . 'ById';
                                $requiredModule = $this->modules->{$requiredModuleType}->getById($requiredModuleId);

                                if ($requiredModule) {
                                    array_push($requiredModules[$requiredModuleType], $requiredModule['display_name'] ?? $requiredModule['name']);
                                }
                            }
                        }
                    }

                    $module['required_by_modules_names'] = $requiredModules;
                }
            }

            $this->addResponse('Module information success.',0, ['module' => $module]);

            return $module;
        } else {
           $this->addResponse('Module information failed.', 1, []);
        }

        return false;
    }

    public function updateModuleRepoDetails($module, $data = null)
    {
        if (isset($module['app_type']) &&
            $module['app_type'] === 'core'
        ) {
            $repo = 'core';
        } else {
            $repo = strtolower($this->helper->last(explode('/', $module['repo'])));
        }

        try {
            if (!$this->initApi($module)) {
                return false;
            }

            if ($this->apiClientConfig['id'] !== $module['api_id']) {
                $this->apiClientConfig = null;

                if (!$this->initApi($module)) {
                    return false;
                }
            }

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGet';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'ReposApi';
                $method = 'reposGet';
            }

            $args = [$this->apiClientConfig['org_user'], $repo];

            $responseArr = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if ($responseArr) {
                if (is_string($module['repo_details'])) {
                    $module['repo_details'] = $this->helper->decode($module['repo_details'], true);
                }

                $module['repo_details']['details'] = $responseArr;

                $repoArr = explode('/', $module['repo']);
                $names = explode('-', $this->helper->last($repoArr));

                if (count($names) === 1) {
                    if (!isset($module['module_type'])) {
                        $module['module_type'] = 'apptypes';
                    }
                }

                $this->remoteModules[$module['module_type']] = [$responseArr];

                $latestRelease = $this->moduleNeedsUpgrade($responseArr, $module);

                if (!$latestRelease &&
                    (isset($data['getLatestRelease']) && $data['getLatestRelease'] === true)
                ) {
                    $latestRelease = $this->getLatestRelease($module['repo_details']['details']);
                }

                if ($latestRelease) {
                    $module['repo_details']['latestRelease'] = $latestRelease;
                    $latestReleaseJson = $this->getRemoteModuleJson($module['module_type'], $module, true);

                    if ($latestReleaseJson) {
                        $module['repo_details']['latestRelease']['moduleJson'] = $this->remoteModulesJson[$module['module_type']][$module['name']];

                        if ($module['module_type'] === 'bundles') {
                            $module['update_available'] = null;
                            $module['update_version'] = null;
                            $module['installed'] = null;
                            $module['version'] = $module['repo_details']['latestRelease']['name'];
                            $module['bundle_modules'] = $module['repo_details']['latestRelease']['moduleJson']['bundle_modules'];
                        } else {
                            $module['dependencies'] = $module['repo_details']['latestRelease']['moduleJson']['dependencies'];
                            if ($module['installed'] == '1') {
                                if ($module['repo_details']['latestRelease']['name'] !== $module['version']) {
                                    $module['update_available'] = '1';
                                    $module['update_version'] = $module['repo_details']['latestRelease']['name'];
                                } else {
                                    $module['update_available'] = null;
                                    $module['update_version'] = null;
                                }
                            } else {
                                $module['version'] = $module['repo_details']['latestRelease']['name'];
                                $module['update_available'] = null;
                                $module['update_version'] = null;
                            }
                        }
                    }
                } else {
                    $module['update_available'] = '0';
                    $module['update_version'] = null;
                }

                if (str_contains($module['module_type'], 'apptype')) {
                    $this->apps->types->update($module);
                } else {
                    $this->modules->{$module['module_type']}->update($module);
                }
            } else {
                $module['repo_details'] = false;
            }

            if ($data &&
                $data['module_type'] === 'views' &&
                str_contains($data['module_id'], '-public')
            ) {
                $repo = $repo . '-public';
                $args = [$this->apiClientConfig['org_user'], $repo];

                $responseArr = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                if ($responseArr) {
                    if (is_string($module['repo_details'])) {
                        $module['repo_details'] = $this->helper->decode($module['repo_details'], true);
                    }

                    $module['repo_details']['details'] = $responseArr;

                    $latestRelease = $this->moduleNeedsUpgrade($responseArr, $module, $data);

                    if ($latestRelease) {
                        $module['repo_details']['latestRelease'] = $latestRelease;
                        if ($module['installed'] == '1') {
                            if ($module['repo_details']['latestRelease']['name'] !== $module['version']) {
                                $module['update_available'] = '1';
                                $module['update_version'] = $module['repo_details']['latestRelease']['name'];
                            } else {
                                $module['update_available'] = null;
                                $module['update_version'] = null;
                            }
                        } else {
                            $module['update_available'] = null;
                            $module['update_version'] = null;
                            $module['version'] = $module['repo_details']['latestRelease']['name'];
                        }
                    } else {
                        $module['update_available'] = null;
                        $module['update_version'] = null;
                    }
                } else {
                    $module['repo_details'] = false;
                }
            }
        } catch (ClientException | \throwable $e) {
            $this->addResponse($e->getMessage(), 2, ['module' => $module]);

            return false;
        }

        return $module;
    }

    public function getRepositoryModules($data = [])
    {
        $localModules = [];
        $sortedModules = [];

        $apis = $this->basepackages->apiClientServices->init()->getAll()->apiClientServices;

        if (!$apis ||
            ($apis && count($apis) === 0)
        ) {
            $this->addResponse('No API configured', 1);

            return false;
        }

        $modulesManagerPackage = $this->modules->packages->getPackageByName('ModulesManager');

        if (!$modulesManagerPackage) {
            $this->addResponse('Modules Manager not found in packages, contact administrator.', 1);

            return false;
        }

        if (is_string($modulesManagerPackage['settings'])) {
            $modulesManagerPackage['settings'] = $this->helper->decode($modulesManagerPackage['settings'], true);
        }

        if (!isset($modulesManagerPackage['settings']['api_clients'])) {
            $modulesManagerPackage['settings']['api_clients'] = [];
        }

        if (count($modulesManagerPackage['settings']['api_clients']) === 0) {
            $this->addResponse('Modules Manager settings does not have any API clients assigned to modules.', 1);

            return false;
        }

        foreach ($apis as $api) {
            if ($api['in_use'] == 0) {
                continue;
            }

            if (is_string($api['used_by'])) {
                $api['used_by'] = $this->helper->decode($api['used_by'], true);
            }

            if (!in_array('modules', $api['used_by']) ||
                !in_array($api['id'], $modulesManagerPackage['settings']['api_clients'])
            ) {
                continue;
            }

            if (strtolower($api['category']) === 'repos') {
                $apiClient = $this->basepackages->apiClientServices->useApi($api['id']);
                $apiClientConfig = $apiClient->getApiConfig();

                $sortedModules[$api['id']] = [];
                $sortedModules[$api['id']]['childs'] = [];
                $sortedModules[$api['id']]['name'] = $apiClientConfig['name'];
                $sortedModules[$api['id']]['data']['type'] = 'repo';
                $sortedModules[$api['id']]['data']['apiid'] = $apiClientConfig['id'];
            }
        }

        if (count($sortedModules) === 0) {
            $this->addResponse('Modules Manager settings does not have any API clients assigned to modules.', 1);

            return false;
        }

        if (isset($data['api_id'])) {
            $localModules['components'] = msort($this->modules->components->init(true)->getComponentsByApiId($data['api_id']), 'name');
            $localModules['middlewares'] = msort($this->modules->middlewares->init(true)->getMiddlewaresByApiId($data['api_id']), 'name');
            $localModules['packages'] = msort($this->modules->packages->init(true)->getPackagesByApiId($data['api_id']), 'name');
            $localModules['views'] = msort($this->modules->views->init(true)->getViewsByApiId($data['api_id']), 'name');
            $localModules['bundles'] = msort($this->modules->bundles->init(true)->getBundlesByApiId($data['api_id']), 'name');
        } else {
            $localModules['components'] = msort($this->modules->components->init(true)->components, 'name');
            $localModules['middlewares'] = msort($this->modules->middlewares->init(true)->middlewares, 'name');
            $localModules['packages'] = msort($this->modules->packages->init(true)->packages, 'name');
            $localModules['views'] = msort($this->modules->views->init(true)->views, 'name');
            $localModules['bundles'] = msort($this->modules->bundles->init(true)->bundles, 'name');
        }
        $localModules['externals'] = msort($this->modules->externals->init(true)->externals, 'developer');

        foreach ($localModules as $moduleType => $modulesArr) {
            if (count($modulesArr) > 0) {
                foreach ($modulesArr as $moduleArr) {
                    if ($moduleArr['module_type'] === 'externals') {
                        $moduleArr['api_id'] = 1;
                    }

                    if (!isset($sortedModules[$moduleArr['api_id']])) {
                        continue;
                    }

                    if (!isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['name'] = $moduleArr['app_type'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['data']['type'] = 'apptype';
                    }

                    if (!isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['name'] = $moduleArr['module_type'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['data']['type'] = 'module';
                    }

                    if (isset($moduleArr['category']) &&
                        !isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']])
                    ) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['name'] = $moduleArr['category'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['data']['type'] = 'category';
                    } else if (isset($moduleArr['developer']) &&
                               !isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['developer']])
                    ) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['developer']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['developer']]['name'] = $moduleArr['developer'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['developer']]['data']['type'] = 'developer';
                    }

                    $module['id'] = $moduleArr['id'];
                    $module['name'] = $moduleArr['name'];
                    if (isset($moduleArr['display_name'])) {
                        $module['name'] = $moduleArr['display_name'];
                    }
                    $module['data']['apiid'] = $moduleArr['api_id'];
                    $module['data']['apptype'] = $moduleArr['app_type'];
                    $module['data']['moduletype'] = $moduleArr['module_type'];
                    if ($moduleArr['module_type'] === 'externals') {
                        $module['data']['modulecategory'] = $moduleArr['developer'] ?? '-';
                    } else {
                        $module['data']['modulecategory'] = $moduleArr['category'] ?? '-';
                    }
                    $module['data']['moduleid'] = $moduleArr['module_type'] . '-' . $moduleArr['id'];
                    $module['data']['installed'] = $moduleArr['installed'] ?? 0;
                    $module['data']['update_available'] = $moduleArr['update_available'];
                    $module['data']['isnew'] = '0';
                    if (($moduleArr['module_type'] !== 'bundles' && $moduleArr['installed'] == '0') ||
                        $moduleArr['module_type'] === 'bundles'
                    ) {
                        if (isset($moduleArr['updated_on']) &&
                            ($moduleArr['updated_on'] !== null || $moduleArr['updated_on'] !== '')
                        ) {
                            try {
                                $updatedOn = Carbon::parse($moduleArr['updated_on']);
                                $days = $updatedOn->diffInDays(Carbon::now());

                                if ($days < 7) {
                                    $module['data']['isnew'] = '1';
                                }
                            } catch (\throwable $e) {
                                $module['data']['isnew'] = '0';
                            }
                        }
                    }

                    if (isset($moduleArr['category'])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$module['data']['moduleid']] = $module;
                    } else if ($moduleArr['module_type'] === 'externals') {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['developer']]['childs'][$module['data']['moduleid']] = $module;
                    } else {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$module['data']['moduleid']] = $module;
                    }
                }
            }
        }

        $this->addResponse('Ok', 0, ['modules' => $sortedModules]);

        return $sortedModules;
    }

    protected function initApi($data)
    {
        if ($this->apiClient && $this->apiClientConfig) {
            return true;
        }

        if (!isset($data['api_id'])) {
            $this->addResponse('API information not provided', 1, []);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] == '0') {
            $this->addResponse('This is local module and not remote module, cannot sync.', 1, []);

            return false;
        }

        $this->apiClient = $this->basepackages->apiClientServices->useApi($data['api_id'], true);
        $this->apiClientConfig = $this->apiClient->getApiConfig();

        if ($this->apiClientConfig['auth_type'] === 'auth' &&
            ((!$this->apiClientConfig['username'] || $this->apiClientConfig['username'] === '') &&
            (!$this->apiClientConfig['password'] || $this->apiClientConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'access_token' &&
                  (!$this->apiClientConfig['access_token'] || $this->apiClientConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'autho' &&
                  (!$this->apiClientConfig['authorization'] || $this->apiClientConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return false;
        }

        return true;
    }

    public function syncRemoteWithLocal($data)
    {
        if (!isset($data['api_id'])) {
            $this->addResponse('Please select repository', 1);

            return;
        }

        if (!$this->initApi($data)) {
            return false;
        }

        try {
            if (isset($data['import_repo'])) {
                $this->apiClientConfig['repo_url'] = $this->apiClientConfig['repo_url'] . '/' . $data['import_repo'];
                $this->apiClientConfig['sync']['modules']['last_sync'] = '';

                if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB(false, true) === true) {
                    if (isset($data['get_repository_modules']) &&
                        $data['get_repository_modules'] == 'true'
                    ) {
                        $this->getRepositoryModules();
                    } else {
                        $this->getRepositoryModules(['api_id' => $this->apiClientConfig['id']]);
                    }

                    return true;
                }
            } else {
                if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {
                    if (isset($data['get_repository_modules']) &&
                        $data['get_repository_modules'] == 'true'
                    ) {
                        $this->getRepositoryModules();
                    } else {
                        $this->getRepositoryModules(['api_id' => $this->apiClientConfig['id']]);
                    }

                    $this->apiClientConfig['sync']['modules']['last_sync'] = (\Carbon\Carbon::now())->toDateTimeLocalString();

                    $this->basepackages->apiClientServices->updateApi($this->apiClientConfig);

                    return true;
                }
            }
        } catch (ClientException | \throwable $e) {
            //To troubleshoot sync errors
            // trace([$e]);
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return false;
    }

    protected function getRemoteModules()
    {
        $this->apiClientConfig['repo_url'] = rtrim($this->apiClientConfig['repo_url'], '/');
        //Get one or core repo details, no need to loop through all repos in the org.
        if (str_ends_with($this->apiClientConfig['repo_url'], '/core')) {
            return $this->getRemoteModule('core');
        } else if (!str_ends_with($this->apiClientConfig['repo_url'], '/' . $this->apiClientConfig['org_user'])) {
            $repoUrlArr = explode('/', $this->apiClientConfig['repo_url']);

            $remoteModule = $this->getRemoteModule($this->helper->last($repoUrlArr));

            if (!$remoteModule) {
                return false;
            }

            $modulesArr = [$remoteModule];
        } else {
            //Process all repositories in the org as repourl and org name is the same
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'OrganizationApi';
                $method = 'orgListRepos';
                $args = [$this->apiClientConfig['org_user']];
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'ReposApi';
                $method = 'reposListForOrg';
                $args = [$this->apiClientConfig['org_user']];
            }

            try {
                $page = 1;
                $getRemoteModules = [];
                $modulesArr = [];
                while ($getRemoteModules !== false) {
                    $args['page'] = $page;
                    $getRemoteModules = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                    if ($getRemoteModules) {
                        $modulesArr = array_merge($modulesArr, $getRemoteModules);
                    }
                    $page++;
                }
            } catch (\throwable | ClientException $e) {
                $this->addResponse($e->getMessage(), 1);

                if ($e->getCode() === 401) {
                    $this->addResponse('API Authentication failed.', 1);
                } else if (str_contains($e->getMessage(), 'Connection timed out')) {
                    $this->addResponse('Error connecting to the repository.', 1);
                }

                return false;
            }
        }

        if ($modulesArr) {
            if ($this->apiClientConfig['sync'] &&
                isset($this->apiClientConfig['sync']['modules']['last_sync']) &&
                $this->apiClientConfig['sync']['modules']['last_sync'] !== ''
            ) {
                $lastSync = \Carbon\Carbon::parse($this->apiClientConfig['sync']['modules']['last_sync']);
            }

            foreach ($modulesArr as $key => $module) {
                if (isset($lastSync) && isset($module['updated_at']) && $module['updated_at'] !== '') {
                    $updatedAt = \Carbon\Carbon::parse($module['updated_at']);

                    if ($lastSync->greaterThan($updatedAt)) {//Only process if its updated.
                        continue;
                    }
                }

                $names = explode('-', $module['name']);

                if (strtolower($this->apiClientConfig['provider']) === 'github') {//Github does not have release_counter set
                    $collection = 'ReposApi';
                    $method = 'reposListReleases';
                    $args =
                        [
                            $this->apiClientConfig['org_user'],
                            strtolower($module['name'])
                        ];

                    try {
                        $module['release_counter'] = count($this->apiClient->useMethod($collection, $method, $args)->getResponse(true));
                    } catch (\throwable $e) {
                        $module['release_counter'] = 0;
                    }
                }

                if ($module['release_counter'] == 0) {
                    if (!isset($this->counter['noRelease']['count'])) {
                        $this->counter['noRelease']['count'] = 1;
                    } else {
                        $this->counter['noRelease']['count'] = $this->counter['noRelease']['count'] + 1;
                    }

                    $this->counter['noRelease']['api']['id'] = $this->apiClientConfig['id'];
                    $this->counter['noRelease']['api']['name'] = $this->apiClientConfig['name'];

                    if (count($modulesArr) === 1) {
                        $this->addResponse('Module has no release', 1);

                        return false;
                    } else {
                        continue;//Dont add as there are no releases.
                    }
                }

                if (count($names) === 1) {//Only Core and Apptype has no module type set
                    $names[1] = 'apptypes';
                }

                if (!$this->getRemoteModuleJson($names[1], $module)) {
                    $this->addResponse('Unable to retrieve json file for module ' . $module['name'], 1);

                    return false;
                }
            }

            $this->addResponse('Sync complete');

            return true;
        }

        $this->addResponse('Unable to sync with remote or remote has no repositories!', 1);

        return false;
    }

    protected function getRemoteModule($moduleName)
    {
        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGet';
            $args = [$this->apiClientConfig['org_user']];
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposGet';
            $args = [$this->apiClientConfig['org_user'], $moduleName];
        }

        try {
            $module = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable | ClientException $e) {
            $this->addResponse($e->getMessage(), 1);

            if ($e->getCode() === 401) {
                $this->addResponse('API Authentication failed.', 1);
            } else if (str_contains($e->getMessage(), 'Connection timed out')) {
                $this->addResponse('Error connecting to the repository.', 1);
            }

            return false;
        }

        if ($moduleName !== 'core') {
            return $module;
        }

        if ($this->apiClientConfig['sync'] &&
            isset($this->apiClientConfig['sync']['modules']['last_sync']) &&
            $this->apiClientConfig['sync']['modules']['last_sync'] !== ''
        ) {
            $lastSync = \Carbon\Carbon::parse($this->apiClientConfig['sync']['modules']['last_sync']);
        }

        if (isset($lastSync) && isset($module['updated_at']) && $module['updated_at'] !== '') {
            $updatedAt = \Carbon\Carbon::parse($module['updated_at']);

            if ($lastSync->greaterThan($updatedAt)) {//Only process if its updated.
                return true;
            }
        }

        $this->remoteModules['packages'] = [];
        $this->remoteModules['packages'] = [$module];

        return true;
    }

    protected function getRemoteModuleJson($moduleType, $module, $onlyJson = false)
    {
        if (str_contains($moduleType, 'apptype')) {
            $jsonFileName = 'Install/type.json';
        } else {
            if ($moduleType === 'views' || $moduleType === 'bundles') {//remove "s" from the name
                if ($moduleType === 'views' &&
                    str_contains($module['name'], '-public')
                ) {//We dont import and install -public repo.
                    return true;
                }

                $jsonFileName = substr($moduleType, 0, -1) . '.json';
            } else if ($moduleType === 'packages' && $module['name'] === 'Core') {
                $jsonFileName = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json';
            } else {
                $jsonFileName = 'Install/' . substr($moduleType, 0, -1) . '.json';
            }
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\ObjectSerializer::setUrlEncoding(false);

            $collection = 'RepositoryApi';
            $method = 'repoGetContents';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposGetContent';
        }

        if (isset($module['html_url'])) {
            $moduleRepoArr = explode('/', $module['html_url']);
        } else if (isset($module['repo'])) {
            $moduleRepoArr = explode('/', $module['repo']);
        } else {
            return true;
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $this->helper->last($moduleRepoArr),
                $jsonFileName,
                $this->apiClientConfig['branch']
            ];

        try {
            $jsonFile = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if ($jsonFile) {
                if (!isset($this->remoteModulesJson[$moduleType])) {
                    $this->remoteModulesJson[$moduleType] = [];
                }

                $this->remoteModulesJson[$moduleType][$module['name']] = $this->helper->decode(base64_decode($jsonFile['content']), true);
            }

            if (!$onlyJson) {
                if (isset($this->remoteModules[$moduleType])) {
                    array_push($this->remoteModules[$moduleType], $module);
                } else {
                    $this->remoteModules[$moduleType] = [$module];
                }
            }
        } catch (ClientException | \throwable $e) {
            $this->logger->log->debug(
                'Reading module ' . $module['name'] . ' JSON file resulted in error. ' .
                $e->getMessage()
            );

            //We dont do anything here with respect to return. If the json file is not there, we consider the module to be unavailable
        }

        return true;
    }

    protected function updateRemoteModulesToDB($onlyUpdate = true, $onlyAdd = true)
    {
        if ($this->remoteModules && count($this->remoteModules) === 0) {
            return true;
        }

        foreach ($this->remoteModules as $remoteModulesType => $remoteModules) {
            $remotePackages = $this->findRemoteInLocal($remoteModules, $remoteModulesType);

            if ($onlyUpdate && count($remotePackages['updates']) > 0) {
                if (!isset($this->counter['updates'])) {
                    $this->counter['updates'] = [];
                    $this->counter['updates']['api']['id'] = $this->apiClientConfig['id'];
                    $this->counter['updates']['api']['name'] = $this->apiClientConfig['name'];
                    $this->counter['updates']['count'] = 0;
                }

                $this->updateRemotePackages($remotePackages, $remoteModulesType);
            }

            if ($onlyAdd && count($remotePackages['new']) > 0) {
                if (!isset($this->counter['new'])) {
                    $this->counter['new'] = [];
                    $this->counter['new']['api']['id'] = $this->apiClientConfig['id'];
                    $this->counter['new']['api']['name'] = $this->apiClientConfig['name'];
                    $this->counter['new']['count'] = 0;
                }

                $this->addRemotePackages($remotePackages, $remoteModulesType);
            }
        }

        $this->packagesData->counter = $this->counter;

        return true;
    }

    protected function updateRemotePackages($remotePackages, $remoteModulesType)
    {
        foreach ($remotePackages['updates'] as $updateRemotePackageKey => $updateRemotePackage) {
            if (str_contains($remoteModulesType, 'apptype')) {
                $this->apps->types->update($updateRemotePackage);
            } else {
                $this->modules->{$remoteModulesType}->update($updateRemotePackage);
            }

            if (isset($updateRemotePackage['installed']) &&
                $updateRemotePackage['installed'] == '1' &&
                $updateRemotePackage['update_available'] == '1'
            ) {
                $this->counter['updates']['count'] = $this->counter['updates']['count'] + 1;
            }
        }
    }

    protected function addRemotePackages($remotePackages, $remoteModulesType)
    {
        foreach ($remotePackages['new'] as $registerRemotePackageKey => $registerRemotePackage) {
            if ($registerRemotePackage['repo_details']['latestRelease'] === false) {
                continue;
            }

            $repo_details = $registerRemotePackage['repo_details'];
            $version = $registerRemotePackage['repo_details']['latestRelease']['tag_name'];

            $registerRemotePackage = $this->remoteModulesJson[$remoteModulesType][$registerRemotePackage['name']];
            $registerRemotePackage['repo_details'] = $repo_details;
            $registerRemotePackage['version'] = $version;

            $registerRemotePackage['settings'] =
                isset($registerRemotePackage['settings']) ?
                $this->helper->encode($registerRemotePackage['settings']) :
                $this->helper->encode([]);

            $registerRemotePackage['apps'] = $this->helper->encode([]);

            $registerRemotePackage['installed'] = 0;

            if ($this->access->auth->account()) {
                $registerRemotePackage['updated_by'] = $this->access->auth->account()['id'];
            } else {
                $registerRemotePackage['updated_by'] = 0;
            }

            $registerRemotePackage['api_id'] = $this->apiClientConfig['id'];

            if (str_contains($remoteModulesType, 'apptype')) {
                $this->apps->types->add($registerRemotePackage);
            } else {
                if ($registerRemotePackage['module_type'] === 'components') {
                    if (!isset($registerRemotePackage['menu']) ||
                        (isset($registerRemotePackage['menu']) && is_bool($registerRemotePackage['menu']))
                    ) {
                        $registerRemotePackage['menu'] = 'false';
                    }
                } else if ($registerRemotePackage['module_type'] === 'views') {
                    if (array_key_exists('is_subview', $registerRemotePackage) && $registerRemotePackage['is_subview'] == false) {
                        $registerRemotePackage['base_view_module_id'] = 0;
                    } else if (array_key_exists('is_subview', $registerRemotePackage) && $registerRemotePackage['is_subview'] == true) {
                        $baseView = $this->modules->views->getViewByRepo($registerRemotePackage['dependencies']['views'][0]['repo']);

                        if ($baseView) {//Add Baseview ID here or during installation.
                            $registerRemotePackage['base_view_module_id'] = $baseView['id'];
                        } else {
                            $registerRemotePackage['base_view_module_id'] = 0;
                        }
                    } else {
                        $registerRemotePackage['base_view_module_id'] = 0;
                    }
                }

                $this->modules->{$remoteModulesType}->add($registerRemotePackage);
            }

            $this->counter['new']['count'] = $this->counter['new']['count'] + 1;
        }
    }

    protected function findRemoteInLocal($remoteModules, $remoteModulesType)
    {
        $modules = [];
        $modules['updates'] = [];
        $modules['new'] = [];

        foreach ($remoteModules as $remoteModuleKey => $remoteModule) {
            if (isset($remoteModule['repo'])) {
                $repoUrl = $remoteModule['repo'];
            } else if (isset($remoteModule['html_url'])) {
                $repoUrl = $remoteModule['html_url'];
            }

            if (str_contains($remoteModulesType, 'apptype')) {
                $localModule = $this->apps->types->getAppTypeByRepo($repoUrl);
            } else {
                $moduleMethod = 'get' . ucfirst(substr($remoteModulesType, 0, -1)) . 'ByRepo';
                $localModule = $this->modules->{$remoteModulesType}->$moduleMethod($repoUrl);
            }

            if ($localModule) {
                $localModule['repo_details'] = [];
                $localModule['repo_details']['details'] = $remoteModule;

                $moduleNeedsUpgrade = $this->moduleNeedsUpgrade($remoteModule, $localModule);

                if ($moduleNeedsUpgrade) {
                    $localModule['repo_details']['latestRelease'] = $moduleNeedsUpgrade;

                    if ($this->getRemoteModuleJson($remoteModulesType, $localModule, true)) {
                        if (isset($this->remoteModulesJson[$remoteModulesType][$remoteModule['name']])) {
                            $localModuleVersion = $localModule['version'];
                            $localModule = array_merge($localModule, $this->remoteModulesJson[$remoteModulesType][$remoteModule['name']]);
                            $localModule['version'] = $localModuleVersion;
                            $localModule['repo_details']['latestRelease']['moduleJson'] = $this->remoteModulesJson[$remoteModulesType][$remoteModule['name']];
                        }
                    }

                    if (isset($localModule['installed']) && $localModule['installed'] == '0') {
                        $localModule['version'] = $moduleNeedsUpgrade['name'];
                        $localModule['update_available'] = '0';
                        $localModule['update_version'] = null;
                    } else if (isset($localModule['installed']) && $localModule['installed'] == '1') {
                        $localModule['update_available'] = '1';
                        $localModule['update_version'] = $moduleNeedsUpgrade['name'];
                    }

                    $modules['updates'][$localModule['id']] = $localModule;
                } else {
                    if (isset($localModule['update_available']) && $localModule['update_available'] == 1) {
                        $localModule['update_available'] = '0';
                        $localModule['update_version'] = null;

                        $modules['updates'][$localModule['id']] = $localModule;
                    }
                }

                unset($remoteModules[$remoteModuleKey]);
            } else {
                $remoteModule['repo_details'] = [];
                $remoteModule['repo_details']['details'] = $remoteModule;
                $remoteModule['repo_details']['latestRelease'] = $this->moduleNeedsUpgrade($remoteModule);

                $modules['new'][$remoteModuleKey] = $remoteModule;
            }
        }

        return $modules;
    }

    protected function moduleNeedsUpgrade($remoteModule, $localModule = null, $data = null)
    {
        if ($localModule) {
            $repo = strtolower($this->helper->last(explode('/', $localModule['repo'])));

            if ($data &&
                $data['module_type'] === 'views' &&
                str_contains($data['module_id'], '-public')
            ) {
                $repo = $repo . '-public';
            }

            if (array_key_exists('level_of_update', $localModule) &&
                $localModule['level_of_update'] == '4'
            ) {
                try {
                    if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                        $collection = 'RepositoryApi';
                        $method = 'repoListReleases';
                    } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                        $collection = 'ReposApi';
                        $method = 'reposListReleases';
                    }

                    $args =
                        [
                            $this->apiClientConfig['org_user'],
                            $repo
                        ];

                    $releases = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                } catch (\Exception $e) {
                    $latestRelease = $this->getLatestRelease($remoteModule, $data);
                }

                if ($releases && count($releases) > 0) {
                    foreach ($releases as $release) {
                        if (isset($release['draft']) && $release['draft'] == 'true') {
                            continue;
                        }

                        $latestRelease = $release;

                        break;
                    }
                }
            } else {
                $latestRelease = $this->getLatestRelease($remoteModule, $data);
            }
        } else {
            $latestRelease = $this->getLatestRelease($remoteModule, $data);
        }

        if (!$latestRelease) {
            return false;
        }

        if ($localModule) {
            if (array_key_exists('level_of_update', $localModule) && $localModule['level_of_update'] !== null) {
                $localVersion = Version::parse($localModule['version']);
                $latestReleaseVersion = Version::parse($latestRelease['tag_name']);

                if ($localModule['level_of_update'] == '1') {//Only Major
                    if ((int) $latestReleaseVersion->getMajor() > (int) $localVersion->getMajor()) {
                        return $latestRelease;
                    }
                } else if ($localModule['level_of_update'] == '2') {//Major & Minor
                    if ((int) $latestReleaseVersion->getMinor() > (int) $localVersion->getMinor() ||
                        (int) $latestReleaseVersion->getMajor() > (int) $localVersion->getMajor()
                    ) {
                        return $latestRelease;
                    }
                } else if ($localModule['level_of_update'] == '3') {//Major & Minor & Patch
                    if ((int) $latestReleaseVersion->getMajor() > (int) $localVersion->getMajor() ||
                        (int) $latestReleaseVersion->getPatch() > (int) $localVersion->getPatch() ||
                        (int) $latestReleaseVersion->getMinor() > (int) $localVersion->getMinor()
                    ) {
                        return $latestRelease;
                    }
                } else if ($localModule['level_of_update'] == '4') {//pre-release
                    if (Version::greaterThan($latestRelease['tag_name'], $localModule['version'])) {
                        return $latestRelease;
                    }
                }

                return false;
            } else {
                if (Version::greaterThan($latestRelease['tag_name'], $localModule['version'])) {
                    return $latestRelease;
                }
                if ($data &&
                    $data['module_type'] === 'views' &&
                    str_contains($data['module_id'], '-public')
                ) {
                    return $latestRelease;
                }
            }
        } else {
            return $latestRelease;
        }
    }

    protected function getLatestRelease($remoteModule, $data = null)
    {
        try {
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGetLatestRelease';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'ReposApi';
                $method = 'reposGetLatestRelease';
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($remoteModule['name'])
                ];

            return $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                return false;
            }

            $this->addResponse($e->getMessage(), 1);
        }
    }

    public function getAvailableApis($getAll = false, $returnApis = true)
    {
        $apisArr = [];

        if (!$getAll) {
            $package = $this->getPackage();
            if (isset($package['settings']) &&
                isset($package['settings']['api_clients']) &&
                is_array($package['settings']['api_clients']) &&
                count($package['settings']['api_clients']) > 0
            ) {
                foreach ($package['settings']['api_clients'] as $key => $clientId) {
                    $client = $this->basepackages->apiClientServices->getApiById($clientId);

                    if ($client) {
                        array_push($apisArr, $client);
                    }
                }
            }
        } else {
            $apisArr = $this->basepackages->apiClientServices->getApiByAppType();
        }

        if (count($apisArr) > 0) {
            foreach ($apisArr as $api) {
                if ($api['category'] === 'repos') {
                    $useApi = $this->basepackages->apiClientServices->useApi([
                            'config' =>
                                [
                                    'id'           => $api['id'],
                                    'category'     => $api['category'],
                                    'provider'     => $api['provider'],
                                    'checkOnly'    => true//Set this to check if the API exists and can be instantiated.
                                ]
                        ]);

                    if ($useApi) {
                        $apiConfig = $useApi->getApiConfig();

                        $apis[$api['id']]['id'] = $apiConfig['id'];
                        $apis[$api['id']]['name'] = $apiConfig['name'];
                        $apis[$api['id']]['data']['url'] = $apiConfig['repo_url'];
                    }
                }
            }
        }

        if ($returnApis) {
            return $apis ?? [];
        }

        return $apisArr;
    }
}