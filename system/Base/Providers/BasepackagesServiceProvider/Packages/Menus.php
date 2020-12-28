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

    public function getMenusForApplication($applicationId)
    {
        $menus = $this->filterMenu($applicationId);

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

    protected function filterMenu($applicationId)
    {
        $menus = [];

        $filter =
            $this->model->filter(
                function($menu) use ($applicationId) {
                    $menu = $menu->toArray();
                    // var_dump($menu);
                    $menu['applications'] = Json::decode($menu['applications'], true);
                    if (count($menu['applications']) > 0) {
                        if (isset($menu['applications'][$applicationId]) &&
                            $menu['applications'][$applicationId]['enabled'] === true
                        ) {
                            // $menu['sequence'] = $menu['applications'][$applicationId]['sequence'];
                            return $menu;
                        }
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $menus[$key] = $value;
        }

        return $menus;
    }
}