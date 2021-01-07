<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Menus as MenusModel;

class Menus extends BasePackage
{
    protected $modelToUse = MenusModel::class;

    protected $packageNameS = 'Menu';

    public $menus;

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function buildMenusForApplication($applicationId)
    {
        $menus = $this->getMenusForApplication($applicationId);

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

        $this->cacheTools->setCache('menus_' . $applicationId, $buildMenu);

        return $buildMenu;
    }

    public function getMenusForApplication($applicationId)
    {
        $menus = [];

        $filter =
            $this->model->filter(
                function($menu) use ($applicationId) {
                    $menu = $menu->toArray();

                    $menu['applications'] = Json::decode($menu['applications'], true);
                    if (count($menu['applications']) > 0) {
                        if (isset($menu['applications'][$applicationId]) &&
                            $menu['applications'][$applicationId]['enabled'] === true
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

                $menu['applications'] = Json::decode($menu['applications'], true);

                $menu['applications'] = array_replace($menu['applications'], $value);

                $menu['applications'] = Json::encode($menu['applications']);

                $this->update($menu);
            }
        }

        $this->basepackages->menus->init(true);
    }
}