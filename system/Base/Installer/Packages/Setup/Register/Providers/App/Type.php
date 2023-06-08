<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers\App;

class Type
{
    public function register($db, $ff)
    {
        $types =
            [
                '1'   =>
                    [
                        'app_type'      => 'core',
                        'name'          => 'Core',
                        'description'   => 'Core app to manage core.',
                    ],
                '2'   =>
                    [
                        'app_type'      => 'dash',
                        'name'          => 'Dash',
                        'description'   => 'Dash app to manage dash.',
                    ]
            ];


        foreach ($types as $key => $type) {
            $appType =
                [
                    'app_type'                  => $type['app_type'],
                    'name'                      => $type['name'],
                    'description'               => $type['description']
                ];

            if ($db) {
                $db->insertAsDict('service_provider_apps_types', $appType);
            }

            if ($ff) {
                $appTypeStore = $ff->store('service_provider_apps_types');

                $appTypeStore->updateOrInsert($appType);
            }
        }
    }
}