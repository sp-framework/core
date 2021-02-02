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

        $cachedMenu = $this->cacheTools->getCache('menus');

        if ($cachedMenu) {
            return $cachedMenu;
        }

        $buildMenu = [];

        foreach (msort($menus, 'sequence') as $key => $menu) {
            $menu = Json::decode($menu['menu'], true);
            if ($menu) {
                $buildMenu = array_replace_recursive($buildMenu, $menu);
            }
        }

        $this->cacheTools->setCache('menus_' . $appId, $buildMenu);

        return $buildMenu;
    }

    public function getMenusForApp($appId)
    {
        $menus = [];

        $filter =
            $this->model->filter(
                function($menu) use ($appId) {
                    $menu = $menu->toArray();

                    $menu['apps'] = Json::decode($menu['apps'], true);
                    if (count($menu['apps']) > 0) {
                        if (isset($menu['apps'][$appId]) &&
                            $menu['apps'][$appId]['enabled'] === true
                        ) {

                            return $menu;
                        }
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $menus[$value['id']] = $value;
        }

        return $menus;
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

                $this->update($menu);
            }
        }

        $this->basepackages->menus->init(true);
    }
}