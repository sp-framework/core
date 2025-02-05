<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

class Dashboard
{
    public function register($db, $ff, $componentFile, $helper)
    {
        $dashboard =
            [
                'name'                  => 'Core Default',
                'app_id'                => 1,
                'app_default'           => 1,
                'created_by'            => 1,
                'settings'              => $helper->encode($componentFile['settings'])
            ];

        if ($db) {
            $db->insertAsDict('basepackages_dashboards', $dashboard);
        }

        if ($ff) {
            $dashboardStore = $ff->store('basepackages_dashboards');

            $dashboardStore->updateOrInsert($dashboard);
        }

        //Register Timezone Widget
        $widget =
            [
                "dashboard_id"              =>  1,
                "sequence"                  =>  0,
                "widget_id"                 =>  1,
                "settings"                  =>
                    $helper->encode([
                        "minW"              =>  "3",
                        "method"            =>  "worldClock",
                        "widget_id"         =>  "1",
                        "dashboard_id"      =>  "1",
                        "x"                 =>  "0",
                        "y"                 =>  "0",
                        "clocks"            => ["australiamelbourne"],
                        "w"                 =>  "12"
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