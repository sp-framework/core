<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Helper\Arr;
use System\Base\BasePackage;

class Manager extends BasePackage
{
    protected $repository;

    protected $localModules = [];

    protected $remoteModules = [];

    protected $modulesData = [];

    protected $module;

    protected $core;

    protected $applications;

    protected $packages;

    protected $middlewares;

    protected $views;

    public function syncRemoteWithLocal($id)
    {
        $this->repository = $this->modules->repositories->getById($id);

        //populate localModules so that we can compare with remoteModules
        $this->getModulesData(null, true);

        if ($this->getRemoteModules() === true && $this->updateRemoteModulesToDB() === true) {

            //generate localModules with updated Data
            $this->getModulesData(null, true, true);

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
        $this->packagesData->applicationInfo =
            $this->modules->applications->getApplicationInfo();

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
                    = json_decode($component['settings'], true);
                $this->localModules['components'][$component['id']]['dependencies']
                    = json_decode($component['dependencies'], true);
            }
        } else {
            $this->localModules['components'] = [];
        }

        if (count($this->views) > 0) {
            foreach ($this->views as $viewKey => $view) {
                $this->localModules['views'][$view['id']] = $view;
                $this->localModules['views'][$view['id']]['settings']
                    = json_decode($view['settings'], true);
                $this->localModules['views'][$view['id']]['dependencies']
                    = json_decode($view['dependencies'], true);
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
            $application = $this->modules->applications->getById($filter);

            if ($getFresh) {
                $this->components =
                    $this->modules->components->init(true)->getComponentsForCategoryAndSubcategory($application['category'], $application['sub_category']);
                $this->views =
                    $this->modules->views->init(true)->getViewsForCategoryAndSubcategory($application['category'], $application['sub_category']);
            } else {
                $this->components =
                    $this->modules->components->getComponentsForCategoryAndSubcategory($application['category'], $application['sub_category']);
                $this->views =
                    $this->modules->views->getViewsForCategoryAndSubcategory($application['category'], $application['sub_category']);
            }
        } else {
            $this->components = $this->modules->components->components;
            $this->views = $this->modules->views->views;
        }
    }

    protected function getRemoteModules()
    {
        $repoUrl = $this->repository['url'];

        if ($this->repository['repo_provider'] === '1') {//Gitea
            $headers =
                [
                    'headers'   =>
                        [
                            'accept'    =>  'application/json'
                        ]
                ];
            $siteUrl = 'https://dev.bazaari.com.au/';
            $branchUrl = '/raw/branch/master/';
        } else if ($this->repository['repo_provider'] === '2') {//Github
            $headers =
                [
                    'headers'   =>
                        [
                            'accept'    =>  'application/vnd.github.mercy-preview+json'
                        ]
                ];
            $siteUrl = 'https://raw.githubusercontent.com/';
            $branchUrl = '/master/';
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
            $body = json_decode($this->remoteContent->get($repoUrl, $headers)->getBody()->getContents());

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
                        $url = $siteUrl . $value->full_name . $branchUrl . 'core.json';

                        $this->remoteModules['core'][$value->name] =
                            json_decode(
                                $this->remoteContent->get($url, $headers)->getBody()->getContents()
                                , true
                            );

                    } else if ($names[2] === 'component') {
                        $url =
                            $siteUrl . $value->full_name . $branchUrl . ucfirst(Arr::last($names)) . '/Install/' . 'component.json';

                        $this->remoteModules['components'][$value->name] =
                            json_decode(
                                $this->remoteContent->get($url, $headers)->getBody()->getContents()
                                , true
                            );

                    } else if ($names[2] === 'view') {
                        $url =
                            $siteUrl . $value->full_name . $branchUrl . ucfirst(Arr::last($names)) .'view.json';

                        $this->remoteModules['views'][$value->name] =
                            json_decode(
                                $this->remoteContent->get($url, $headers)->getBody()->getContents()
                                , true
                            );
                    }
                }
            }
        }

        return true;
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
                dump($remoteComponents);
                die();
                if (count($remoteComponents['update']) > 0) {
                    foreach ($remoteComponents['update'] as $updateRemoteComponentKey => $updateRemoteComponent) {

                        $updateRemoteComponent['settings'] =
                            isset($updateRemoteComponent['settings']) ?
                            json_encode($updateRemoteComponent['settings']) :
                            json_encode([]);

                        $updateRemoteComponent['dependencies'] =
                            isset($updateRemoteComponent['dependencies']) ?
                            json_encode($updateRemoteComponent['dependencies']) :
                            json_encode([]);

                        $this->modules->components->update($updateRemoteComponent);

                        $counter['update'] = $counter['update'] + 1;
                    }
                }

                if (count($remoteComponents['register']) > 0) {
                    foreach ($remoteComponents['register'] as $registerRemoteComponentKey => $registerRemoteComponent) {
                        $registerRemoteComponent['settings'] =
                            isset($registerRemoteComponent['settings']) ?
                            json_encode($registerRemoteComponent['settings']) :
                            json_encode([]);

                        $registerRemoteComponent['dependencies'] =
                            isset($registerRemoteComponent['dependencies']) ?
                            json_encode($registerRemoteComponent['dependencies']) :
                            json_encode([]);

                        $this->modules->components->add($registerRemoteComponent);

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
                        $registerRemoteView['settings'] =
                            isset($registerRemoteView['settings']) ?
                            json_encode($registerRemoteView['settings']) :
                            json_encode([]);

                        $registerRemoteView['dependencies'] =
                            isset($registerRemoteView['dependencies']) ?
                            json_encode($registerRemoteView['dependencies']) :
                            json_encode([]);

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
                            $localModule['settings'] = json_encode($localModule['settings']);
                        } else {
                            $localModule['settings'] = null;
                        }

                        if (isset($localModule['dependencies'])) {
                            $localModule['dependencies'] = json_encode($remoteModule['dependencies']);
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