<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

class Widgets
{
    public function register($db, $componentFile, $registeredComponentId, $path, $localContent)
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
                    $this->addToDb($db, $widget, $registeredComponentId);
                }
            }
        }
    }

    protected function addToDb($db, $widget, $registeredComponentId)
    {
        $db->insertAsDict(
            'basepackages_widgets',
            [
                'name'                  => $widget['name'],
                'method'                => $widget['method'],
                'component_id'          => $registeredComponentId
            ]
        );
    }
}