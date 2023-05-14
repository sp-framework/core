<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\StorageAttributes;
use Phalcon\Helper\Json;
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
            if ($result['score'] === 0) {
                $result['score'] = 1;
            }

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

    public function formatJson($data)
    {
        if (!isset($data['json'])) {
            $this->addResponse('Json data not provided.', 1);

            return;
        }

        if (is_array($data['json'])) {
            $data['json'] = Json::encode($data['json'], JSON_UNESCAPED_SLASHES);
        }

        $return = "\n";
        $indent = "\t";
        $formatted_json = '';
        $quotes = false;
        $arrayLevel = 0;

        for ($i = 0; $i < strlen($data['json']); $i++) {
            $prefix = '';
            $suffix = '';

            switch ($data['json'][$i]) {
                case '"':
                    $quotes = !$quotes;
                    break;

                case '[':
                    $arrayLevel++;
                    break;

                case ']':
                    $arrayLevel--;
                    $prefix = $return;
                    $prefix .= str_repeat($indent, $arrayLevel);
                    break;

                case '{':
                    $arrayLevel++;
                    $suffix = $return;
                    $suffix .= str_repeat($indent, $arrayLevel);
                    break;

                case ':':
                    $suffix = ' ';
                    break;

                case ',':
                    if (!$quotes) {
                        $suffix = $return;
                        $suffix .= str_repeat($indent, $arrayLevel);
                    }
                    break;

                case '}':
                    $arrayLevel--;

                case ']':
                    $prefix = $return;
                    $prefix .= str_repeat($indent, $arrayLevel);
                    break;
            }

            $formatted_json .= $prefix.$data['json'][$i].$suffix;
        }

        $this->addResponse('Success', 0, ['formatted_json' => $formatted_json]);

        return $formatted_json;
    }
}