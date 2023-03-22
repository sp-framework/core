<?php

namespace Apps\Dash\Components\Dashboards;

use Phalcon\Helper\Arr;

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

        $this->view->component = $component;

        $this->view->componentName = strtolower(str_replace('Component', '', $this->componentObj->reflection->getShortName()));

        $this->view->appRoute = $this->componentObj->apps->getAppInfo()['route'];

        $this->view->componentId =
            strtolower($this->view->appRoute) . '-' . strtolower($this->view->componentName);

        $reflection = Arr::sliceRight(explode('\\', $this->componentObj->reflection->getName()), 3);

        if (count($reflection) === 1) {
            $parents = str_replace('Component', '', Arr::last($reflection));
            $this->view->parents = $parents;
            $this->view->parent = strtolower($parents);
        } else {
            $reflection[Arr::lastKey($reflection)] =
                str_replace('Component', '', Arr::last($reflection));

            $parents = $reflection;

            $this->view->parents = $parents;
            $this->view->parent = strtolower(Arr::last($parents));
        }

        $this->views = $this->componentObj->modules->views;

        $this->views->setPhalconViewPath();

        $this->view->setViewsDir($this->views->getPhalconViewPath());

        return $this;
    }

    public function info($route, $widgetMethod)
    {
        return $this->view->pick($route . '/widgets/' . strtolower($widgetMethod) . '/info');
    }

    public function getWidgetContent($route, $widgetMethod, $data = [])
    {
        try {
            return $this->view->getPartial('widgets/' . strtolower($widgetMethod) . '/view', $data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}