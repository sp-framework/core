<?php

namespace System\Base\Providers\ModulesServiceProvider;

use GuzzleHttp\Exception\ClientException;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Manager extends BasePackage
{
    protected $api;

    protected $apiConfig;

    protected $localModules = [];

    protected $remoteModules = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $packages;

    protected $middlewares;

    protected $views;

    public function getModuleInfo($data)
    {
        if ($data['module_type'] === 'component') {
            //
        } else if ($data['module_type'] === 'package') {
            $module = $this->modules->packages->getIdPackage($data['module_id']);

            if ($module) {
                unset($module['notification_subscriptions']);
                unset($module['files']);
                unset($module['settings']);

                $moduleUpdatedBy = $module['updated_by'];

                if ($module['updated_by'] == 0) {
                    $module['updated_by'] = 'System';
                } else {
                    $module['updated_by'] = $this->basepackages->accounts->getById($module['updated_by'])['email'];
                }

                if (isset($data['sync']) && $data['sync'] == true) {
                    $repoNameArr = explode('/', $module['repo']);

                    try {
                        $api = $this->basepackages->api->useApi($module['api_id']);

                        $responseArr = $api->useMethod('RepositoryApi', 'repoGet', [$api->getApiConfig()['org_user'], Arr::last($repoNameArr)])->getResponse(true);

                        if ($responseArr && $responseArr !== '') {
                            $module['repo_details'] = Json::encode($responseArr);
                            $module['updated_by'] = $moduleUpdatedBy;

                            $this->modules->packages->updatePackage($module);
                            $module['repo_details'] = $responseArr;
                        }
                    } catch (ClientException | \throwable $e) {
                        $this->addResponse($e->getMessage(), 2, ['module' => $module]);

                        return false;
                    }
                }

                $this->addResponse('Module information success.',0, ['module' => $module]);
            } else {
                $this->addResponse('Module information failed.', 1, []);
            }
        } else if ($data['module_type'] === 'middleware') {
            //
        } else if ($data['module_type'] === 'view') {
            //
        }
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

        // var_dump($this->getModulesData(null, true));
        //populate localModules so that we can compare with remoteModules
        // $this->getModulesData(null, true);

        if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {

            //generate localModules with updated Data
            // $this->getModulesData(null, true, true);
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Synced successfully';

            return true;
        }

        return false;
    }

    public function getRepositoryModules($data)
    {
        $modules = [];
        $sortedModules = [];

        $modules['components'] = $this->modules->components->getComponentsByApiId($data['api_id']);
        $modules['middlewares'] = $this->modules->middlewares->getMiddlewaresByApiId($data['api_id']);
        $modules['packages'] = $this->modules->packages->getPackagesByApiId($data['api_id']);
        $modules['views'] = $this->modules->views->getViewsByApiId($data['api_id']);

        foreach ($modules as $moduleType => $modulesArr) {
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
                    $module['data']['moduleid'] = $moduleArr['module_type'] . '-' . $moduleArr['id'];
                    $module['data']['installed'] = $moduleArr['installed'];
                    $module['data']['update_available'] = $moduleArr['update_available'];

                    array_push($sortedModules[$moduleArr['app_type']]['childs'][$moduleArr['module_type']]['childs'][$moduleArr['category']]['childs'][$moduleArr['sub_category']]['childs'], $module);
                }
            }
        }

        $this->addResponse('Ok', 0, ['modules' => $sortedModules]);
    }

    // protected function applyFilters($filter = null, $getFresh = false)
    // {
    //     if ($filter) {
    //         $app = $this->apps->getById($filter);

    //         if ($getFresh) {
    //             $this->components =
    //                 $this->modules->components->init(true)
    //                 // ->getComponentsForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getComponentsForAppType($app['app_type']);

    //             $this->packages =
    //                 $this->modules->packages->init(true)
    //                 // ->getPackagesForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getPackagesForAppType($app['app_type']);

    //             $this->middlewares =
    //                 $this->modules->middlewares->init(true)
    //                 // ->getMiddlewaresForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getMiddlewaresForAppType($app['app_type']);

    //             $this->views =
    //                 $this->modules->views->init(true)
    //                 // ->getViewsForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getViewsForAppType($app['app_type']);
    //         } else {
    //             $this->components =
    //                 $this->modules->components
    //                 // ->getComponentsForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getComponentsForAppType($app['app_type']);

    //             $this->packages =
    //                 $this->modules->packages
    //                 // ->getPackagesForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getPackagesForAppType($app['app_type']);

    //             $this->middlewares =
    //                 $this->modules->middlewares
    //                 // ->getMiddlewaresForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getMiddlewaresForAppType($app['app_type']);

    //             $this->views =
    //                 $this->modules->views
    //                 // ->getViewsForCategoryAndSubcategory($app['category'], $app['sub_category']);
    //                 ->getViewsForAppType($app['app_type']);
    //         }
    //     } else {
    //         $this->components = $this->modules->components->components;

    //         $this->packages = $this->modules->packages->packages;

    //         $this->middlewares = $this->modules->middlewares->middlewares;

    //         $this->views = $this->modules->views->views;
    //     }
    // }

    protected function getRemoteModules()
    {
        try {
            if (strtolower($this->apiConfig['provider']) === 'gitea') {
                $modulesArr = $this->api->useMethod('UserApi', 'userListRepos', [$this->apiConfig['org_user']])->getResponse();
            } else if (strtolower($this->apiConfig['provider']) === 'github') {
                //
            }
        } catch (\Exception | ClientException $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        die(var_dump($modulesArr));
        if ($modulesArr) {
            foreach ($modulesArr as $key => $value) {
                $names = explode('-', $value->name);

                if (count($names) > 0) {
                    if (count($names) === 1 && $names[0] === 'core') {
                        $url = $siteUrl . $value->full_name . $branch . 'core.json';

                        try {
                            $this->remoteModules['core'][$value->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );
                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing ' . $value->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'component') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $value->full_name . $branch . $path . 'Install/' . 'component.json';

                        try {
                            $this->remoteModules['components'][$value->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing component ' . $value->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'package') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $value->full_name . $branch . $path . 'Install/' . 'package.json';

                        try {
                            $this->remoteModules['packages'][$value->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing package ' . $value->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'middleware') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $value->full_name . $branch . $path . 'Install/' . 'middleware.json';

                        try {
                            $this->remoteModules['middlewares'][$value->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );

                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing middleware ' . $value->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }

                    } else if ($names[2] === 'view') {

                        $path = $this->getNamesPathString($names);

                        $url =
                            $siteUrl . $value->full_name . $branch . $path .'view.json';

                        try {
                            $this->remoteModules['views'][$value->name] =
                                Json::decode(
                                    $this->remoteWebContent->get($url, $headers)->getBody()->getContents()
                                    , true
                                );
                        } catch (\Exception $e) {
                            $this->packagesData->responseCode = 1;

                            $this->packagesData->responseMessage =
                                'Syncing view ' . $value->name . ' resulted in error ' .
                                $e->getResponse()->getStatusCode() .
                                '. Sync Halted! Please contact remote administrator or developer.';

                            $this->logger->log->debug($e->getMessage());

                            return false;
                        }
                    }
                }
            }
            return true;

        }

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
            if ($remoteModulesType === 'core') {

                $remoteCore = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);

                if (count($remoteCore['update']) > 0) {
                    foreach ($remoteCore['update'] as $updateRemoteCoreKey => $updateRemoteCore) {
                        $this->modules->core->update($updateRemoteCore);
                        $counter['update'] = $counter['update'] + 1;
                    }
                }
            }

            if ($remoteModulesType === 'components') {

                if (count($this->localModules[$remoteModulesType]) > 0) {
                    $remoteComponents = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
                } else {
                    $remoteComponents['update'] = [];
                    $remoteComponents['register'] = $remoteModules;
                }

                if (count($remoteComponents['update']) > 0) {
                    foreach ($remoteComponents['update'] as $updateRemoteComponentKey => $updateRemoteComponent) {

                        $updateRemoteComponent['settings'] =
                            isset($updateRemoteComponent['settings']) ?
                            Json::encode($updateRemoteComponent['settings']) :
                            Json::encode([]);

                        $updateRemoteComponent['dependencies'] =
                            isset($updateRemoteComponent['dependencies']) ?
                            Json::encode($updateRemoteComponent['dependencies']) :
                            Json::encode([]);

                        $this->modules->components->update($updateRemoteComponent);

                        $counter['update'] = $counter['update'] + 1;
                    }
                }

                if (count($remoteComponents['register']) > 0) {
                    foreach ($remoteComponents['register'] as $registerRemoteComponentKey => $registerRemoteComponent) {
                        $registerRemoteComponent['dependencies'] =
                            isset($registerRemoteComponent['dependencies']) ?
                            Json::encode($registerRemoteComponent['dependencies']) :
                            Json::encode([]);

                        if ($registerRemoteComponent['menu']) {
                            if (isset($registerRemoteComponent['menu']['seq'])) {
                                $sequence = $registerRemoteComponent['menu']['seq'];
                                unset($registerRemoteComponent['menu']['seq']);
                            } else {
                                $sequence = 99;
                            }
                            $menu['menu'] = Json::encode($registerRemoteComponent['menu']);
                            $menu['sequence'] = $sequence;
                            $menu['apps'] = Json::encode([]);

                            $this->basepackages->menus->add($menu);

                            $registerRemoteComponent['menu_id'] = $this->basepackages->menus->packagesData->last['id'];

                            $this->basepackages->menus->init(true);//Reset Cache

                            $registerRemoteComponent['menu'] = Json::encode($registerRemoteComponent['menu']);
                        } else {
                            $registerRemoteComponent['menu'] = false;
                        }

                        $registerRemoteComponent['settings'] =
                            isset($registerRemoteComponent['settings']) ?
                            Json::encode($registerRemoteComponent['settings']) :
                            Json::encode([]);

                        $registerRemoteComponent['apps'] =
                            Json::encode([]);

                        $registerRemoteComponent['installed'] = 0;

                        if ($this->auth->account()) {
                            $registerRemoteComponent['updated_by'] = $this->auth->account()['id'];
                        } else {
                            $registerRemoteComponent['updated_by'] = 0;
                        }

                        $this->modules->components->add($registerRemoteComponent);

                        $counter['register'] = $counter['register'] + 1;
                    }
                }
            }

            if ($remoteModulesType === 'packages') {

                if (count($this->localModules[$remoteModulesType]) > 0) {
                    $remotePackages = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
                } else {
                    $remotePackages['update'] = [];
                    $remotePackages['register'] = $remoteModules;
                }

                if (count($remotePackages['update']) > 0) {
                    foreach ($remotePackages['update'] as $updateRemotePackageKey => $updateRemotePackage) {

                        $updateRemotePackage['settings'] =
                            isset($updateRemotePackage['settings']) ?
                            Json::encode($updateRemotePackage['settings']) :
                            Json::encode([]);

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
            }

            if ($remoteModulesType === 'middlewares') {

                if (count($this->localModules[$remoteModulesType]) > 0) {
                    $remoteMiddlewares = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
                } else {
                    $remoteMiddlewares['update'] = [];
                    $remoteMiddlewares['register'] = $remoteModules;
                }

                if (count($remoteMiddlewares['update']) > 0) {
                    foreach ($remoteMiddlewares['update'] as $updateRemoteMiddlewareKey => $updateRemoteMiddleware) {

                        $updateRemoteMiddleware['settings'] =
                            isset($updateRemoteMiddleware['settings']) ?
                            Json::encode($updateRemoteMiddleware['settings']) :
                            Json::encode([]);

                        $this->modules->middlewares->update($updateRemoteMiddleware);

                        $counter['update'] = $counter['update'] + 1;
                    }
                }

                if (count($remoteMiddlewares['register']) > 0) {
                    foreach ($remoteMiddlewares['register'] as $registerRemoteMiddlewareKey => $registerRemoteMiddleware) {

                        $registerRemoteMiddleware['settings'] =
                            isset($registerRemoteMiddleware['settings']) ?
                            Json::encode($registerRemoteMiddleware['settings']) :
                            Json::encode([]);

                        $registerRemoteMiddleware['apps'] = Json::encode([]);

                        $registerRemoteMiddleware['installed'] = 0;

                        if ($this->auth->account()) {
                            $registerRemoteMiddleware['updated_by'] = $this->auth->account()['id'];
                        } else {
                            $registerRemoteMiddleware['updated_by'] = 0;
                        }

                        $this->modules->middlewares->add($registerRemoteMiddleware);

                        $counter['register'] = $counter['register'] + 1;
                    }
                }
            }

            if ($remoteModulesType === 'views') {

                if (count($this->localModules[$remoteModulesType]) > 0) {
                    $remoteViews = $this->findRemoteInLocal($remoteModules, $this->localModules[$remoteModulesType]);
                } else {
                    $remoteViews['update'] = [];
                    $remoteViews['register'] = $remoteModules;
                }

                if (count($remoteViews['update']) > 0) {
                    foreach ($remoteViews['update'] as $updateRemoteViewKey => $updateRemoteView) {
                        $this->modules->views->update($updateRemoteView);
                        $counter['update'] = $counter['update'] + 1;
                    }
                }

                if (count($remoteViews['register']) > 0) {
                    foreach ($remoteViews['register'] as $registerRemoteViewKey => $registerRemoteView) {
                        $registerRemoteView['dependencies'] =
                            isset($registerRemoteView['dependencies']) ?
                            Json::encode($registerRemoteView['dependencies']) :
                            Json::encode([]);

                        $registerRemoteView['settings'] =
                            isset($registerRemoteView['settings']) ?
                            Json::encode($registerRemoteView['settings']) :
                            Json::encode([]);

                        $registerRemoteView['apps'] =
                            Json::encode([]);

                        $registerRemoteView['installed'] = 0;

                        if ($this->auth->account()) {
                            $registerRemoteView['updated_by'] = $this->auth->account()['id'];
                        } else {
                            $registerRemoteView['updated_by'] = 0;
                        }

                        $this->modules->views->add($registerRemoteView);

                        $counter['register'] = $counter['register'] + 1;
                    }
                }
            }
        }

        $this->packagesData->counter = $counter;

        return true;
    }

    protected function findRemoteInLocal($remoteModules, $localModules)
    {
        $modules = [];
        $modules['update'] = [];

        foreach ($remoteModules as $remoteModuleKey => $remoteModule) {
            foreach ($localModules as $localModuleKey => $localModule) {

                if ($localModule['repo'] === $remoteModule['repo']) {

                    if ($this->moduleNeedsUpgrade($localModule, $remoteModule)) {

                        // if ($localModule['installed'] === '0') {

                        //     $localModule['version'] = $remoteModule['version'];
                        // } else if ($localModule['installed'] === '1') {

                        //     $localModule['update_available'] = '1';

                        //     $localModule['update_version'] = $remoteModule['version'];
                        // }

                        if (isset($localModule['settings'])) {
                            $localModule['settings'] = Json::encode($localModule['settings']);
                        } else {
                            $localModule['settings'] = null;
                        }

                        if (isset($localModule['dependencies'])) {
                            $localModule['dependencies'] = Json::encode($remoteModule['dependencies']);
                        } else {
                            $localModule['dependencies'] = null;
                        }

                        $modules['update'][$localModuleKey] = $localModule;

                        unset($remoteModules[$remoteModuleKey]);
                    }

                    unset($remoteModules[$remoteModuleKey]);
                }
            }
        }

        $modules['register'] = $remoteModules;

        return $modules;
    }

    protected function moduleNeedsUpgrade($localModule, $remoteModule)
    {
        if ($localModule['version'] !== $remoteModule['version'] &&
            $localModule['update_version'] !== $remoteModule['version']
           ) {

            $installedModuleVersion = explode('.', $localModule['version']);

            $newModuleVersion = explode('.', $remoteModule['version']);

            if ($newModuleVersion[0] > $installedModuleVersion[0]) {

                return true;

            } else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
                       $newModuleVersion[1] > $installedModuleVersion[1]
                      ) {

                return true;

            } else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
                       $newModuleVersion[1] === $installedModuleVersion[1] &&
                       $newModuleVersion[2] > $installedModuleVersion[2]
                    ) {

                return true;
            }
        }
    }
}