<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Helper\Json;

class Dashboard
{
    public function register($db, $componentFile)
    {
        $db->insertAsDict(
            'basepackages_dashboards',
            [
                'name'                  => 'Default',
                'app_id'                => 1,
                'created_by'            => 1,
                'settings'              => Json::encode(['maxWidgets'=>10])
            ]
        );
    }
}