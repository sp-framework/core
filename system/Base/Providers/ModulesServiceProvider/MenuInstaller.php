<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;

class MenuInstaller extends BasePackage
{
    public function installMenu($componentClass)
    {
        if (str_contains(get_class($componentClass), 'CoreServiceProvider')) {
            $adminComponents = $this->basepackages->utils->init($this->container)->scanDir('apps/Core/Components/', true);

            if (!$adminComponents || count($adminComponents) === 0) {
                return false;
            }

            foreach ($adminComponents['files'] as $adminComponentKey => $adminComponent) {
                if (strpos($adminComponent, 'component.json')) {
                    try {
                        $jsonFile =
                            $this->helper->decode(
                                $this->localContent->read($adminComponent),
                                true
                            );
                    } catch (\throwable $e) {
                        throw new \Exception($e->getMessage() . '. Problem reading component.json at location ' . $adminComponent);
                    }

                    $component = $this->modules->components->init(true)->getComponentByClass($jsonFile['class']);

                    if ($component) {
                        $menu = $this->basepackages->menus->init(true)->getMenusByComponentIdForAppType($component['id'], $component['app_type']);

                        if ($menu) {
                            $menu['component_id'] = $component['id'];

                            if (isset($component['menu_id']) && $jsonFile['menu'] == 'false') {
                                $this->uninstallMenu($jsonFile['class']);

                                $component['menu'] = false;
                                $component['menu_id'] = null;
                            } else {
                                $this->basepackages->menus->updateMenu($menu['id'], $jsonFile, $component, ['id' => 1]);
                            }
                        } else {
                            if ($jsonFile['menu'] && $jsonFile['menu'] != 'false') {
                                $menu = $this->basepackages->menus->addMenu($jsonFile, $component, ['id' => 1]);

                                $component['menu'] = $jsonFile['menu'];
                                $component['menu_id'] = $menu['id'];
                            }
                        }

                        $this->modules->components->update($component);
                    }
                }
            }
        } else {
            try {
                $file = 'apps/' . implode('/', array_slice(explode('\\', get_class($componentClass)), 1, -1)) . '/component.json';

                if ($this->localContent->fileExists($file)) {
                    $installComponentJsonFile = $this->helper->decode($this->localContent->read($file), true);

                    //Assign MenuId
                    $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
                    $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

                    $component = $this->modules->components->init(true)->getComponentByClass($componentClass);

                    if ($component) {
                        $menu = $this->basepackages->menus->init(true)->getMenusByRouteForAppType($installComponentJsonFile['route'], $installComponentJsonFile['app_type']);

                        if ($menu) {
                            $menu['component_id'] = $component['id'];

                            if (isset($component['menu_id']) && $installComponentJsonFile['menu'] == 'false') {
                                $this->uninstallMenu($installComponentJsonFile['class']);

                                $component['menu'] = false;
                                $component['menu_id'] = null;
                            } else {
                                $component['menu'] = $installComponentJsonFile['menu'];
                                $component['menu_id'] = $menu['id'];

                                $this->basepackages->menus->updateMenu($menu['id'], $installComponentJsonFile, $component);
                            }
                        } else {
                            if ($installComponentJsonFile['menu'] && $installComponentJsonFile['menu'] != 'false') {
                                $menu = $this->basepackages->menus->addMenu($installComponentJsonFile, $component);

                                $component['menu'] = $installComponentJsonFile['menu'];
                                $component['menu_id'] = $menu['id'];
                            }
                        }

                        $this->modules->components->update($component);
                    }
                }
            } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
                throw $e;
            }
        }

        if ($this->opCache) {
            $this->opCache->removeCache('menus', 'core');
            $this->opCache->removeCache('components', 'core');
        }

        return true;
    }

    public function uninstallMenu($componentClass)
    {
        if (!is_string($componentClass)) {
            $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
            $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';
        }

        $component = $this->modules->components->init(true)->getComponentByClass($componentClass);

        if ($component && $component['menu_id']) {
            if ($this->opCache) {
                $this->opCache->removeCache('menus', 'core');
            }

            return $this->basepackages->menus->remove($component['menu_id']);
        }

        return false;
    }
}