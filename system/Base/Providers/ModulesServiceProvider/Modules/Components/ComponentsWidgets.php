<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Components;

use Phalcon\Helper\Arr;

class ComponentsWidgets
{
    protected $componentObj;

    protected $component;

    protected $view;

    protected $views;

    public function init($componentObj, $component)//Init from Basecomponent
    {
        $this->componentObj = $componentObj;

        $this->component = $component;

        $this->view = $this->componentObj->view;

        $this->view->component = $component;

        if ($component['route'] !== 'pages') {
            $this->view->componentName = 'dashboards';
        } else {
            $this->view->componentName = 'widgets';
        }

        $this->view->appRoute = $this->componentObj->apps->getAppInfo()['route'];

        $this->view->componentId =
            strtolower($this->view->appRoute) . '-' . strtolower($this->view->componentName);

        $this->view->sectionId = 'main';

        $reflection = $this->componentObj->helper->sliceRight(explode('\\', $this->componentObj->reflection->getName()), 3);

        if (count($reflection) === 1) {
            $parents = str_replace('Component', '', $this->componentObj->helper->last($reflection));
            $this->view->parents = $parents;
            $this->view->parent = strtolower($parents);
        } else {
            $reflection[$this->componentObj->helper->lastKey($reflection)] =
                str_replace('Component', '', $this->componentObj->helper->last($reflection));

            $parents = $reflection;

            $this->view->parents = $parents;
            $this->view->parent = strtolower($this->componentObj->helper->last($parents));
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

    public function settings($widget, $pagewidget = null)
    {
        return $this->view->getPartial('widgets/' . strtolower($widget['method']) . '/settings', ['pagewidget' => $pagewidget]);
    }

    public function getWidgetContent($widget, $data = [])
    {
        if (count($data) > 0) {
            if (isset($widget['data'])) {
                $widget['data'] = array_merge_recursive($widget['data'], $data);
            } else {
                $widget['data'] = $data;
            }
        }

        try {
            return $this->view->getPartial('widgets/' . strtolower($widget['method']) . '/view', ['widget' => $widget]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}