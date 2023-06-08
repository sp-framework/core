<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Helper\Json;

class Dashboard
{
    public function register($db, $ff, $componentFile)
    {
        $dashboard =
            [
                'name'                  => 'Default',
                'app_id'                => 1,
                'created_by'            => 1,
                'settings'              => Json::encode($componentFile['settings'])
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