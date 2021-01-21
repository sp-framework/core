<?php

namespace System\Base\Installer\Packages\Setup\Register\Application;

class Type
{
    public function register($db)
    {
        $types =
            [
                '1'   =>
                    [
                        'app_type'      => 'dash',
                        'name'          => 'Dashboard',
                        'description'   => 'Dashboard. Can run modules that require a dashboard, like Admin, Cpanel or Dashboard.',
                    ],
                '2'    =>
                    [
                        'app_type'      => 'ecom',
                        'name'          => 'E-Commerce E-Shop',
                        'description'   => 'Online product catalogue and checkout system.',
                    ],
                '3'    =>
                    [
                        'app_type'      => 'pos',
                        'name'          => 'Point of Sales System',
                        'description'   => 'In-store checkout system.',
                    ],
                '4'    =>
                    [
                        'app_type'      => 'cms',
                        'name'          => 'Content Management System',
                        'description'   => 'Application to display any web content. Like a blog.',
                    ],
                '5'    =>
                    [
                        'app_type'      => 'other',
                        'name'          => 'Other/Custom',
                        'description'   => 'Used for any other/custom application. Modules for this app are developed under app route name.',
                    ]
            ];


        foreach ($types as $key => $type) {
            $db->insertAsDict(
                'application_types',
                [
                    'app_type'                  => $type['app_type'],
                    'name'                      => $type['name'],
                    'description'               => $type['description']
                ]
            );
        }
    }
}