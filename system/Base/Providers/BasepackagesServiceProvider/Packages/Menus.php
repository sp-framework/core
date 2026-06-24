<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesMenus;

class Menus extends BasePackage
{
    protected $modelToUse = BasepackagesMenus::class;

    protected $packageName = 'menus';

    public $menus;

    public function init(bool $resetCache = false)
    {
        if ($this->opCache) {
            if (!$resetCache && $this->opCache->checkCache('menus', 'core')) {
                $this->menus = $this->opCache->getCache('menus', 'core');
            } else {
                $this->getAll($resetCache);

                $this->opCache->setCache('menus', $this->menus, 'core');
            }
        } else {
            $this->getAll($resetCache);
        }

        return $this;
    }

    public function buildMenusForApp($app)
    {
        $menus = $this->getMenusForApp($app);

        $buildMenu = $this->buildMenus($menus);

        return $buildMenu;
    }

    public function buildMenusForAppWithPermissions($app, $permissions)
    {
        $menus = $this->getMenusForApp($app);

        $buildMenu = $this->buildMenus($menus, $permissions);

        return $buildMenu;
    }

    public function buildMenus($menus = null, $permissions = null)
    {
        if ($menus === null) {
            $menus = $this->menus;
        }

        $buildMenu = [];

        foreach (msort(array: $menus, key: 'sequence', preserveKey: true) as $key => $menu) {
            if (is_string($menu['menu'])) {
                $menu['menu'] = $this->helper->decode($menu['menu'], true);
            }

            if ($permissions) {
                if (!isset($permissions[$menu['component_id']]['view']) ||
                    isset($permissions[$menu['component_id']]['view']) && $permissions[$menu['component_id']]['view'] === 1
                ) {
                    if ($menu['menu']) {
                        $buildMenu = array_replace_recursive($buildMenu, $menu['menu']);
                    }
                }
            } else {
                if ($menu['menu']) {
                    $buildMenu = array_replace_recursive($buildMenu, $menu['menu']);
                }
            }
        }

        return $buildMenu;
    }

    public function getMenusForApp($app)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            $menu['apps'] = $this->helper->decode($menu['apps'], true);

            if (count($menu['apps']) > 0) {
                if (isset($menu['apps'][$app['id']]) &&
                    $menu['app_type'] === $app['app_type'] &&
                    $menu['apps'][$app['id']]['enabled'] === true
                ) {
                    $menus[$menu['id']] = $menu;
                }
            }

        }

        return $menus;
    }

    public function getMenusForAppType($appType, $buildMenu = true)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            if ($menu['app_type'] === $appType) {
                if (is_string($menu['menu'])) {
                    $menu['menu'] = $this->helper->decode($menu['menu'], true);
                }
                if (is_string($menu['apps'])) {
                    $menu['apps'] = $this->helper->decode($menu['apps'], true);
                }

                $menus[$menu['id']] = $menu;
            }
        }

        if ($buildMenu) {
            return $this->buildMenus($menus);
        }

        return $menus;
    }

    public function getMenusByComponentIdForAppType($componentId, $appType)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            if ($menu['component_id'] === $componentId &&
                $menu['app_type'] === $appType
            ) {
                return $menu;
            }
        }

        return false;
    }

    public function getMenusByRouteForAppType($route, $appType)
    {
        $menus = [];

        foreach($this->menus as $menu) {
            if ($menu['route'] === $route &&
                $menu['app_type'] === $appType
            ) {
                return $menu;
            }
        }

        return false;
    }

    public function updateMenus(array $data)
    {
        $menus = $data['menus'];

        if (is_string($menus)) {
            $menus = $this->helper->decode($menus, true);
        }

        if (count($menus) > 0) {
            foreach ($menus as $menuId => $value) {
                $menu = $this->getById($menuId);

                if (!$menu) {
                    continue;
                }

                if (is_string($menu['apps'])) {
                    $menu['apps'] = $this->helper->decode($menu['apps'], true);
                }

                $menu['apps'] = array_replace($menu['apps'], $value);

                $menu['apps'] = $this->helper->encode($menu['apps']);

                if (is_string($menu['menu'])) {
                    $menu['menu'] = $this->helper->decode($menu['menu'], true);
                }
                $menu['menu'] = $this->helper->encode($menu['menu'], JSON_UNESCAPED_SLASHES);

                $this->update($menu);
            }
        }

        $this->init(true);
    }

    public function addMenu(array $componentJsonFile, $component, $app = null)
    {
        $menu = $componentJsonFile['menu'];

        $menuApps = [];

        if ($app) {
            $menuApps[$app['id']] = ['enabled'  => true];
        }

        if (isset($menu['seq'])) {
            $sequence = $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $menu = $this->addSequence($menu, $sequence);

        $insertMenu = $this->add([
                'menu'                  => $this->helper->encode($menu),
                'apps'                  => $this->helper->encode($menuApps),
                'app_type'              => $componentJsonFile['app_type'],
                'component_id'          => $component['id'],
                'route'                 => $componentJsonFile['route'],
                'sequence'              => $sequence
            ]
        );

        if ($insertMenu) {
            return $this->packagesData->last;
        } else {
            return null;
        }
    }

    public function updateMenu($id, array $componentJsonFile, $component, $app = null)
    {
        $menu = $this->getById($id);

        $menuApps = [];

        if ($menu) {
            if (is_string($menu['menu'])) {
                $menu['menu'] = $this->helper->decode($menu['menu'], true);
            }

            if (is_string($menu['apps'])) {
                $menu['apps'] = $this->helper->decode($menu['apps'], true);
            }

            $menuApps = array_merge($menuApps, $menu['apps']);

            if ($app) {
                $menuApps[$app['id']] = ['enabled'  => true];
            }

            $menu = array_merge($menu['menu'], $componentJsonFile['menu']);
        } else {
            $menu = $componentJsonFile['menu'];
        }

        if (isset($menu['seq'])) {
            $sequence = $menu['seq'];
            unset($menu['seq']);
        } else {
            $sequence = 99;
        }

        $menu = $this->addSequence($menu, $sequence);

        $this->update([
                'id'                    => $id,
                'menu'                  => $this->helper->encode($menu),
                'apps'                  => $this->helper->encode($menuApps),
                'app_type'              => $componentJsonFile['app_type'],
                'component_id'          => $component['id'],
                'route'                 => $componentJsonFile['route'],
                'sequence'              => $sequence
            ]
        );

        return true;
    }

    public function removeMenu(array $data)
    {
        $menu = $this->getById($data['id']);

        $remove = $this->remove($menu['id']);

        if ($remove) {
            $this->addResponse('Menu Removed');

            return true;
        }

        $this->addResponse('Error removing menu', 1);

        return false;
    }

    protected function addSequence($menu, $sequence)
    {
        foreach ($menu as &$value) {
            if (!is_array($value)) {
                continue;
            }

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