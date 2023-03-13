<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

use Phalcon\Helper\Json;

class Menu
{
    public function register($db, $appType, array $menu)
    {
        if (isset($menu['seq'])) {
            $sequence = $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $insertMenu = $db->insertAsDict(
            'basepackages_menus',
            [
                'menu'                  => Json::encode($menu),
                'apps'                  => Json::encode(['1' => ['enabled'  => true]]),
                'app_type'              => $appType,
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