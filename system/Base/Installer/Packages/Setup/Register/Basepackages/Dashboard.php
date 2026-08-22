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

/**
 * Seeds default dashboard and initial widgets into basepackages_dashboards.
 */
class Dashboard
{
    /**
     * Registers default Core dashboard and timezone widget.
     *
     * @param mixed                $db            PDO database connection adapter.
     * @param mixed                $ff            FlatFile database manager.
     * @param array<string, mixed> $componentFile Component metadata array.
     * @param mixed                $helper        Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $componentFile, mixed $helper): void
    {
        $settings = isset($componentFile['settings']) ? $helper->encode($componentFile['settings']) : $helper->encode([]);

        $dashboard = [
            'name'       => 'Core Default',
            'app_type'   => 'core',
            'created_by' => 1,
            'settings'   => $settings
        ];

        if ($db) {
            $db->insertAsDict('basepackages_dashboards', $dashboard);
        }

        if ($ff) {
            $dashboardStore = $ff->store('basepackages_dashboards');

            $dashboardStore->updateOrInsert($dashboard);
        }

        $widget = [
            'dashboard_id' => 1,
            'sequence'     => 0,
            'widget_id'    => 1,
            'settings'     => $helper->encode([
                'minW'         => '3',
                'method'       => 'worldClock',
                'widget_id'    => '1',
                'dashboard_id' => '1',
                'x'            => '0',
                'y'            => '0',
                'clocks'       => ['australiamelbourne'],
                'w'            => '12'
            ])
        ];

        if ($db) {
            $db->insertAsDict('basepackages_dashboards_widgets', $widget);
        }

        if ($ff) {
            $widgetStore = $ff->store('basepackages_dashboards_widgets');

            $widgetStore->updateOrInsert($widget);
        }
    }
}