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
        $this->getAll($resetCache);

        return $this;
    }

    public function getWidgetsTree()
    {
        $componentsArr = $this->modules->components->components;

        $widgetsTree = [];

        foreach ($componentsArr as $componentKey => $component) {
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
    {//Do investigation on BaseComponent method useComponentWithView for this.
        $widget = $this->getById($id);

        if (!$task) {
            return $widget;
        }

        if ($widget['settings']) {
            $widget['settings'] = JSON::decode($widget['settings'], true);
        }
        $widgetMethod = $widget['method'];

        $component = $this->modules->components->getComponentById($widget['component_id']);

        try {
            if (class_exists($component['class'])) {
                $componentObj = new $component['class'];

                $widgetClass = $componentObj->widgets->init($componentObj, $component);
            }
        } catch (\Exception $e) {
            throw $e;
        }

        if ($widgetClass) {
            $widgetsReflection = new \ReflectionClass($widgetClass);

            if (isset($widgetMethod) && $widgetsReflection->hasMethod($widgetMethod)) {
                if ($task === 'info') {
                    $widget['info'] = $widgetClass->info($widget);
                } else if ($task === 'content') {
                    $widget['content'] = $widgetClass->$widgetMethod($widget, $dashboardWidget);
                }
            }

            return $widget;
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

    public function getWidgetsContent(array $data)
    {
        $widgetsData = [];

        foreach ($data as $key => $dashboardWidget) {
            $dashboardWidget['settings'] = $this->helper->decode($dashboardWidget['settings'], true);
            $widgetsData[$key] = $dashboardWidget;
            $widget = $this->getWidget($dashboardWidget['widget_id'], 'content', $dashboardWidget);
            $widgetsData[$key]['widget'] = $widget;
        }

        $widgetsData = msort($widgetsData, 'sequence');

        $this->addResponse('Ok', 0, ['widgetsData' => $widgetsData]);

        return $widgetsData;
    }
}