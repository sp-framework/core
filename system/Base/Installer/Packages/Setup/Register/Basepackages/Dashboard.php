<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

class Dashboard
{
    public function register($db, $ff, $componentFile, $helper)
    {
        $dashboard =
            [
                'name'                  => 'Default',
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
    }
}