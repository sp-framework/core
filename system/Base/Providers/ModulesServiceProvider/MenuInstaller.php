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

                if (!isset($installComponentJsonFile['menu']) ||
                    (isset($installComponentJsonFile['menu']) && (bool) $installComponentJsonFile['menu'] === false)
                ) {
                    return true;
                }

                $menu = $this->basepackages->menus->getMenusByRouteForAppType($installComponentJsonFile['route'], $installComponentJsonFile['app_type']);

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

        return true;
    }

    public function uninstallMenu($menuId)
    {
        return $this->basepackages->menus->remove($menuId);
    }
}