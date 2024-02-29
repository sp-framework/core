<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages;

class Menu
{
    public function register($db, $ff, $appType, array $menu)
    {
        if (isset($menu['seq'])) {
            $sequence = $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $menu = $this->addSequence($menu, $sequence);

        $menuToRegister =
            [
                'menu'                  => $this->helper->encode($menu),
                'apps'                  => $this->helper->encode(['1' => ['enabled'  => true]]),
                'app_type'              => $appType,
                'sequence'              => $sequence
            ];

        if ($db) {
            $dbMenu = $db->insertAsDict('basepackages_menus', $menuToRegister);

            $dbMenuId = (int) $db->lastInsertId();
        }

        if ($ff) {
            $menuStore = $ff->store('basepackages_menus');

            $menuStore->updateOrInsert($menuToRegister);

            $ffMenuId = (int) $menuStore->getLastInsertedId();
        }

        if (isset($dbMenuId) && isset($ffMenuId)) {
            if ($dbMenuId == $ffMenuId) {
                return $dbMenuId;
            }

            throw new \Exception('Menu ids dont match for db and ff');
        } else if (isset($dbMenuId) && !isset($ffMenuId)) {
            return $dbMenuId;
        } else if (!isset($dbMenuId) && isset($ffMenuId)) {
            return $ffMenuId;
        }

        return null;
    }

    protected function addSequence($menu, $sequence)
    {
        foreach ($menu as $key => &$value) {
            if (!isset($value['seq'])) {
                $value['seq'] = $sequence;
            }

            if (isset($value['childs'])) {
                $value['childs'] = $this->addSequence($value['childs'], $sequence);
            }
        }

        return $menu;
    }
}