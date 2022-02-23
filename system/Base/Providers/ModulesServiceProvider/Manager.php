<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Manager extends BasePackage
{
    protected $repository;

    protected $localModules = [];

    protected $remoteModules = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $packages;

    protected $middlewares;

    protected $views;

    public function syncRemoteWithLocal($id)
    {
        $repository = $this->modules->repositories->getById($id);

        if ($repository['auth_token'] == 1 && (!$repository['password'] || $repository['password']=== '')) {

            $this->addResponse('Password missing, cannot sync', 1);

            return;
        } else if ($repository['auth_token'] == 2 && (!$repository['token'] || $repository['token'] === '')) {

            $this->addResponse('Token missing, cannot sync', 1);

            return;
        }

        $repository = $this->decryptPassToken($repository);

        $this->repository = $repository;

        var_dump($this->getModulesData(null, true));
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

    public function getModulesData($filter = null, $inclCore = false, $getFresh = false)
    {
        $this->getLocalModules($filter, $inclCore, $getFresh);

        $this->packagesData->responseCode = 0;

        $this->packagesData->modulesData = $this->localModules;

        return true;
    }

    public function getLocalModules($filter = null, $inclCore = false, $getFresh = false)
    {
        $this->packagesData->appInfo = $this->app;

        if ($getFresh) {
            $this->core = $this->modules->core->init(true)->core;
        } else {
            $this->core = $this->modules->core->core;
        }

        if ($filter === 'core') {
            $this->localModules['core'][$this->core[0]['id']] = $this->core[0];
            return;
        }

        if ($inclCore) {
            $this->localModules['core'][$this->core[0]['id']] = $this->core[0];
        }

        $this->applyFilters($filter, $getFresh);

        if (count($this->components) > 0) {
            foreach ($this->components as $componentKey => $component) {
                $this->localModules['components'][$component['id']] = $component;
                $this->localModules['components'][$component['id']]['settings']
                    = Json::decode($component['settings'], true);
                $this->localModules['components'][$component['id']]['dependencies']
                    = Json::decode($component['dependencies'], true);

                if ($component['installed'] == 0) {
                    $updatedOnDate = new \DateTime($component['updated_on']);
                    $now = new \DateTime('now');
                    $diff = date_diff($updatedOnDate, $now);
                    if ($diff->h < 24) {
                        $this->localModules['components'][$component['id']]['new'] = true;
                    }
                }
            }
        } else {
            $this->localModules['components'] = [];
        }

        if (count($this->packages) > 0) {
            foreach ($this->packages as $packagesKey => $packages) {
                $this->localModules['packages'][$packages['id']] = $packages;
                $this->localModules['packages'][$packages['id']]['settings']
                    = Json::decode($packages['settings'], true);

                if ($packages['installed'] == 0) {
                    $updatedOnDate = new \DateTime($packages['updated_on']);
                    $now = new \DateTime('now');
                    $diff = date_diff($updatedOnDate, $now);
                    if ($diff->h < 24) {
                        $this->localModules['packages'][$packages['id']]['new'] = true;
                    }
                }
            }
        } else {
            $this->localModules['packages'] = [];
        }

        if (count($this->middlewares) > 0) {
            foreach ($this->middlewares as $middlewareKey => $middleware) {
                $this->localModules['middlewares'][$middleware['id']] = $middleware;
                $this->localModules['middlewares'][$middleware['id']]['settings']
                    = Json::decode($middleware['settings'], true);

                if ($middleware['installed'] == 0) {
                    $updatedOnDate = new \DateTime($middleware['updated_on']);
                    $now = new \DateTime('now');
                    $diff = date_diff($updatedOnDate, $now);
                    if ($diff->h < 24) {
                        $this->localModules['middlewares'][$middleware['id']]['new'] = true;
                    }
                }
            }
        } else {
            $this->localModules['middlewares'] = [];
        }

        if (count($this->views) > 0) {
            foreach ($this->views as $viewKey => $view) {
                $this->localModules['views'][$view['id']] = $view;
                $this->localModules['views'][$view['id']]['settings']
                    = Json::decode($view['settings'], true);
                $this->localModules['views'][$view['id']]['dependencies']
                    = Json::decode($view['dependencies'], true);

                if ($middleware['installed'] == 0) {
                    $updatedOnDate = new \DateTime($view['updated_on']);
                    $now = new \DateTime('now');
                    $diff = date_diff($updatedOnDate, $now);
                    if ($diff->h < 24) {
                        $this->localModules['views'][$view['id']]['new'] = true;
                    }
                }
            }
        } else {
            $this->localModules['views'] = [];
        }

        $this->packagesData->responseCode = 0;

        $this->packagesData->modulesData = $this->localModules;
    }

    protected function applyFilters($filter = null, $getFresh = false)
    {
        if ($filter) {
            $app = $this->apps->getById($filter);

            if ($getFresh) {
                $this->components =
                    $this->modules->components->init(true)
                    // ->getComponentsForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getComponentsForAppType($app['app_type']);

                $this->packages =
                    $this->modules->packages->init(true)
                    // ->getPackagesForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getPackagesForAppType($app['app_type']);

                $this->middlewares =
                    $this->modules->middlewares->init(true)
                    // ->getMiddlewaresForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getMiddlewaresForAppType($app['app_type']);

                $this->views =
                    $this->modules->views->init(true)
                    // ->getViewsForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getViewsForAppType($app['app_type']);
            } else {
                $this->components =
                    $this->modules->components
                    // ->getComponentsForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getComponentsForAppType($app['app_type']);

                $this->packages =
                    $this->modules->packages
                    // ->getPackagesForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getPackagesForAppType($app['app_type']);

                $this->middlewares =
                    $this->modules->middlewares
                    // ->getMiddlewaresForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getMiddlewaresForAppType($app['app_type']);

                $this->views =
                    $this->modules->views
                    // ->getViewsForCategoryAndSubcategory($app['category'], $app['sub_category']);
                    ->getViewsForAppType($app['app_type']);
            }
        } else {
            $this->components = $this->modules->components->components;

            $this->packages = $this->modules->packages->packages;

            $this->middlewares = $this->modules->middlewares->middlewares;

            $this->views = $this->modules->views->views;
        }
    }

    protected function getRemoteModules()
    {
        $repoUrl = $this->repository['repo_url'];

        if ($this->repository['repo_provider'] === '1') {//Gitea
            $headers =
                [
                    'headers'   =>
                        [
                            'accept'    =>  'app/json'
                        ]
                ];
            $siteUrl = $this->repository['site_url'];

            $branch = '/raw/branch/' . $this->repository['branch'] . '/';
        } else if ($this->repository['repo_provider'] === '2') {//Github
            $headers =
                [
                    'headers'   =>
                        [
                            'accept'    =>  'app/vnd.github.mercy-preview+json'
                        ]
                ];
            $siteUrl = $this->repository['site_url'];//https://raw.githubusercontent.com/

            $branch = '/' . $this->repository['branch'] . '/';
        }

        if ($this->repository['auth_token'] === '1') {//Auth
            $headers['auth'] =
                [
                    $this->repository['username'],
                    $this->repository['password']
                ];

        } else if ($this->repository['auth_token'] === '2') {//Token
            if ($this->repository['repo_provider'] === '1') {//Gitea

               $headers['headers']['Authorization'] = 'token ' . $this->repository['token'];

            } else if ($this->repository['repo_provider'] === '2') {//Github
                //
            }
        }
        // https://docs.guzzlephp.org/en/stable/request-options.html#verify-option
        // We need to download the CS certificate from Firefox and load it in the php.ini file.
        $headers['verify'] = false;

        try {
            $body = Json::decode($this->remoteContent->get($repoUrl, $headers)->getBody()->getContents());

        } catch (ClientException $e) {
            $body = null;

            $this->packagesData->responseCode = 1;

            if ($e->getResponse()->getStatusCode() === 403) {
                $this->packagesData->responseMessage = 'Add username and password to repository.<br>' . $e->getMessage();
            } else {
                $this->packagesData->responseMessage = $e->getMessage();
            }

            return false;
        } catch (ConnectException $e) {

            $body = null;

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            return false;
        }
        if ($body) {
            foreach ($body as $key => $value) {

                $names = explode('-', $value->name);

                if (count($names) > 0) {
                    if (count($names) === 1 && $names[0] === 'core') {
                        $url = $siteUrl . $value->full_name . $branch . 'core.json';

                        try {
                            $this->remoteModules['core'][$value->name] =
                                Json::decode(
                                    $this->remoteContent->get($url, $headers)->getBody()->getContents()
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
                                    $this->remoteContent->get($url, $headers)->getBody()->getContents()
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
                                    $this->remoteContent->get($url, $headers)->getBody()->getContents()
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
                                    $this->remoteContent->get($url, $headers)->getBody()->getContents()
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
                                    $this->remoteContent->get($url, $headers)->getBody()->getContents()
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
        }

        return true;
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

    protected function decryptPassToken(array $data)
    {
        if ($data['auth_token'] == 1) {
            $data['password'] = $this->crypt->decryptBase64($data['password'], $this->secTools->getSigKey());
        } else if ($data['auth_token'] == 2) {
            $data['token'] = $this->crypt->decryptBase64($data['token'], $this->secTools->getSigKey());
        }

        return $data;
    }
}