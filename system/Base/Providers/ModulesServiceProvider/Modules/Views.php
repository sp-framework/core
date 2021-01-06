<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Views as ViewsModel;

class Views extends BasePackage
{
    protected $modelToUse = ViewsModel::class;

    public $views;

    protected $view;

    protected $applications;

    protected $application;

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

        $this->applications = $this->modules->applications;

        $this->setApplication();

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
            if ($this->application && $this->view) {
                $this->voltCompiledPath =
                    base_path('applications/' .
                              ucfirst($this->application['app_type']) . '/' .
                              // ucfirst($this->application['sub_category']) .
                              '/Views/Html_compiled/' . ucfirst($this->application['route']) . '/' . $this->view['name'] . '/'
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
            if ($this->application && $this->view) {
                $this->phalconViewPath =
                    base_path('applications/' .
                              ucfirst($this->application['app_type']) . '/' .
                              // ucfirst($this->application['sub_category']) .
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
            if ($this->application && $this->view) {
                $this->phalconViewLayoutPath =
                    base_path('applications/' .
                              ucfirst($this->application['app_type']) . '/' .
                              // ucfirst($this->application['sub_category']) .
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

    protected function setApplication()
    {
        if (!$this->application) {
            $this->application = $this->applications->getApplicationInfo();

            $this->domain = $this->basepackages->domains->getDomain();

            if ($this->application &&
                isset($this->domain['applications'][$this->application['id']]['view'])
            ) {
                $viewsName = $this->getIdViews($this->domain['applications'][$this->application['id']]['view'])['name'];
            } else {
                $viewsName =  'Default';
            }

            if (!$this->view) {
                //Make sure view has proper application ID.
                if ($this->application) {
                    $this->view = $this->getApplicationView($this->application['id'], $viewsName);
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
            $this->modules->packages->getNamedPackageForApplication(
                Arr::last(explode('\\', $packageName)),
                $this->modules->applications->getApplicationInfo()['id']
            );
    }

    public function getApplicationView($applicationId, $name)
    {
        $filter =
            $this->model->filter(
                function($view) use ($applicationId, $name) {
                    $view = $view->toArray();
                    $view['applications'] = Json::decode($view['applications'], true);
                    if ((isset($view['applications'][$applicationId]['enabled']) &&
                        $view['applications'][$applicationId]['enabled'] === true) &&
                        $view['name'] === ucfirst($name)
                    ) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate default view for application ' . $name);
        } else if (count($filter) === 1) {
            return $filter[0];
        } else {
            return false;
        }
    }

    public function getViewsForApplication($applicationId)
    {
        $filter =
            $this->model->filter(
                function($view) use ($applicationId) {
                    $view = $view->toArray();
                    $view['applications'] = Json::decode($view['applications'], true);
                    if (isset($view['applications'][$applicationId]['enabled']) &&
                        $view['applications'][$applicationId]['enabled'] === true
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

            $view['applications'] = Json::decode($view['applications'], true);

            if ($status === true) {
                $view['applications'][$data['id']]['enabled'] = true;
            } else if ($status === false) {
                $view['applications'][$data['id']]['enabled'] = false;
            }

            $view['applications'] = Json::encode($view['applications']);

            $view['settings'] = Json::decode($view['settings'], true);

            if (isset($view['settings']['tags'])) {
                $package = $this->modules->packages->getNamePackage($view['settings']['tags']);

                if ($package) {
                    $package['applications'] = Json::decode($package['applications'], true);

                    $package['applications'][$data['id']]['enabled'] = true;

                    $package['applications'] = Json::encode($package['applications']);

                    $this->modules->packages->update($package);
                }
            }

            $view['settings'] = Json::encode($view['settings']);

            $this->update($view);
        }

        return true;
    }
}