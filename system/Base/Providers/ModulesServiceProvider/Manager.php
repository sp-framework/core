<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Installer;
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

    public function init()
    {
        return $this;
    }

    public function saveModuleSettings($data)
    {
        if ($data['module_type'] === 'components') {
            $module = $this->modules->components->getComponentById($data['module_id']);
        } else if ($data['module_type'] === 'packages') {
            $module = $this->modules->packages->getPackageById($data['module_id']);
        } else if ($data['module_type'] === 'middlewares') {
            $module = $this->modules->middlewares->getMiddlewareById($data['module_id']);
        } else if ($data['module_type'] === 'views') {
            $module = $this->modules->views->getViewById($data['module_id']);
        }

        if ($module) {
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
        if ($data['module_type'] === 'components') {
            $module = $this->modules->components->getComponentById($data['module_id']);
        } else if ($data['module_type'] === 'packages') {
            $module = $this->modules->packages->getPackageById($data['module_id']);
        } else if ($data['module_type'] === 'middlewares') {
            $module = $this->modules->middlewares->getMiddlewareById($data['module_id']);
        } else if ($data['module_type'] === 'views') {
            $module = $this->modules->views->getViewById($data['module_id']);
        } else if ($data['module_type'] === 'bundles') {
            $module = $this->modules->bundles->getBundleById($data['module_id']);
        }

        if (isset($module) && is_array($module)) {
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
                $module = $this->updateModuleRepoDetails($module);

                if (!$module) {
                    return false;
                }
            }

            if ($module['repo_details']) {
                if (is_string($module['repo_details'])) {
                    try {
                        $module['repo_details'] = $this->helper->decode($module['repo_details'], true);
                    } catch (\Exception $e) {
                        $module['repo_details'] = null;
                    }
                }
            }

            $this->addResponse('Module information success.',0, ['module' => $module]);

            return $module;
        } else {
           $this->addResponse('Module information failed.', 1, []);
        }

        return false;
    }

    public function updateModuleRepoDetails($module)
    {
        if ($module['app_type'] === 'core') {
            $repo = 'core';
        } else {
            $repo = strtolower($this->helper->last(explode('/', $module['repo'])));
        }

        try {
            if (!$this->initApi($module)) {
                return false;
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

                $this->remoteModules[$module['module_type']] = [$responseArr];

                $latestRelease = $this->moduleNeedsUpgrade($responseArr, $module);

                if ($latestRelease) {
                    $module['repo_details']['latestRelease'] = $latestRelease;
                    $module['update_available'] = '1';
                    $module['update_version'] = $module['repo_details']['latestRelease']['name'];
                }

                $this->modules->{$module['module_type']}->update($module);
            } else {
                $module['repo_details'] = false;
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

        foreach ($apis as $api) {
            if ($api['category'] === 'repos') {
                $this->apiClient = $this->basepackages->apiClientServices->useApi($api['id']);
                $this->apiClientConfig = $this->apiClient->getApiConfig();

                $sortedModules[$api['id']] = [];
                $sortedModules[$api['id']]['childs'] = [];
                $sortedModules[$api['id']]['name'] = $this->apiClientConfig['name'];
                $sortedModules[$api['id']]['data']['type'] = 'repo';
                $sortedModules[$api['id']]['data']['apiid'] = $this->apiClientConfig['id'];
            }
        }

        if (isset($data['api_id'])) {
            $localModules['components'] = $this->modules->components->init(true)->getComponentsByApiId($data['api_id']);
            $localModules['middlewares'] = $this->modules->middlewares->init(true)->getMiddlewaresByApiId($data['api_id']);
            $localModules['packages'] = $this->modules->packages->init(true)->getPackagesByApiId($data['api_id']);
            $localModules['views'] = $this->modules->views->init(true)->getViewsByApiId($data['api_id']);
            $localModules['bundles'] = $this->modules->bundles->init(true)->getBundlesByApiId($data['api_id']);
        } else {
            $localModules['components'] = $this->modules->components->init(true)->components;
            $localModules['middlewares'] = $this->modules->middlewares->init(true)->middlewares;
            $localModules['packages'] = $this->modules->packages->init(true)->packages;
            $localModules['views'] = $this->modules->views->init(true)->views;
            $localModules['bundles'] = $this->modules->bundles->init(true)->bundles;
        }

        foreach ($localModules as $moduleType => $modulesArr) {
            if (count($modulesArr) > 0) {
                foreach ($modulesArr as $moduleArr) {
                    if (!isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['name'] = $moduleArr['app_type'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['data']['type'] = 'app';
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
                    }

                    $module['id'] = $moduleArr['id'];
                    $module['name'] = $moduleArr['name'];
                    if (isset($moduleArr['display_name'])) {
                        $module['name'] = $moduleArr['display_name'];
                    }
                    $module['data']['apiid'] = $moduleArr['api_id'];
                    $module['data']['apptype'] = $moduleArr['app_type'];
                    $module['data']['moduletype'] = $moduleArr['module_type'];
                    $module['data']['modulecategory'] = $moduleArr['category'] ?? '-';
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
            if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {
                if (isset($data['get_repository_modules']) &&
                    $data['get_repository_modules'] == 'true'
                ) {
                    $this->getRepositoryModules();
                } else {
                    $this->getRepositoryModules(['api_id' => $this->apiClientConfig['id']]);
                }

                return true;
            }
        } catch (ClientException | \throwable $e) {
            trace([$e]);
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return false;
    }

    protected function getRemoteModules()
    {
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
            $modulesArr = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable | ClientException $e) {
            $this->addResponse($e->getMessage(), 1);

            if ($e->getCode() === 401) {
                $this->addResponse('API Authentication failed!', 1);
            }

            return false;
        }

        if ($modulesArr) {
            foreach ($modulesArr as $key => $module) {
                $names = explode('-', $module['name']);

                if ($names[0] === 'core') {
                    $this->remoteModules['packages'] = [$module];

                    return true;
                }

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
                    continue;//Dont add as there are no releases.
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

        $this->addResponse('Unable to Sync with remote server', 1);

        return false;
    }

    protected function getRemoteModuleJson($moduleType, $module, $onlyJson = false)
    {
        if ($moduleType === 'apptypes') {
            $jsonFileName = 'Install/type.json';
        } else {
            if ($moduleType === 'views' || $moduleType === 'bundles') {//remove "s" from the name
                if ($moduleType === 'views' &&
                    str_contains($module['name'], '-public')
                ) {//We dont import and install -public repo.
                    return true;
                }

                $jsonFileName = substr($moduleType, 0, -1) . '.json';
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

        $args =
            [
                $this->apiClientConfig['org_user'],
                $module['name'],
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

            //We dont so anything here with respect to return. If the json file is not there, we consider the module to be unavailable
        }

        return true;
    }

    protected function updateRemoteModulesToDB()
    {
        $counter = [];

        if ($this->remoteModules && count($this->remoteModules) === 0) {
            return true;
        }

        foreach ($this->remoteModules as $remoteModulesType => $remoteModules) {
            $remotePackages = $this->findRemoteInLocal($remoteModules, $remoteModulesType);

            if (count($remotePackages['updates']) > 0) {
                foreach ($remotePackages['updates'] as $updateRemotePackageKey => $updateRemotePackage) {
                    if (!isset($counter['updates'])) {
                        $counter['updates'] = [];
                        $counter['updates']['api']['id'] = $this->apiClientConfig['id'];
                        $counter['updates']['api']['name'] = $this->apiClientConfig['name'];
                        $counter['updates']['count'] = 0;
                    }

                    if ($remoteModulesType === 'apptypes') {
                        $this->apps->types->update($updateRemotePackage);
                    } else {
                        $this->modules->{$remoteModulesType}->update($updateRemotePackage);
                    }

                    if ($updateRemotePackage['installed'] == '1') {
                        $counter['updates']['count'] = $counter['updates']['count'] + 1;
                    }
                }
            }

            if (count($remotePackages['new']) > 0) {
                foreach ($remotePackages['new'] as $registerRemotePackageKey => $registerRemotePackage) {
                    if (!isset($counter['new'])) {
                        $counter['new'] = [];
                        $counter['new']['api']['id'] = $this->apiClientConfig['id'];
                        $counter['new']['api']['name'] = $this->apiClientConfig['name'];
                        $counter['new']['count'] = 0;
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

                    if ($this->auth->account()) {
                        $registerRemotePackage['updated_by'] = $this->auth->account()['id'];
                    } else {
                        $registerRemotePackage['updated_by'] = 0;
                    }

                    $registerRemotePackage['api_id'] = $this->apiClientConfig['id'];

                    if ($remoteModulesType === 'apptypes') {
                        $this->apps->types->add($registerRemotePackage);
                    } else {
                        if ($registerRemotePackage['module_type'] === 'views') {
                            if (count($registerRemotePackage['dependencies']['views']) === 0 &&
                                (!isset($registerRemotePackage['base_view_module_id']) ||
                                 (isset($registerRemotePackage['base_view_module_id']) && $registerRemotePackage['base_view_module_id'] === null)
                                )
                            ) {
                                $registerRemotePackage['base_view_module_id'] = 0;
                            } else if (count($registerRemotePackage['dependencies']['views']) === 1) {
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

                    $counter['new']['count'] = $counter['new']['count'] + 1;
                }
            }
        }

        $this->packagesData->counter = $counter;

        return true;
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

            if ($remoteModulesType === 'apptypes') {
                $localModule = $this->apps->types->getAppTypeByRepo($repoUrl);
            } else if ($remoteModulesType === 'components') {
                $localModule = $this->modules->components->getComponentByRepo($repoUrl);
            } else if ($remoteModulesType === 'packages') {
                $localModule = $this->modules->packages->getPackageByRepo($repoUrl);
            } else if ($remoteModulesType === 'middlewares') {
                $localModule = $this->modules->middlewares->getMiddlewareByRepo($repoUrl);
            } else if ($remoteModulesType === 'views') {
                $localModule = $this->modules->views->getViewByRepo($repoUrl);
            } else if ($remoteModulesType === 'bundles') {
                $localModule = $this->modules->bundles->getBundleByRepo($repoUrl);
            }

            if ($localModule) {
                $localModule['repo_details'] = [];
                $localModule['repo_details']['details'] = $remoteModule;

                $moduleNeedsUpgrade = $this->moduleNeedsUpgrade($remoteModule, $localModule);

                if ($moduleNeedsUpgrade) {
                    if ($this->getRemoteModuleJson($remoteModulesType, $localModule, true)) {
                        if (isset($this->remoteModulesJson[$remoteModulesType][$remoteModule['name']])) {
                            $localModule = array_merge($localModule, $this->remoteModulesJson[$remoteModulesType][$remoteModule['name']]);
                        }
                    }

                    $localModule['repo_details']['latestRelease'] = $moduleNeedsUpgrade;
                    if ($localModule['installed'] == '0') {
                        $localModule['version'] = $moduleNeedsUpgrade['name'];
                    } else if ($localModule['installed'] == '1') {
                        $localModule['update_available'] = '1';
                        $localModule['update_version'] = $moduleNeedsUpgrade['name'];
                    }

                    $modules['updates'][$localModule['id']] = $localModule;

                    unset($remoteModules[$remoteModuleKey]);
                }
            } else {
                $remoteModule['repo_details'] = [];
                $remoteModule['repo_details']['details'] = $remoteModule;
                $moduleNeedsUpgrade = $this->moduleNeedsUpgrade($remoteModule);
                $remoteModule['repo_details']['latestRelease'] = $moduleNeedsUpgrade;

                $modules['new'][$remoteModuleKey] = $remoteModule;
            }
        }

        return $modules;
    }

    protected function moduleNeedsUpgrade($remoteModule, $localModule = null, $returnLatestReleaseOnly = false)
    {
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
                $remoteModule['name']
            ];

        $latestRelease = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

        if (!$latestRelease) {
            return false;
        }

        if ($returnLatestReleaseOnly) {
            return $latestRelease;
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
                }

                return false;
            } else {
                if (Version::greaterThan($latestRelease['tag_name'], $localModule['version'])) {
                    return $latestRelease;
                }
            }
        } else {
            return $latestRelease;
        }
    }

    // protected function getNamesPathString($names)
    // {
    //     unset($names[0]);
    //     unset($names[1]);
    //     unset($names[2]);
    //     unset($names[3]);

    //     $path = '';

    //     foreach ($names as $name) {
    //         $path .= ucfirst($name) . '/';
    //     }

    //     return $path;
    // }
}