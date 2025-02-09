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
                    $this->basepackages->menus->addMenu($installComponentJsonFile);
                }
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            trace([$e]);
            throw $e;
        }

        return true;
    }
}