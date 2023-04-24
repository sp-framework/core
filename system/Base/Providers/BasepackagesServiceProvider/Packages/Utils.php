<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\StorageAttributes;
use System\Base\BasePackage;
use ZxcvbnPhp\Zxcvbn;

class Utils extends BasePackage
{
    public function init($container = null)
    {
        if ($container) {
            $this->container = $container;
        }

        return $this;
    }

    public function scanDir($directory, $sub = true, $exclude = [])
    {
        $files = [];
        $files['dirs'] = [];
        $files['files'] = [];

        if ($directory) {
            $files['files'] =
                $this->localContent->listContents($directory, $sub)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray();

            $files['dirs'] =
                $this->localContent->listContents($directory, $sub)
                ->filter(fn (StorageAttributes $attributes) => $attributes->isDir())
                ->map(fn (StorageAttributes $attributes) => $attributes->path())
                ->toArray();

            if (count($exclude) > 0) {
                foreach ($exclude as $excluded) {
                    foreach ($files['files'] as $key => $file) {
                        if (strpos($file, $excluded)) {
                            unset($files['files'][$key]);
                        }
                    }
                    foreach ($files['dirs'] as $key => $dir) {
                        if (strpos($dir, $excluded)) {
                            unset($files['dirs'][$key]);
                        }
                    }
                }
            }

            return $files;
        } else {
            return null;
        }
    }

    public function checkPwStrength(string $pass)
    {
        $checkingTool = new Zxcvbn();

        $result = $checkingTool->passwordStrength($pass);

        if ($result && is_array($result) && isset($result['score'])) {
            $this->addResponse('Checking Password Strength Success', 0, ['result' => $result['score']]);

            return $result['score'];
        }

        $this->addResponse('Error Checking Password Strength', 1);

        return false;
    }

    public function generateNewPassword()
    {
        $this->addResponse('Password Generate Successfully', 0, ['password' => $this->secTools->random->base62(12)]);
    }
}