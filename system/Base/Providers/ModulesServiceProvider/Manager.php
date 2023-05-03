<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use z4kn4fein\SemVer\Version;

class Manager extends BasePackage
{
    protected $api;

    protected $apiConfig;

    protected $remoteModules = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $packages;

    protected $middlewares;

    protected $views;

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
            if (isset($module['notification_subscriptions'])) {
                unset($module['notification_subscriptions']);
            }
            if (isset($module['files'])) {
                unset($module['files']);
            }
            if (isset($module['settings'])) {
                unset($module['settings']);
            }

            $moduleUpdatedBy = $module['updated_by'];

            if ($module['updated_by'] == 0) {
                $module['updated_by'] = 'System';
            } else {
                $module['updated_by'] = $this->basepackages->accounts->getById($module['updated_by'])['email'];
            }

            if (isset($data['sync']) && $data['sync'] == true) {
                $module = $this->updateModuleRepoDetails($module, $moduleUpdatedBy);

                if (!$module) {
                    return false;
                }
            }

            $this->addResponse('Module information success.',0, ['module' => $module]);
        } else {
            $this->addResponse('Module information failed.', 1, []);
        }
    }

    protected function updateModuleRepoDetails($module, $moduleUpdatedBy)
    {
        if ($module['app_type'] === 'core') {
            $repoNameArr = ['core'];
        } else {
            $repoNameArr = explode('/', $module['repo']);
        }

        try {
            $api = $this->basepackages->api->useApi($module['api_id']);

            if (strtolower($api->getApiConfig()['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGet';
                $args = [$api->getApiConfig()['org_user'], Arr::last($repoNameArr)];
            } else if (strtolower($api->getApiConfig()['provider']) === 'github') {
                //For github
            }
            $responseArr = $api->useMethod($collection, $method, $args)->getResponse(true);

            if ($responseArr) {
                $module['repo_details'] = Json::encode($responseArr);
                $module['updated_by'] = $moduleUpdatedBy;
                $module['repo_details'] = $responseArr;

                if ($module['module_type'] === 'components') {
                    $this->modules->components->update($module);
                } else if ($module['module_type'] === 'packages') {
                    $this->modules->packages->update($module);
                } else if ($module['module_type'] === 'middlewares') {
                    $this->modules->middlewares->update($module);
                } else if ($module['module_type'] === 'views') {
                    $this->modules->views->update($module);
                }
            }
        } catch (ClientException | \throwable $e) {
            $this->addResponse($e->getMessage(), 2, ['module' => $module]);

            return false;
        }

        return $module;
    }

    public function getRepositoryModules($data)
    {
        $localModules = [];
        $sortedModules = [];

        $localModules['components'] = $this->modules->components->getComponentsByApiId($data['api_id']);
        $localModules['middlewares'] = $this->modules->middlewares->getMiddlewaresByApiId($data['api_id']);
        $localModules['packages'] = $this->modules->packages->getPackagesByApiId($data['api_id']);
        $localModules['views'] = $this->modules->views->getViewsByApiId($data['api_id']);

        foreach ($localModules as $moduleType => $modulesArr) {
            if (count($modulesArr) > 0) {
                foreach ($modulesArr as $moduleArr) {
                    if (!isset($sortedModules[$moduleArr['app_type']])) {
                        $sortedModules[$moduleArr['app_type']] = [];
                        $sortedModules[$moduleArr['app_type']]['name'] = $moduleArr['app_type'];
                    }

                    if (!isset($sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']])) {
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']] = [];
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['name'] = $moduleArr['module_type'];
                    }

                    if (!isset($sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']])) {
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']] = [];
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['name'] = $moduleArr['category'];
                    }

                    if (!isset($sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']])) {
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']] = [];
                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['name'] = $moduleArr['sub_category'];

                        $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['childs'] = [];
                    }

                    $module['id'] = $moduleArr['id'];
                    $module['name'] = $moduleArr['name'];
                    if (isset($moduleArr['display_name'])) {
                        $module['name'] = $moduleArr['display_name'];
                    }
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

                    $sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['childs'][$module['data']['moduleid']] = $module;
                }
            }
        }

        $this->addResponse('Ok', 0, ['modules' => $sortedModules]);
    }

    public function syncRemoteWithLocal($id)
    {
        $this->api = $this->basepackages->api->useApi($id, true);

        $this->apiConfig = $this->api->getApiConfig();

        if ($this->apiConfig['auth_type'] === 'auth' &&
            ((!$this->apiConfig['username'] || $this->apiConfig['username'] === '') &&
             (!$this->apiConfig['password'] || $this->apiConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return;
        } else if ($this->apiConfig['auth_type'] === 'access_token' &&
                   (!$this->apiConfig['access_token'] || $this->apiConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return;
        } else if ($this->apiConfig['auth_type'] === 'autho' &&
                   (!$this->apiConfig['authorization'] || $this->apiConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return;
        }

        if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {
            $this->addResponse('Synced successfully');

            return true;
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
                    if (count($names) === 1 && $names[0] === 'core') {
                        $url = $siteUrl . $module->full_name . $branch . 'core.json';

                        try {
                            $this->remoteModules['core'][$module->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );
                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing ' . $module->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'component') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $module->full_name . $branch . $path . 'Install/' . 'component.json';

                        try {
                            $this->remoteModules['components'][$module->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing component ' . $module->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'package') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $module->full_name . $branch . $path . 'Install/' . 'package.json';

                        try {
                            $this->remoteModules['packages'][$module->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing package ' . $module->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'middleware') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $module->full_name . $branch . $path . 'Install/' . 'middleware.json';

                        try {
                            $this->remoteModules['middlewares'][$module->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing middleware ' . $module->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'view') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $module->full_name . $branch . $path .'view.json';

                        try {
                            $this->remoteModules['views'][$module->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );
                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing view ' . $module->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }
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

        $path = '';

        foreach ($names as $name) {
            $path .= ucfirst($name) . '/';
        }

        return $path;
    }

    protected function updateRemoteModulesToDB()
    {
        $counter = [];
        $counter['register'] = 0;
        $counter['update'] = 0;

        foreach ($this->remoteModules as $remoteModulesType => $remoteModules) {
            $remotePackages = $this->findRemoteInLocal($remoteModules, $remoteModulesType);

            if (count($remotePackages['update']) > 0) {
                foreach ($remotePackages['update'] as $updateRemotePackageKey => $updateRemotePackage) {
                    $this->modules->packages->update($updateRemotePackage);

                    $counter['update'] = $counter['update'] + 1;
                }
            }

            if (count($remotePackages['register']) > 0) {
                foreach ($remotePackages['register'] as $registerRemotePackageKey => $registerRemotePackage) {

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

                    $this->modules->packages->add($registerRemotePackage);

                    $counter['register'] = $counter['register'] + 1;
                }
            }
            // if ($remoteModulesType === 'components') {

            //     if (count($this->localModules[$remoteModulesType]) > 0) {
            //         $remoteComponents = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
            //     } else {
            //         $remoteComponents['update'] = [];
            //         $remoteComponents['register'] = $remoteModules;
            //     }

            //     if (count($remoteComponents['update']) > 0) {
            //         foreach ($remoteComponents['update'] as $updateRemoteComponentKey => $updateRemoteComponent) {

            //             $updateRemoteComponent['settings'] =
            //                 isset($updateRemoteComponent['settings']) ?
            //                 Json::encode($updateRemoteComponent['settings']) :
            //                 Json::encode([]);

            //             $updateRemoteComponent['dependencies'] =
            //                 isset($updateRemoteComponent['dependencies']) ?
            //                 Json::encode($updateRemoteComponent['dependencies']) :
            //                 Json::encode([]);

            //             $this->modules->components->update($updateRemoteComponent);

            //             $counter['update'] = $counter['update'] + 1;
            //         }
            //     }

            //     if (count($remoteComponents['register']) > 0) {
            //         foreach ($remoteComponents['register'] as $registerRemoteComponentKey => $registerRemoteComponent) {
            //             $registerRemoteComponent['dependencies'] =
            //                 isset($registerRemoteComponent['dependencies']) ?
            //                 Json::encode($registerRemoteComponent['dependencies']) :
            //                 Json::encode([]);

            //             if ($registerRemoteComponent['menu']) {
            //                 if (isset($registerRemoteComponent['menu']['seq'])) {
            //                     $sequence = $registerRemoteComponent['menu']['seq'];
            //                     unset($registerRemoteComponent['menu']['seq']);
            //                 } else {
            //                     $sequence = 99;
            //                 }
            //                 $menu['menu'] = Json::encode($registerRemoteComponent['menu']);
            //                 $menu['sequence'] = $sequence;
            //                 $menu['apps'] = Json::encode([]);

            //                 $this->basepackages->menus->add($menu);

            //                 $registerRemoteComponent['menu_id'] = $this->basepackages->menus->packagesData->last['id'];

            //                 $this->basepackages->menus->init(true);//Reset Cache

            //                 $registerRemoteComponent['menu'] = Json::encode($registerRemoteComponent['menu']);
            //             } else {
            //                 $registerRemoteComponent['menu'] = false;
            //             }

            //             $registerRemoteComponent['settings'] =
            //                 isset($registerRemoteComponent['settings']) ?
            //                 Json::encode($registerRemoteComponent['settings']) :
            //                 Json::encode([]);

            //             $registerRemoteComponent['apps'] =
            //                 Json::encode([]);

            //             $registerRemoteComponent['installed'] = 0;

            //             if ($this->auth->account()) {
            //                 $registerRemoteComponent['updated_by'] = $this->auth->account()['id'];
            //             } else {
            //                 $registerRemoteComponent['updated_by'] = 0;
            //             }

            //             $this->modules->components->add($registerRemoteComponent);

            //             $counter['register'] = $counter['register'] + 1;
            //         }
            //     }
            // } else if ($remoteModulesType === 'packages') {
            // } else if ($remoteModulesType === 'middlewares') {

            //     if (count($this->localModules[$remoteModulesType]) > 0) {
            //         $remoteMiddlewares = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
            //     } else {
            //         $remoteMiddlewares['update'] = [];
            //         $remoteMiddlewares['register'] = $remoteModules;
            //     }

            //     if (count($remoteMiddlewares['update']) > 0) {
            //         foreach ($remoteMiddlewares['update'] as $updateRemoteMiddlewareKey => $updateRemoteMiddleware) {

            //             $updateRemoteMiddleware['settings'] =
            //                 isset($updateRemoteMiddleware['settings']) ?
            //                 Json::encode($updateRemoteMiddleware['settings']) :
            //                 Json::encode([]);

            //             $this->modules->middlewares->update($updateRemoteMiddleware);

            //             $counter['update'] = $counter['update'] + 1;
            //         }
            //     }

            //     if (count($remoteMiddlewares['register']) > 0) {
            //         foreach ($remoteMiddlewares['register'] as $registerRemoteMiddlewareKey => $registerRemoteMiddleware) {

            //             $registerRemoteMiddleware['settings'] =
            //                 isset($registerRemoteMiddleware['settings']) ?
            //                 Json::encode($registerRemoteMiddleware['settings']) :
            //                 Json::encode([]);

            //             $registerRemoteMiddleware['apps'] = Json::encode([]);

            //             $registerRemoteMiddleware['installed'] = 0;

            //             if ($this->auth->account()) {
            //                 $registerRemoteMiddleware['updated_by'] = $this->auth->account()['id'];
            //             } else {
            //                 $registerRemoteMiddleware['updated_by'] = 0;
            //             }

            //             $this->modules->middlewares->add($registerRemoteMiddleware);

            //             $counter['register'] = $counter['register'] + 1;
            //         }
            //     }
            // } else if ($remoteModulesType === 'views') {

            //     if (count($this->localModules[$remoteModulesType]) > 0) {
            //         $remoteViews = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
            //     } else {
            //         $remoteViews['update'] = [];
            //         $remoteViews['register'] = $remoteModules;
            //     }

            //     if (count($remoteViews['update']) > 0) {
            //         foreach ($remoteViews['update'] as $updateRemoteViewKey => $updateRemoteView) {
            //             $this->modules->views->update($updateRemoteView);
            //             $counter['update'] = $counter['update'] + 1;
            //         }
            //     }

            //     if (count($remoteViews['register']) > 0) {
            //         foreach ($remoteViews['register'] as $registerRemoteViewKey => $registerRemoteView) {
            //             $registerRemoteView['dependencies'] =
            //                 isset($registerRemoteView['dependencies']) ?
            //                 Json::encode($registerRemoteView['dependencies']) :
            //                 Json::encode([]);

            //             $registerRemoteView['settings'] =
            //                 isset($registerRemoteView['settings']) ?
            //                 Json::encode($registerRemoteView['settings']) :
            //                 Json::encode([]);

            //             $registerRemoteView['apps'] =
            //                 Json::encode([]);

            //             $registerRemoteView['installed'] = 0;

            //             if ($this->auth->account()) {
            //                 $registerRemoteView['updated_by'] = $this->auth->account()['id'];
            //             } else {
            //                 $registerRemoteView['updated_by'] = 0;
            //             }

            //             $this->modules->views->add($registerRemoteView);

            //             $counter['register'] = $counter['register'] + 1;
            //         }
            //     }
            // }
        }

        $this->packagesData->counter = $counter;

        return true;
    }

    protected function findRemoteInLocal($remoteModules, $remoteModulesType)
    {
        $modules = [];
        $modules['update'] = [];

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

                    $modules['update'][$localModule['id']] = $localModule;

                    unset($remoteModules[$remoteModuleKey]);
                }
            }
        }

        $modules['register'] = $remoteModules;

        return $modules;
    }

    protected function moduleNeedsUpgrade($localModule, $remoteModule)
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

        if (Version::greaterThan($latestRelease['name'], $localModule['version'])) {
            return $latestRelease;
        }
    }
}