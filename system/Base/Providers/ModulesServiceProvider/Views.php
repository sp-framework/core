<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Di\DiInterface;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ViewBaseInterface;
use Phalcon\Mvc\View\Engine\Php as PhpTemplateService;
use Phalcon\Mvc\View\Engine\Volt;
use System\Base\Providers\ModulesServiceProvider\Views\ViewsData;

class Views
{
    private $container;

    protected $phalconView;

    protected $views;

    protected $applications;

    protected $applicationInfo;

    protected $db;

    protected $path;

    protected $cache;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->db = $this->container->getShared('db');

        $this->applications = $this->container->getShared('applications');

        $this->setApplicationInfo();

        $this->setPath();

        $this->registerVoltTemplateService();
    }

    protected function setPath()
    {
        $this->path =
            base_path('applications/' . $this->applicationInfo['name'] .
                      '/Views/' . $this->views['name'] .
                      '/html_compiled/');
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function getViewInfo()
    {
        return $this->views;
    }

    public function getViewsData()
    {
        return $this->viewsData;
    }

    public function registerPhalconView()
    {
        $this->phalconView = new View();

        $this->phalconView->setViewsDir(
            base_path('applications/Admin/Views/Default/html/')
        );

        $this->phalconView->registerEngines(
            [
                '.html'     => 'voltTemplateService',
                '.phtml'    => PhpTemplateService::class
            ]
        );

        return $this->phalconView;
    }

    protected function registerVoltTemplateService()
    {
        $this->container->setShared(
            'voltTemplateService',
            function(ViewBaseInterface $view) {

                $this->volt = new Volt($view, $this);

                if ($this->getShared('views')->getCache()) {
                    $always = false;
                } else {
                    $always = true;
                }

                $this->volt->setOptions(
                    [
                        'always'        => $always,
                        'separator'     => '-',
                        'stat'          => true,
                        'path'          => $this->getShared('views')->getPath()
                    ]
                );

                return $this->volt;
            }
        );
    }

    protected function setApplicationInfo()
    {
        $this->applicationInfo = $this->applications->getApplicationInfo();

        if ($this->applicationInfo) {

            $applicationDefaults = $this->applications->getApplicationDefaults($this->applicationInfo['name']);
        } else {
            $applicationDefaults = null;
        }
        if ($this->applicationInfo && $applicationDefaults) {

            $applicationName = $applicationDefaults['application'];

            $viewsName = $applicationDefaults['view'];

            if (!$this->views) {
                $this->getApplicationView($viewsName, $this->applicationInfo['id']);
            }

            $this->cache = json_decode($this->views['settings'], true)['cache'];
        }
    }

    protected function getApplicationView($name, $id)
    {
        $this->views =
            $this->db->fetchAll(
                "SELECT * FROM `views` WHERE `name` = '" . $name . "' AND `application_id` = '" . $id . "'"
            )[0];
    }
}