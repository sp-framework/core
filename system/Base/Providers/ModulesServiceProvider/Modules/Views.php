<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\ModulesViews;

class Views extends BasePackage
{
    protected $modelToUse = ModulesViews::class;

    public $views;

    protected $view;

    protected $voltCompiledPath;

    protected $phalconViewPath;

    protected $phalconViewLayoutPath;

    protected $phalconViewLayoutFile;

    protected $activeLayout;

    protected $viewSettings;

    protected $cache;

    protected $tags;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        $this->setApp();

        $this->setVoltCompiledPath();

        $this->setPhalconViewPath();

        $this->setPhalconViewLayoutPath();

        $this->setPhalconViewLayoutFile();

        return $this;
    }

    public function setVoltCompiledPath($path = null)
    {
        if ($path) {
            $this->voltCompiledPath = $path;
            return;
        }

        if (!isset($this->voltCompiledPath)) {
            if ($this->app && $this->view) {
                $this->voltCompiledPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/Html_compiled/' . ucfirst($this->app['route']) . '/' . $this->view['name'] . '/'
                          );
            } else {
                $this->voltCompiledPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/Html_compiled/');
            }

            if (!is_dir($this->voltCompiledPath)) {
                mkdir(
                    $this->voltCompiledPath,
                    0777,
                    true
                );
            }
        }
    }

    public function setPhalconViewPath($path = null)
    {
        if ($path) {
            $this->phalconViewPath = $path;
            return;
        }

        if (!isset($this->phalconViewPath)) {
            if ($this->app && $this->view) {
                $this->phalconViewPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/' . $this->view['name'] .
                              '/html/');
            } else {
                $this->phalconViewPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/');
            }
        }
    }

    public function setPhalconViewLayoutPath($path = null)
    {
        if ($path) {
            $this->phalconViewLayoutPath = $path;
            return;
        }

        if (!isset($this->phalconViewLayoutPath)) {
            if ($this->app && $this->view) {
                $this->phalconViewLayoutPath =
                    base_path('apps/' .
                              ucfirst($this->app['app_type']) .
                              '/Views/' . $this->view['name'] .
                              '/html/layouts/');
            } else {
                $this->phalconViewLayoutPath =
                    base_path('system/Base/Providers/ErrorServiceProvider/View/layouts/');
            }
        }
    }

    public function setPhalconViewLayoutFile()
    {
        if (!isset($this->phalconViewLayoutFile)) {
            if ($this->viewSettings && isset($this->viewSettings['layouts'])) {
                foreach ($this->viewSettings['layouts'] as $layout) {
                    if (isset($layout['active']) && $layout['active'] == true) {
                        $this->activeLayout = $layout['view'];
                        break;
                    }
                }
            }
        }

        if (!$this->phalconViewLayoutFile) {
            $this->phalconViewLayoutFile = 'default';
        }
        if (!$this->activeLayout) {
            $this->activeLayout = 'default';
        }
    }

    public function getVoltCompiledPath()
    {
        return $this->voltCompiledPath;
    }

    public function getPhalconViewPath()
    {
        return $this->phalconViewPath;
    }

    public function getPhalconViewLayoutPath()
    {
        return $this->phalconViewLayoutPath;
    }

    public function getPhalconViewLayoutFile()
    {
        return $this->phalconViewLayoutFile;
    }

    public function getActiveLayout()
    {
        return $this->activeLayout;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getViewInfo()
    {
        return $this->view;
    }

    public function getViewSettings()
    {
        return $this->viewSettings;
    }

    public function getViewTags()
    {
        return $this->tags;
    }

    protected function setApp()
    {
        if (!$this->app) {
            $this->app = $this->apps->getAppInfo();

            $this->domain = $this->domains->getDomain();

            if ($this->app &&
                isset($this->domain['apps'][$this->app['id']]['view'])
            ) {
                $viewsName = $this->getViewById($this->domain['apps'][$this->app['id']]['view'])['name'];
                //Get views settings
                $viewsSettings = $this->modules->viewsSettings->getViewsSettingsByViewIdDomainIdAndAppId(
                    $this->domain['apps'][$this->app['id']]['view'],
                    $this->domain['id'],
                    $this->app['id']
                );

                if ($viewsSettings) {
                    $this->viewSettings = Json::decode($viewsSettings['settings'], true);
                }
            } else {
                $viewsName =  'Default';
            }

            if (!$this->view) {
                //Make sure view has proper app ID.
                if ($this->app) {
                    $this->view = $this->getViewByNameForAppId($viewsName, $this->app['id']);
                }
            }
            if ($this->view) {
                if (!$this->viewSettings) {
                    $this->viewSettings = Json::decode($this->view['settings'], true);
                }

                $this->cache = $this->config->cache->enabled;

                if (isset($this->viewSettings['tags']) && $this->viewSettings['tags']) {
                    $this->tags = $this->checkTagsPackage($this->viewSettings['tags']);
                }
            } else {
                $this->cache = false;
                $this->tags = false;
            }
        }
    }

    protected function checkTagsPackage($packageName)
    {
        return
            $this->modules->packages->getPackageByNameForAppId(
                Arr::last(explode('\\', $packageName)),
                $this->apps->getAppInfo()['id']
            );
    }

    public function getViewByNameForAppId($name, $appId)
    {
        foreach($this->views as $view) {
            $view['apps'] = Json::decode($view['apps'], true);

            if ((isset($view['apps'][$appId]['enabled']) &&
                $view['apps'][$appId]['enabled'] == true) &&
                strtolower($view['name']) == strtolower($name)
            ) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsForAppId($appId)
    {
        $views = [];

        foreach($this->views as $view) {
            $view['apps'] = Json::decode($view['apps'], true);

            if (isset($view['apps'][$appId]['enabled']) &&
                $view['apps'][$appId]['enabled'] == 'true'
            ) {
                array_push($views, $view);
            }
        }

        return $views;
    }

    public function getViewById($id)
    {
        foreach($this->views as $view) {
            if ($view['id'] == $id) {
                return $view;
            }
        }

        return false;
    }

    public function getViewByRepo($repo)
    {
        foreach($this->views as $view) {
            if ($view['repo'] == $repo) {
                return $view;
            }
        }

        return false;
    }

    public function getViewByAppTypeAndRepoAndName($appType, $repo, $name)
    {
        foreach($this->views as $view) {
            if ($view['app_type'] === $appType &&
                $view['repo'] === $repo &&
                strtolower($name) === strtolower($view['name'])
            ) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsByApiId($apiId)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['api_id'] == $apiId) {
                array_push($views, $view);
            }
        }

        return $views;
    }

    public function getViewByName($name)
    {
        foreach($this->views as $view) {
            if ($view['name'] == $name) {
                return $view;
            }
        }

        return false;
    }

    public function getViewsForCategory($category)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['category'] === $category) {
                $views[$view['id']] = $view;
            }
        }

        return $views;
    }

    public function getViewsForAppType($appType)
    {
        $views = [];

        foreach($this->views as $view) {
            if ($view['app_type'] === $appType) {
                $views[$view['id']] = $view;
            }
        }

        return $views;
    }

    public function getDefaultViewForAppType($appType)
    {
        foreach($this->views as $view) {
            if ($view['app_type'] === $appType &&
                $view['name'] === 'Default'
            ) {
                return $view;
            }
        }

        return false;
    }

    public function updateViews(array $data)
    {
        $views = Json::decode($data['views'], true);

        foreach ($views as $viewId => $status) {
            $view = $this->getById($viewId);

            $view['apps'] = Json::decode($view['apps'], true);

            if ($status === true) {
                $view['apps'][$data['id']]['enabled'] = true;
            } else if ($status === false) {
                $view['apps'][$data['id']]['enabled'] = false;
            }

            $view['apps'] = Json::encode($view['apps']);

            $view['settings'] = Json::decode($view['settings'], true);

            if (isset($view['settings']['tags'])) {
                $package = $this->modules->packages->getPackageByName($view['settings']['tags']);

                if ($package) {
                    $package['apps'] = Json::decode($package['apps'], true);

                    $package['apps'][$data['id']]['enabled'] = true;

                    $package['apps'] = Json::encode($package['apps']);

                    $this->modules->packages->update($package);
                }
            }

            $view['settings'] = Json::encode($view['settings']);

            $this->update($view);
        }

        return true;
    }
}