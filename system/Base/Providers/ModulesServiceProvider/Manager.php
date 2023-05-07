<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\ObjectSerializer;
use System\Base\Providers\ModulesServiceProvider\Installer;
use z4kn4fein\SemVer\Version;

class Manager extends BasePackage
{
    protected $api;

    protected $apiConfig;

    protected $remoteModules = [];

    protected $remoteModulesJson = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $packages;

    protected $middlewares;

    protected $views;

    public $installer;

    public function init()
    {
        return $this;
    }

    public function saveModuleSettings($data)
    {
        if ($data['module_type'] === 'components') {
            $module = $this->modules->components->getComponentById($data['module_id']);
        } else if ($data['module_type'] === 'packages') {
            $module = $this->modules->packages->getIdPackage($data['module_id']);
        } else if ($data['module_type'] === 'middlewares') {
            $module = $this->modules->middlewares->getMiddlewareById($data['module_id']);
        } else if ($data['module_type'] === 'views') {
            $module = $this->modules->views->getIdViews($data['module_id']);
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
            $module = $this->modules->packages->getIdPackage($data['module_id']);
        } else if ($data['module_type'] === 'middlewares') {
            $module = $this->modules->middlewares->getMiddlewareById($data['module_id']);
        } else if ($data['module_type'] === 'views') {
            $module = $this->modules->views->getIdViews($data['module_id']);
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

            $moduleUpdatedBy = $module['updated_by'];

            if ($module['installed'] == '1') {
                if ($module['updated_by'] == 0) {
                    $module['updated_by'] = 'System';
                } else {
                    $module['updated_by'] = $this->basepackages->accounts->getById($module['updated_by'])['email'];
                }
            } else {
                $module['updated_by'] = 'System';
            }

            if (isset($data['sync']) && $data['sync'] == true) {
                $module = $this->updateModuleRepoDetails($module, $moduleUpdatedBy);

                if (!$module) {
                    return false;
                }
            }

            if ($module['repo_details']) {
                if (is_string($module['repo_details'])) {
                    try {
                        $module['repo_details'] = Json::decode($module['repo_details'], true);
                        $module['repo_details']['latestRelease']['body'] = $this->escaper->escapeHtml($module['repo_details']['latestRelease']['body']);
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

    protected function updateModuleRepoDetails($module, $moduleUpdatedBy)
    {
        if ($module['app_type'] === 'core') {
            $repoNameArr = ['core'];
        } else {
            $repoNameArr = explode('/', $module['repo']);
        }

        try {
            if (!$this->initApi($module['api_id'])) {
                return false;
            }

            if (strtolower($this->apiConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGet';
                $args = [$this->apiConfig['org_user'], Arr::last($repoNameArr)];
            } else if (strtolower($this->apiConfig['provider']) === 'github') {
                //For github
            }

            $responseArr = $this->api->useMethod($collection, $method, $args)->getResponse(true);

            if ($responseArr) {
                $module['repo_details']['details'] = $responseArr;

                $this->remoteModules[$module['module_type']] = [$responseArr];

                $module['repo_details']['latestRelease'] = $this->moduleNeedsUpgrade($module, $responseArr, true);
                $module['updated_by'] = $moduleUpdatedBy;

                $this->modules->{$module['module_type']}->update($module);
            } else {
                $module['repo_details'] = false;
            }
        } catch (ClientException | \throwable $e) {
            var_dump($e);die();
            $this->addResponse($e->getMessage(), 2, ['module' => $module]);

            return false;
        }

        return $module;
    }

    public function getRepositoryModules($data = [])
    {
        $localModules = [];
        $sortedModules = [];

        $apis = $this->basepackages->api->init()->getAll()->api;

        if (count($apis) === 0) {
            $this->addResponse('No API configured', 1);

            return false;
        }

        foreach ($apis as $api) {
            if ($api['category'] === 'repos') {
                $this->api = $this->basepackages->api->useApi($api['id'], true);
                $this->apiConfig = $this->api->getApiConfig();

                $sortedModules[$api['id']] = [];
                $sortedModules[$api['id']]['childs'] = [];
                $sortedModules[$api['id']]['name'] = $this->apiConfig['name'];
                $sortedModules[$api['id']]['data']['type'] = 'repo';
                $sortedModules[$api['id']]['data']['apiid'] = $this->apiConfig['id'];
            }
        }

        if (isset($data['api_id'])) {
            $localModules['components'] = $this->modules->components->getComponentsByApiId($data['api_id']);
            $localModules['middlewares'] = $this->modules->middlewares->getMiddlewaresByApiId($data['api_id']);
            $localModules['packages'] = $this->modules->packages->getPackagesByApiId($data['api_id']);
            $localModules['views'] = $this->modules->views->getViewsByApiId($data['api_id']);
        } else {
            $localModules['components'] = $this->modules->components->getAll()->components;
            $localModules['middlewares'] = $this->modules->middlewares->getAll()->middlewares;
            $localModules['packages'] = $this->modules->packages->getAll()->packages;
            $localModules['views'] = $this->modules->views->getAll()->views;
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

                    if (!isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['name'] = $moduleArr['category'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['data']['type'] = 'category';
                    }

                    if (!isset($sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']])) {
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']] = [];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['name'] = $moduleArr['sub_category'];
                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['data']['type'] = 'sub_category';

                        $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['childs'] = [];
                    }

                    $module['id'] = $moduleArr['id'];
                    $module['name'] = $moduleArr['name'];
                    if (isset($moduleArr['display_name'])) {
                        $module['name'] = $moduleArr['display_name'];
                    }
                    $module['data']['apiid'] = $moduleArr['api_id'];
                    $module['data']['apptype'] = $moduleArr['app_type'];
                    $module['data']['moduletype'] = $moduleArr['module_type'];
                    $module['data']['modulecategory'] = $moduleArr['category'];
                    $module['data']['modulesubcategory'] = $moduleArr['sub_category'];
                    $module['data']['moduleid'] = $moduleArr['module_type'] . '-' . $moduleArr['id'];
                    $module['data']['installed'] = $moduleArr['installed'];
                    $module['data']['update_available'] = $moduleArr['update_available'];
                    $module['data']['isnew'] = '0';
                    if ($moduleArr['installed'] == '0') {
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

                    $sortedModules[$moduleArr['api_id']]['childs'][$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['childs'][$module['data']['moduleid']] = $module;
                }
            }
        }

        $this->addResponse('Ok', 0, ['modules' => $sortedModules]);

        return $sortedModules;
    }

    protected function initApi($id)
    {
        $this->api = $this->basepackages->api->useApi($id, true);

        $this->apiConfig = $this->api->getApiConfig();

        if ($this->apiConfig['auth_type'] === 'auth' &&
            ((!$this->apiConfig['username'] || $this->apiConfig['username'] === '') &&
             (!$this->apiConfig['password'] || $this->apiConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return false;
        } else if ($this->apiConfig['auth_type'] === 'access_token' &&
                   (!$this->apiConfig['access_token'] || $this->apiConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return false;
        } else if ($this->apiConfig['auth_type'] === 'autho' &&
                   (!$this->apiConfig['authorization'] || $this->apiConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return false;
        }

        return true;
    }

    public function syncRemoteWithLocal($id, $getRepositoryModules = false)
    {
        if (!$this->initApi($id)) {
            return false;
        }

        try {
            if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {
                if ($getRepositoryModules) {
                    $this->getRepositoryModules();
                } else {
                    $this->getRepositoryModules(['api_id' => $this->apiConfig['id']]);
                }

                return true;
            }
        } catch (ClientException | \throwable $e) {
            var_dump($e);die();
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return false;
    }

    protected function getRemoteModules()
    {
        try {
            if (strtolower($this->apiConfig['provider']) === 'gitea') {
                $modulesArr = $this->api->useMethod('UserApi', 'userListRepos', [$this->apiConfig['org_user']])->getResponse(true);
            } else if (strtolower($this->apiConfig['provider']) === 'github') {
                //
            }
        } catch (\throwable | ClientException $e) {
            $this->addResponse($e->getMessage(), 1);

            if (str_contains($e->getMessage(), 'token')) {
                $this->addResponse('API Authentication failed!', 1);
            }

            return false;
        }

        if ($modulesArr) {
            foreach ($modulesArr as $key => $module) {
                $names = explode('-', $module['name']);

                if (count($names) > 1) {
                    if (strtolower($this->apiConfig['provider']) === 'gitea') {
                        if ($module['release_counter'] == 0) {
                            continue;//Dont add as there are no releases.
                        }

                        if (isset($this->remoteModules[$names[1]])) {
                            array_push($this->remoteModules[$names[1]], $module);
                        } else {
                            $this->remoteModules[$names[1]] = [$module];
                        }

                        try {
                            ObjectSerializer::setUrlEncoding(false);

                            $jsonFile = $this->api->useMethod('RepositoryApi', 'repoGetRawFile',
                                [
                                    $this->apiConfig['org_user'],
                                    $module['name'],
                                    'Install/' . $names[1] . '.json'
                                ]
                            )->getResponse(true);

                            if ($jsonFile) {
                                $this->remoteModulesJson[$module['name']] = $jsonFile;
                            }
                        } catch (ClientException | \throwable $e) {
                            $this->logger->log->debug(
                                'Reading component ' . $module['name'] . ' install JSON file resulted in error. ' .
                                $e->getMessage()
                            );

                            unset($this->remoteModules[$names[1]]);
                        }

                        return true;
                    }
                } else {
                    if ($names[0] === 'core') {
                        $this->remoteModules['packages'] = [$module];

                        return true;
                    }

                    $this->addResponse('Unable to Sync with remote server', 1);

                    return false;
                }
            }

            return true;
        }

        $this->addResponse('Unable to Sync with remote server', 1);

        return false;
    }

    protected function getNamesPathString($names)
    {
        unset($names[0]);
        unset($names[1]);
        unset($names[2]);
        unset($names[3]);

        $path = '';

        foreach ($names as $name) {
            $path .= ucfirst($name) . '/';
        }

        return $path;
    }

    protected function updateRemoteModulesToDB()
    {
        $counter = [];

        foreach ($this->remoteModules as $remoteModulesType => $remoteModules) {
            $remotePackages = $this->findRemoteInLocal($remoteModules, $remoteModulesType);

            if (count($remotePackages['updates']) > 0) {
                foreach ($remotePackages['updates'] as $updateRemotePackageKey => $updateRemotePackage) {
                    if (!isset($counter['updates'])) {
                        $counter['updates'] = [];
                        $counter['updates']['api']['id'] = $this->apiConfig['id'];
                        $counter['updates']['api']['name'] = $this->apiConfig['name'];
                        $counter['updates']['count'] = 0;
                    }

                    $this->modules->$remoteModulesType->update($updateRemotePackage);

                    if ($updateRemotePackage['installed'] == '1') {
                        $counter['updates']['count'] = $counter['updates']['count'] + 1;
                    }
                }
            }

            if (count($remotePackages['new']) > 0) {
                foreach ($remotePackages['new'] as $registerRemotePackageKey => $registerRemotePackage) {
                    if (!isset($counter['new'])) {
                        $counter['new'] = [];
                        $counter['new']['api']['id'] = $this->apiConfig['id'];
                        $counter['new']['api']['name'] = $this->apiConfig['name'];
                        $counter['new']['count'] = 0;
                    }

                    $repo_details = $registerRemotePackage['repo_details'];

                    $registerRemotePackage = $this->remoteModulesJson[$registerRemotePackage['name']];
                    $registerRemotePackage['repo_details'] = $repo_details;

                    if (!$this->apps->types->getTypeAppType($registerRemotePackage['app_type'])) {
                        if (!checkCtype($registerRemotePackage['app_type'], 'alpha')) {
                            $this->addResponse('App Type for package ' . $registerRemotePackage['name'] . ' contains illegal characters.', 1);

                            return false;
                        }

                        try {
                            $this->apps->types->add(
                                [
                                    'name'          => ucfirst($registerRemotePackage['app_type']),
                                    'app_type'      => $registerRemotePackage['app_type'],
                                    'description'   => $registerRemotePackage['app_type']
                                ]
                            );
                        } catch (\Exception $e) {
                            $this->addResponse($e->getMessage(), 1);

                            return false;
                        }
                    }

                    $registerRemotePackage['settings'] =
                        isset($registerRemotePackage['settings']) ?
                        Json::encode($registerRemotePackage['settings']) :
                        Json::encode([]);

                    $registerRemotePackage['apps'] = Json::encode([]);

                    $registerRemotePackage['installed'] = 0;

                    if ($this->auth->account()) {
                        $registerRemotePackage['updated_by'] = $this->auth->account()['id'];
                    } else {
                        $registerRemotePackage['updated_by'] = 0;
                    }

                    $registerRemotePackage['api_id'] = $this->apiConfig['id'];

                    $this->modules->$remoteModulesType->add($registerRemotePackage);

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

            if ($remoteModulesType === 'components') {
                $localModule = $this->modules->components->getComponentByRepo($repoUrl);
            } else if ($remoteModulesType === 'packages') {
                $localModule = $this->modules->packages->getPackageByRepo($repoUrl);
            } else if ($remoteModulesType === 'middlewares') {
                $localModule = $this->modules->middlewares->getMiddlewareByRepo($repoUrl);
            } else if ($remoteModulesType === 'views') {
                $localModule = $this->modules->views->getViewByRepo($repoUrl);
            }

            if ($localModule) {
                $localModule['repo_details'] = [];
                $localModule['repo_details']['details'] = $remoteModule;

                $moduleNeedsUpgrade = $this->moduleNeedsUpgrade($localModule, $remoteModule);

                if ($moduleNeedsUpgrade) {
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
                $moduleNeedsUpgrade = $this->moduleNeedsUpgrade(null, $remoteModule);
                $remoteModule['repo_details']['latestRelease'] = $moduleNeedsUpgrade;

                $modules['new'][$remoteModuleKey] = $remoteModule;
            }
        }

        return $modules;
    }

    protected function moduleNeedsUpgrade($localModule = null, $remoteModule, $returnLatestReleaseOnly = false)
    {
        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGetLatestRelease';
            $args = [$this->apiConfig['org_user'], $remoteModule['name']];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        $latestRelease = $this->api->useMethod($collection, $method, $args)->getResponse(true);

        if (!$latestRelease) {
            return false;
        }

        if ($returnLatestReleaseOnly) {
            return $latestRelease;
        }

        if ($localModule) {
            if (array_key_exists('level_of_update', $localModule)) {
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
}