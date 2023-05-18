<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMenus;

class Menus extends BasePackage
{
    protected $modelToUse = BasepackagesMenus::class;

    protected $packageNameS = 'Menu';

    public $menus;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function buildMenusForApp($appId)
    {
        $menus = $this->getMenusForApp($appId);

        $buildMenu = $this->buildMenus($menus);

        return $buildMenu;
    }

    public function buildMenus($menus = null)
    {
        if ($menus === null) {
            $menus = $this->menus;
        }

        $buildMenu = [];

        foreach (msort($menus, 'sequence') as $key => $menu) {
            $menu = Json::decode($menu['menu'], true);

            if ($menu) {
                $buildMenu = array_replace_recursive($buildMenu, $menu);
            }
        }

        return $buildMenu;
    }

    public function getMenusForApp($appId)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            $menu['apps'] = Json::decode($menu['apps'], true);

            if (count($menu['apps']) > 0) {
                if (isset($menu['apps'][$appId]) &&
                    $menu['apps'][$appId]['enabled'] === true
                ) {
                    $menus[$menu['id']] = $menu;
                }
            }

        }

        return $menus;
    }

    public function getMenusForAppType($appType)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            if ($menu['app_type'] === $appType) {
                $menus[$menu['id']] = $menu;
            }
        }

        return $this->buildMenus($menus);
    }

    public function updateMenus($data)
    {
        $menus = Json::decode($data['menus'], true);

        if (count($menus) > 0) {
            foreach ($menus as $menuId => $value) {
                $menu = $this->getById($menuId);

                $menu['apps'] = Json::decode($menu['apps'], true);

                $menu['apps'] = array_replace($menu['apps'], $value);

                $menu['apps'] = Json::encode($menu['apps']);

                $menu['menu'] = Json::decode($menu['menu'], true);
                $menu['menu'] = Json::encode($menu['menu'], JSON_UNESCAPED_SLASHES);

                $this->update($menu);
            }
        }

        $this->init(true);
    }

    public function addMenu($appType, array $menu)
    {
        if (isset($menu['seq'])) {
            $sequence = $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $menu = $this->addSequence($menu, $sequence);

        $insertMenu = $this->add([
                'menu'                  => Json::encode($menu),
                'apps'                  => Json::encode([]),
                'app_type'              => $appType,
                'sequence'              => $sequence
            ]
        );

        if ($insertMenu) {
            return $this->packagesData->last;
        } else {
            return null;
        }
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