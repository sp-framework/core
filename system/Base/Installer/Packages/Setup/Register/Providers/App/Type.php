<?php

namespace System\Base\Installer\Packages\Setup\Register\Providers\App;

class Type
{
    public function register($db)
    {
        $types =
            [
                '1'   =>
                    [
                        'app_type'      => 'core',
                        'name'          => 'Core',
                        'description'   => 'Core app to manage core.',
                    ]
            ];


        foreach ($types as $key => $type) {
            $db->insertAsDict(
                'service_provider_apps_types',
                [
                    'app_type'                  => $type['app_type'],
                    'name'                      => $type['name'],
                    'description'               => $type['description']
                ]
            );
        }
    }
}