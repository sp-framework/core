<?php

namespace System\Base\Installer\Packages\Setup\Register;

use Phalcon\Helper\Json;

class Menu
{
    public function register($db, array $menu, int $sequence)
    {
        $insertMenu = $db->insertAsDict(
            'menus',
            [
                'menu'                  => Json::encode($menu),
                'applications'          =>
                    Json::encode(
                        ['1' =>
                            ['enabled' => true,
                             // 'sequence' => $sequence
                            ]
                        ]
                    ),
                'sequence'              => $sequence
            ]
        );

        if ($insertMenu) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}