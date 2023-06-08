<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Helper\Json;

class Widgets
{
    public function register($db, $ff, $componentFile, $registeredComponentId, $path, $localContent)
    {
        $registeredComponentClassArr = explode('\\', $componentFile['class']);
        array_pop($registeredComponentClassArr);

        $widgetsClass = '\\' . implode('\\', $registeredComponentClassArr) . '\\Widgets';
        $widgetsPath = str_replace('Install/component.json', '', $path);

        include base_path($widgetsPath . 'Widgets.php');

        if (class_exists($widgetsClass)) {
            $widgets = new $widgetsClass;

            $widgetsReflection = new \ReflectionClass($widgets);

            foreach ($componentFile['widgets'] as $key => $widget) {
                if (isset($widget['method']) && $widgetsReflection->hasMethod($widget['method'])) {
                    $this->addToDb($db, $ff, $widget, $registeredComponentId);
                }
            }
        }
    }

    protected function addToDb($db, $ff, $widget, $registeredComponentId)
    {
        $widgetToAdd =
            [
                'name'                  => $widget['name'],
                'method'                => $widget['method'],
                'component_id'          => $registeredComponentId,
                'multiple'              => isset($widget['multiple']) && $widget['multiple'] === true ? 1 : 0,
                'max_multiple'          => isset($widget['max_multiple']) ? $widget['max_multiple'] : 5,//Max instances of same widget
                'settings'              => isset($widget['settings']) ? Json::encode($widget['settings']) : null
            ];

        if ($db) {
            $db->insertAsDict('basepackages_widgets', $widgetToAdd);
        }

        if ($ff) {
            $WidgetStore = $ff->store('basepackages_widgets');

            $WidgetStore->updateOrInsert($widgetToAdd);
        }
    }
}