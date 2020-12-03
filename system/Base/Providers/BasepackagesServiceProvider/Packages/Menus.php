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

        $this->cacheTools->setCache('menus', $buildMenu);

        return $buildMenu;
    }

    protected function filterMenu($applicationId)
    {
        $menus = [];

        $filter =
            $this->model->filter(
                function($menu) use ($applicationId) {
                    if ($menu->application_id === $applicationId) {
                        return $menu;
                    }
                }
            );

        foreach ($filter as $key => $value) {
            $menus[$key] = $value->toArray();
        }

        return $menus;
    }
}