<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;

class WidgetInstaller extends BasePackage
{
    public function installWidget($componentClass)
    {
        try {
            $file = 'apps/' . implode('/', array_slice(explode('\\', get_class($componentClass)), 1, -1)) . '/component.json';

            if ($this->localContent->fileExists($file)) {
                $installComponentJsonFile = $this->helper->decode($this->localContent->read($file), true);

                $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
                $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

                $component = $this->modules->components->getComponentByClass($componentClass);

                if (!$component) {
                    throw new \Exception('Component with class: ' . $componentClass . ' not found!');
                }

                if (!isset($installComponentJsonFile['widgets']) ||
                    ((isset($installComponentJsonFile['widgets']) && (bool) $installComponentJsonFile['widgets'] === false) ||
                     (isset($installComponentJsonFile['widgets']) && count($installComponentJsonFile['widgets']) === 0))
                ) {
                    return true;
                }

                foreach ($installComponentJsonFile['widgets'] as $widgetArr) {
                    if (!isset($widgetArr['method'])) {
                        continue;
                    }

                    $widget = $this->basepackages->widgets->getWidgetByMethodAndAppType($widgetArr['method'], $component['app_type']);

                    if ($widget) {
                        $widgetToUpdate =
                            [
                                'id'                    => $widget['id'],
                                'name'                  => $widgetArr['name'],
                                'method'                => $widgetArr['method'],
                                'component_id'          => $component['id'],
                                'app_type'              => $component['app_type'],
                                'multiple'              => isset($widgetArr['multiple']) && $widgetArr['multiple'] === true ? 1 : 0,
                                'max_multiple'          => isset($widgetArr['max_multiple']) ? $widgetArr['max_multiple'] : 5,//Max instances of same widget
                                'settings'              => isset($widgetArr['settings']) ? $this->helper->encode($widgetArr['settings']) : null
                            ];

                        if ($this->basepackages->widgets->update($widgetToUpdate)) {
                            foreach ($component['widgets'] as &$componentWidget) {
                                if ($componentWidget['method'] === $widgetToUpdate['method']) {
                                    $componentWidget['id'] = $widgetToUpdate['id'];
                                }
                            }

                            $this->modules->components->update($component);
                        }
                    } else {
                        $widgetToAdd =
                            [
                                'name'                  => $widgetArr['name'],
                                'method'                => $widgetArr['method'],
                                'component_id'          => $component['id'],
                                'app_type'              => $component['app_type'],
                                'multiple'              => isset($widgetArr['multiple']) && $widgetArr['multiple'] === true ? 1 : 0,
                                'max_multiple'          => isset($widgetArr['max_multiple']) ? $widgetArr['max_multiple'] : 5,//Max instances of same widget
                                'settings'              => isset($widgetArr['settings']) ? $this->helper->encode($widgetArr['settings']) : null
                            ];

                        if ($this->basepackages->widgets->add($widgetToAdd)) {
                            $newWidget = $this->basepackages->widgets->packagesData->last;

                            foreach ($component['widgets'] as &$componentWidget) {
                                if ($componentWidget['method'] === $newWidget['method']) {
                                    $componentWidget['id'] = $newWidget['id'];
                                }
                            }

                            $this->modules->components->update($component);
                        }
                    }
                }
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            throw $e;
        }

        if ($this->opCache) {
            $this->opCache->removeCache('widgets', 'core');
        }

        return true;
    }

    public function uninstallWidget($componentClass)
    {
        //Get Component
        $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
        $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

        $component = $this->modules->components->getComponentByClass($componentClass);

        if (!$component) {
            throw new \Exception('Component with class: ' . $componentClass . ' not found!');
        }

        if ($component) {
            foreach ($component['widgets'] as $componentWidget) {
                $widget = $this->basepackages->widgets->getWidgetByMethodAndAppType($componentWidget['method'], $component['app_type']);

                if ($widget) {
                    $this->basepackages->widgets->remove($widget['id']);
                }
            }
        }

        if ($this->opCache) {
            $this->opCache->removeCache('widgets', 'core');
        }
    }
}