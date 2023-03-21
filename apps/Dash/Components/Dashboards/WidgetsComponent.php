<?php

namespace Apps\Dash\Components\Dashboards;

abstract class WidgetsComponent
{
    protected $componentObj;

    protected $component;

    protected $view;

    public function init($componentObj, $component)
    {
        $this->componentObj = $componentObj;

        $this->component = $component;

        $this->view = $this->componentObj->view;

        return $this;
    }

    public function info($route, $widgetMethod)
    {
        return $this->view->pick($route . '/widgets/' . strtolower($widgetMethod) . '/info');
    }
}