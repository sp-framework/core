<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
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

    public function getWidgetById(int $id)
    {
        foreach($this->widgets as $widget) {
            if ($widget['id'] == $id) {
                return $widget;
            }
        }

        return false;
    }

    public function getWidgetInfo(int $id)
    {
        $widget = $this->getWidgetById($id);

        $widgetMethod = $widget['method'];
        $component = $this->modules->components->getComponentById($widget['component_id']);

        try {
            if (class_exists($component['class'])) {
                $widgetClass = (new $component['class'])->widgets;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        if ($widgetClass) {
            $widgetsReflection = new \ReflectionClass($widgetClass);

            if (isset($widgetMethod) && $widgetsReflection->hasMethod($widgetMethod)) {
                return $widgetClass->info($component['route'], $widgetMethod);
            }
        }

        return false;
    }

    protected function initWidget($widget)
    {

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
}