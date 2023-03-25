<?php

namespace Apps\Dash\Components\Dashboards;

use Phalcon\Helper\Arr;

abstract class WidgetsComponent
{
    protected $componentObj;

    protected $component;

    protected $view;

    public function init($componentObj, $component)//Init from Basecomponent
    {
        $this->componentObj = $componentObj;

        $this->component = $component;

        $this->view = $this->componentObj->view;

        $this->view->component = $component;

        $this->view->componentName = 'dashboards';

        $this->view->appRoute = $this->componentObj->apps->getAppInfo()['route'];

        $this->view->componentId =
            strtolower($this->view->appRoute) . '-' . strtolower($this->view->componentName);

        $this->view->sectionId = 'main';

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

        $this->view->setViewsDir($this->views->getPhalconViewPath() . $this->component['route']);

        return $this;
    }

    public function info($widget)
    {
        return $this->view->getPartial('widgets/' . strtolower($widget['method']) . '/info');
    }

    public function getWidgetContent($widget, $data = [])
    {
        if (count($data) > 0) {
            $widget['data'] = $data;
        }

        try {
            return $this->view->getPartial('widgets/' . strtolower($widget['method']) . '/view', ['widget' => $widget]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}