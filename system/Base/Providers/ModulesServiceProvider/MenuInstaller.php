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
        try {
            $file = 'apps/' . implode('/', array_slice(explode('\\', get_class($componentClass)), 1, -1)) . '/component.json';

            if ($this->localContent->fileExists($file)) {
                $installComponentJsonFile = $this->helper->decode($this->localContent->read($file), true);

                $menu = $this->basepackages->menus->getMenusByRouteForAppType($installComponentJsonFile['route'], $installComponentJsonFile['app_type']);

                if (!isset($installComponentJsonFile['menu']) ||
                    (isset($installComponentJsonFile['menu']) && $installComponentJsonFile['menu'] == 'false')
                ) {
                    if ($menu) {
                        return $this->uninstallMenu($componentClass);
                    }

                    return true;
                }

                if ($menu) {
                    $this->basepackages->menus->updateMenu($menu['id'], $installComponentJsonFile);
                } else {
                    $menu = $this->basepackages->menus->addMenu($installComponentJsonFile);
                }

                //Assign MenuId
                $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
                $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

                $component = $this->modules->components->getComponentByClass($componentClass);

                if ($component) {
                    $component['menu'] = $installComponentJsonFile['menu'];
                    $component['menu_id'] = $menu['id'];

                    $this->modules->components->update($component);
                }
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            throw $e;
        }

        if ($this->opCache) {
            $this->opCache->removeCache('menus', 'core');
            $this->opCache->removeCache('components', 'core');
        }

        return true;
    }

    public function uninstallMenu($componentClass)
    {
        //Get MenuId
        $componentClassArr = array_slice(explode('\\', get_class($componentClass)), 1, -2);
        $componentClass = 'Apps\\' . implode('\\', $componentClassArr) . '\\' . $this->helper->last($componentClassArr) . 'Component';

        $component = $this->modules->components->getComponentByClass($componentClass);

        if ($component) {
            if ($this->opCache) {
                $this->opCache->removeCache('menus', 'core');
            }

            return $this->basepackages->menus->remove($component['menu_id']);
        }

        return false;
    }
}