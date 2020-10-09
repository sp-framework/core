<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Modules\Model\Views as ViewsModel;
use System\Base\Providers\ModulesServiceProvider\Views\ViewsData;

class Views extends BasePackage
{
    public $views;

    protected $view;

    protected $applications;

    protected $applicationInfo;

    protected $voltCompiledPath;

    protected $phalconViewPath;

    protected $phalconViewLayoutPath;

    protected $phalconViewLayoutFile;

    protected $cache;

    public function init()
    {
        $this->applications = $this->modules->applications;

        $this->setApplicationInfo();

        $this->setVoltCompiledPath();

        $this->setPhalconViewPath();

        $this->setPhalconViewLayoutPath();

        $this->setPhalconViewLayoutFile();

        return $this;
    }


    protected function setVoltCompiledPath()
    {
        if (!isset($this->voltCompiledPath)) {
            $this->voltCompiledPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html_compiled/');

            if (!is_dir($this->voltCompiledPath)) {
                mkdir(
                    $this->voltCompiledPath,
                    0777,
                    true
                );
            }
        }
    }

    protected function setPhalconViewPath()
    {
        if (!isset($this->phalconViewPath)) {
            $this->phalconViewPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html/');
        }
    }

    protected function setPhalconViewLayoutPath()
    {
        if (!isset($this->phalconViewLayoutPath)) {
            $this->phalconViewLayoutPath =
                base_path('applications/' . $this->applicationInfo['name'] .
                          '/Views/' . $this->view['name'] .
                          '/html/layouts/');
        }
    }

    protected function setPhalconViewLayoutFile()
    {
        if (!isset($this->phalconViewLayoutFile)) {
            $this->phalconViewLayoutFile =
                json_decode($this->view['settings'], true)['layout'];
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

    public function getViewsData()
    {
        return $this->viewsData;
    }

    protected function setApplicationInfo()
    {
        if (!$this->applicationInfo) {
            $this->applicationInfo = $this->applications->getApplicationInfo();

            if ($this->applicationInfo) {

                $applicationDefaults = $this->applications->getApplicationDefaults($this->applicationInfo['name']);
            } else {
                $applicationDefaults = null;
            }
            if ($this->applicationInfo && $applicationDefaults) {

                $applicationName = $applicationDefaults['application'];

                $viewsName = $applicationDefaults['view'];

                if (!$this->view) {
                    $this->getApplicationView($viewsName, $this->applicationInfo['id']);
                }

                $this->cache = json_decode($this->view['settings'], true)['cache'];
            }
        }
    }

    public function getAllViews($conditions = null)
    {
        if (!$this->views) {
            $this->views = ViewsModel::find($conditions, 'views')->toArray();
        }

        return $this;
    }

    protected function getApplicationView($name, $id)
    {
        $this->view =
                $this->views[array_search($id, array_column($this->views, 'application_id'))];
    }


}