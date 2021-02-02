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
            if ($this->view) {
                $this->phalconViewLayoutFile =
                    $this->viewSettings['layout'];
            } else {
                $this->phalconViewLayoutFile = 'default';
            }
        } else {
            $this->phalconViewLayoutFile = 'default';
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

    public function getCache()
    {
        return $this->cache;
    }

    public function getViewInfo()
    {
        return $this->view;
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
                $viewsName = $this->getIdViews($this->domain['apps'][$this->app['id']]['view'])['name'];
            } else {
                $viewsName =  'Default';
            }

            if (!$this->view) {
                //Make sure view has proper app ID.
                if ($this->app) {
                    $this->view = $this->getAppView($this->app['id'], $viewsName);
                }
            }
            if ($this->view) {
                $this->viewSettings = Json::decode($this->view['settings'], true);

                $this->cache = $this->viewSettings['cache'];

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
            $this->modules->packages->getNamedPackageForApp(
                Arr::last(explode('\\', $packageName)),
                $this->apps->getAppInfo()['id']
            );
    }

    public function getAppView($appId, $name)
    {
        $filter =
            $this->model->filter(
                function($view) use ($appId, $name) {
                    $view = $view->toArray();
                    $view['apps'] = Json::decode($view['apps'], true);
                    if ((isset($view['apps'][$appId]['enabled']) &&
                        $view['apps'][$appId]['enabled'] === true) &&
                        $view['name'] === ucfirst($name)
                    ) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate default view for app ' . $name);
        } else if (count($filter) === 1) {
            return $filter[0];
        } else {
            return false;
        }
    }

    public function getViewsForApp($appId)
    {
        $filter =
            $this->model->filter(
                function($view) use ($appId) {
                    $view = $view->toArray();
                    $view['apps'] = Json::decode($view['apps'], true);
                    if (isset($view['apps'][$appId]['enabled']) &&
                        $view['apps'][$appId]['enabled'] === true
                    ) {
                        return $view;
                    }
                }
            );

        return $filter;
    }

    public function getIdViews($id)
    {
        $filter =
            $this->model->filter(
                function($view) use ($id) {
                    if ($view->id == $id) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate view Id found for id ' . $id);
        } else if (count($filter) === 1) {
            return $filter[0]->toArray();
        } else {
            return false;
        }
    }

    public function getNameViews($name)
    {
        $filter =
            $this->model->filter(
                function($view) use ($name) {
                    if ($view->name == $name) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate view Id found for name ' . $name);
        } else if (count($filter) === 1) {
            return $filter[0]->toArray();
        } else {
            return false;
        }
    }

    public function getViewsForCategoryAndSubcategory($category, $subCategory)
    {
        $views = [];

        $filter =
            $this->model->filter(
                function($view) use ($category, $subCategory) {
                    $view = $view->toArray();
                    if ($view['category'] === $category &&
                        $view['sub_category'] === $subCategory
                    ) {
                        return $view;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $views[$key] = $value;
        }

        return $views;
    }

    public function getViewsForAppType(string $type)
    {
        $views = [];

        $filter =
            $this->model->filter(
                function($view) use ($type) {
                    $view = $view->toArray();
                    if ($view['app_type'] === $type) {
                        return $view;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $views[$key] = $value;
        }

        return $views;
    }

    public function getDefaultViewForAppType(string $type)
    {
        $filter =
            $this->model->filter(
                function($view) use ($type) {
                    $view = $view->toArray();
                    if ($view['app_type'] === $type &&
                        $view['name'] === 'Default'
                    ) {
                        return $view;
                    }
                }
            );

        return $filter[0];
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
                $package = $this->modules->packages->getNamePackage($view['settings']['tags']);

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