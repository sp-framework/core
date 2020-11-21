<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use Phalcon\Helper\Arr;
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
                    base_path('applications/' . $this->application['name'] .
                              '/Views/' . $this->view['name'] .
                              '/html_compiled/');
            } else {
                $this->voltCompiledPath =
                    base_path('applications/Admin/Views/Default/html_compiled/');
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
                    base_path('applications/' . $this->application['name'] .
                              '/Views/' . $this->view['name'] .
                              '/html/');
            } else {
                $this->phalconViewPath =
                    base_path('applications/Admin/Views/Default/html/');
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
                    base_path('applications/' . $this->application['name'] .
                              '/Views/' . $this->view['name'] .
                              '/html/layouts/');
            } else {
                $this->phalconViewLayoutPath =
                    base_path('applications/Admin/Views/Default/html/layouts/');
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
            // var_dump($this->application);
            if ($this->application) {
                if (isset($this->modules->domains->getDomain()['settings']['applications'][$this->application['id']])) {
                    $applicationDefaults = $this->modules->domains->getDomain()['settings']['applications'][$this->application['id']];
                } else {
                    $applicationDefaults = null;
                }
            } else {
                $applicationDefaults = null;
            }

            if ($this->application && $applicationDefaults) {

                // $applicationName = ucfirst($this->application['route']);

                $viewsName = $this->getIdViews($applicationDefaults['defaultViews'])['name'];

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
                $this->viewSettings = json_decode($this->view['settings'], true);

                $this->cache = $this->viewSettings['cache'];

                if (isset($this->viewSettings['tags']) && $this->viewSettings['tags']) {
                    $this->tags = $this->checkTagsPackage($this->viewSettings['tags']);
                }
            } else {
                $this->cache = false;
                $this->tags = false;
            }
            //     var_dump($this->viewSettings);
            // var_dump($applicationDefaults);
            // die();
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

    public function getApplicationView($id, $name)
    {
        $filter =
            $this->model->filter(
                function($view) use ($id, $name) {
                    if ($view->application_id == $id && $view->name === ucfirst($name)) {
                        return $view;
                    }
                }
            );

        if (count($filter) > 1) {
            throw new \Exception('Duplicate default view for application ' . $name);
        } else if (count($filter) === 1) {
            return $filter[0]->toArray();
        } else {
            return false;
        }
    }

    public function getViewsForApplication($id)
    {
        $views = [];

        $filter =
            $this->model->filter(
                function($view) use ($id) {
                    if ($view->application_id == $id && !$view->view_id) {
                        return $view;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            array_push($views, $value->toArray());
        }

        return $views;
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
}