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
        // var_dump($buildMenu);die();

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
                $menu = $this->get(['id' => $menuId]);

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

    public function get(array $data = [], bool $resetCache = false)
    {
        if (count($data) === 0) {
            return $this->menus;
        }

        if (isset($data['id'])) {
            return $this->getById($data['id']);
        }
    }

    public function add(array $data)
    {
        return;
    }

    public function update(array $data)
    {
        if ($this->updateToDb($data)) {
            $this->addResponse('Updated menu');
        } else {
            $this->addResponse('Error updating menu.', 1);
        }
    }

    public function remove(array $data)
    {
        return;
    }
}