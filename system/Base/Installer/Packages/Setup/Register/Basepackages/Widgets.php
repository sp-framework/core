<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use ReflectionClass;

/**
 * Seeds component widget definitions into basepackages_widgets.
 */
class Widgets
{
    /**
     * Helpers service instance.
     *
     * @var mixed
     */
    protected mixed $helper = null;

    /**
     * Registers discovered component widgets into database and FlatFile stores.
     *
     * @param mixed                $db                    PDO database connection adapter.
     * @param mixed                $ff                    FlatFile database manager.
     * @param array<string, mixed> $componentFile         Component metadata array.
     * @param mixed                $registeredComponentId Component ID.
     * @param string               $path                  Path to component directory or file.
     * @param mixed                $localContent          Flysystem local content adapter.
     * @param mixed                $helper                Helpers service instance.
     *
     * @return void
     */
    public function register(
        mixed $db,
        mixed $ff,
        array $componentFile,
        mixed $registeredComponentId,
        string $path,
        mixed $localContent,
        mixed $helper
    ): void {
        $this->helper = $helper;

        $componentClass = $componentFile['class'] ?? '';
        $registeredComponentClassArr = explode('\\', (string) $componentClass);
        array_pop($registeredComponentClassArr);

        $widgetsClass = '\\' . implode('\\', $registeredComponentClassArr) . '\\Widgets';
        $widgetsPath = str_replace('Install/component.json', '', $path);
        $widgetsFile = base_path($widgetsPath . 'Widgets.php');

        if (file_exists($widgetsFile)) {
            include_once $widgetsFile;
        }

        if (class_exists($widgetsClass)) {
            $widgets = new $widgetsClass();
            $widgetsReflection = new ReflectionClass($widgets);

            if (isset($componentFile['widgets']) && is_array($componentFile['widgets'])) {
                foreach ($componentFile['widgets'] as $widget) {
                    if (isset($widget['method']) && $widgetsReflection->hasMethod((string) $widget['method'])) {
                        $this->addToDb($db, $ff, $widget, $registeredComponentId);
                    }
                }
            }
        }
    }

    /**
     * Persists widget record into basepackages_widgets.
     *
     * @param mixed                $db                    PDO database connection adapter.
     * @param mixed                $ff                    FlatFile database manager.
     * @param array<string, mixed> $widget                Widget configuration array.
     * @param mixed                $registeredComponentId Component ID.
     *
     * @return void
     */
    protected function addToDb(mixed $db, mixed $ff, array $widget, mixed $registeredComponentId): void
    {
        $widgetToAdd = [
            'name'         => $widget['name'] ?? '',
            'method'       => $widget['method'] ?? '',
            'component_id' => $registeredComponentId,
            'app_type'     => 'core',
            'multiple'     => (isset($widget['multiple']) && $widget['multiple'] === true) ? 1 : 0,
            'max_multiple' => (int) ($widget['max_multiple'] ?? 5),
            'settings'     => isset($widget['settings']) ? $this->helper->encode($widget['settings']) : $this->helper->encode([])
        ];

        if ($db) {
            $db->insertAsDict('basepackages_widgets', $widgetToAdd);
        }

        if ($ff) {
            $widgetStore = $ff->store('basepackages_widgets');

            $widgetStore->updateOrInsert($widgetToAdd);
        }
    }
}