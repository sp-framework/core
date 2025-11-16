<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesWidgets;

class Widgets extends BasePackage
{
    protected $modelToUse = BasepackagesWidgets::class;

    public $widgets;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('widgets', 'core')) {
                $this->widgets = $this->opCache->getCache('widgets', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('widgets', $this->widgets, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function getWidgetsTree()
    {
        $componentsArr = $this->modules->components->components;

        $widgetsTree = [];

        foreach ($componentsArr as $componentKey => $component) {
            if ($component['app_type'] !== $this->apps->getAppInfo()['app_type']) {
                continue;
            }

            $componentWidgets = $this->getWidgetsByComponentId($component['id']);

            if (count($componentWidgets) > 0) {
                $widgetsTree[$componentKey]['id'] = $component['id'];
                $widgetsTree[$componentKey]['title'] = $component['name'];

                foreach ($componentWidgets as $key => $componentWidget) {
                    $widgetsTree[$componentKey]['childs'][$key]['id'] = $componentWidget['id'];
                    $widgetsTree[$componentKey]['childs'][$key]['title'] = $componentWidget['name'];
                    $widgetsTree[$componentKey]['childs'][$key]['data']['method'] = $componentWidget['method'];
                    $widgetsTree[$componentKey]['childs'][$key]['data']['component_id'] = $componentWidget['component_id'];
                }
            }
        }

        return $widgetsTree;
    }

    public function getWidget(int $id, $task = null, $dashboardWidget = [])
    {
        if (isset($this->widgets[$id])) {
            $widget = $this->widgets[$id];
        } else {
            $widget = $this->getById($id);
        }

        if (!isset($widget)) {
            return false;
        }

        if (!$task) {
            return $widget;
        }

        if ($this->opCache && isset($dashboardWidget['getWidgetData']) && isset($widget['content']) && $task === 'content') {
            return $widget;
        }

        if ($widget['settings']) {
            if (is_string($widget['settings'])) {
                $widget['settings'] = $this->helper->decode($widget['settings'], true);
            }
        }

        $widgetMethod = $widget['method'];

        $component = $this->modules->components->getComponentById($widget['component_id']);

        try {
            if (class_exists($component['class'])) {
                $componentObj = new $component['class'];

                try {
                    $componentObj->checkComponentWidgets();
                } catch (\throwable $e) {
                    return false;
                }
            }

            if ($componentObj->widgets) {
                $widgetsReflection = new \ReflectionClass($componentObj->widgets);

                if (isset($widgetMethod) && $widgetsReflection->hasMethod($widgetMethod)) {
                    if ($task === 'info') {
                        $widget['info'] = $componentObj->widgets->info($widget);
                    } else if ($task === 'content') {
                        try {
                            $widget['content'] = $componentObj->widgets->$widgetMethod($widget, $dashboardWidget);

                            if ($this->opCache && isset($dashboardWidget['getWidgetData'])) {
                                $this->widgets[$id] = $widget;

                                $this->opCache->setCache('widgets', $this->widgets, 'core');
                            }
                        } catch (\throwable $e) {
                            return false;
                        }
                    }

                    return $widget;
                }

                return false;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return false;
    }

    public function getWidgetsByComponentId($componentId)
    {
        $widgets = [];

        foreach($this->widgets as $widget) {
            if ($widget['component_id'] === $componentId) {
                $widgets[$widget['id']] = $widget;
            }
        }

        return $widgets;
    }

    public function getWidgetByMethodAndAppType($method, $appType)
    {
        foreach($this->widgets as $widget) {
            if ($widget['method'] === $method &&
                $widget['app_type'] === $appType
            ) {
                return $widget;
            }
        }

        return false;
    }
}