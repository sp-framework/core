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
                'application_id'    => 1,
                'menu'              => Json::encode($menu),
                'sequence'          => $sequence
            ]
        );

        if ($insertMenu) {
            return $db->lastInsertId();
        } else {
            return null;
        }
    }
}