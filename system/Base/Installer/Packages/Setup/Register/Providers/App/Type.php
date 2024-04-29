<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers\App;

class Type
{
    public function register($db, $ff, $typeFile)
    {
        $type =
            [
                'name'                      => $typeFile['name'],
                'app_type'                  => $typeFile['app_type'],
                'description'               => $typeFile['description']
            ];

        if ($db) {
            $db->insertAsDict('service_provider_apps_types', $type);
        }

        if ($ff) {
            $appTypeStore = $ff->store('service_provider_apps_types');

            $appTypeStore->updateOrInsert($type);
        }
    }
}