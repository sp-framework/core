<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

class Dashboard
{
    public function register($db, $ff, $componentFile)
    {
        $dashboard =
            [
                'name'                  => 'Default',
                'app_id'                => 1,
                'created_by'            => 1,
                'settings'              => $this->helper->encode($componentFile['settings'])
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