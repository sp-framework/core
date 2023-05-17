<?php

namespace Apps\Core\Packages\Devtools\Modules;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use System\Base\BasePackage;

class DevtoolsModules extends BasePackage
{
    public function addModule($data)
    {
        if ($data['type'] === 'core') {
            $this->addResponse('Core already exists!', 1);

            return false;
        }

        $data = $this->checkTypeAndCategory($data);

        if ($data['type'] === 'components') {
            $moduleMethod = 'get' . ucfirst(substr($data['type'], 0, -1)) . 'ByAppTypeAndRepoAndRoute';
            $module = $this->modules->{$data['type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['route']);
        } else if ($data['type'] === 'packages' || $data['type'] === 'middlewares') {
            $moduleMethod = 'get' . ucfirst(substr($data['type'], 0, -1)) . 'ByAppTypeAndRepoAndClass';
            $module = $this->modules->{$data['type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['class']);
        } else if ($data['type'] === 'views') {
            $moduleMethod = 'get' . ucfirst(substr($data['type'], 0, -1)) . 'ByAppTypeAndRepoAndName';
            $module = $this->modules->{$data['type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['name']);
        }
        //Need to add more checks. Repo can be as simple as "https://.../" for local, in that case there should be more checks.
        if ($module) {
            $this->addResponse('Module already exists!', 1);

            return false;
        }

        $data['name'] = ucfirst(trim(str_replace('(Clone)', '', $data['name'])));
        $data['repo'] = trim(str_replace('(clone)', '', $data['repo']));
        $data['installed'] = '1';
        $data['updated_by'] = '0';

        if ($this->modules->{$data['type']}->add($data) &&
            $this->updateModuleJson($data) &&
            $this->generateNewFiles($data)
        ) {
            $this->addResponse('Module added');

            return;
        }

        $this->addResponse('Error adding Module', 1);
    }

    public function updateModule($data)
    {
        $data = $this->checkTypeAndCategory($data);

        if ($data['type'] === 'core') {
            if ($this->updateModuleJson($data)) {
                $this->addResponse('Module updated');

                return;
            }
        } else {
            $module = $this->modules->{$data['type']}->getById($data['id']);

            $module = array_merge($module, $data);

            if ($this->modules->{$data['type']}->update($module) &&
                $this->updateModuleJson($data)
            ) {
                $this->addResponse('Module updated');

                return;
            }
        }

        $this->addResponse('Error updating Module', 1);
    }

    // public function removeModule($data)
    // {
    //     $module = $this->getById($data['id']);

    //     if ($this->remove($module['id'])) {
    //         $this->addResponse('Removed module ' . $module['name']);
    //     } else {
    //         $this->addResponse('Error removing module.', 1);
    //     }
    // }

    protected function checkTypeAndCategory($data)
    {
        if (str_contains($data['app_type'], '"data"')) {
            $data['app_type'] = Json::decode($data['app_type'], true);
            if (isset($data['app_type']['data'][0])) {
                $data['app_type'] = $data['app_type']['data'][0];
            } else if (isset($data['app_type']['newTags'][0])) {
                $this->apps->types->add(
                    [
                        'app_type'  => strtolower($data['app_type']['newTags'][0]),
                        'name'      => $data['app_type']['newTags'][0]
                    ]
                );
                $data['app_type'] = strtolower($data['app_type']['newTags'][0]);
            }
        }

        if (str_contains($data['category'], '"data"')) {
            $data['category'] = Json::decode($data['category'], true);
            if (isset($data['category']['data'][0])) {
                $data['category'] = $data['category']['data'][0];
            } else if (isset($data['category']['newTags'][0])) {
                $data['category'] = strtolower($data['category']['newTags'][0]);
            }
        }

        return $data;
    }

    public function validateJson($data)
    {
        if (!isset($data['json'])) {
            $this->addResponse('Json data not provided.', 1);

            return;
        }

        try {
            $parser = new JsonParser();

            $result = $parser->lint($data['json']);

            $parser->parse($data['json'], JsonParser::DETECT_KEY_CONFLICTS);
        } catch (ParsingException | \throwable $e) {
            if ($result === null) {
                if (defined('JSON_ERROR_UTF8') && JSON_ERROR_UTF8 === json_last_error()) {
                    $this->addResponse('Json is not UTF-8, could not parse json data.', 1);

                    return;
                }
            }

            $this->addResponse($e->getDetails(), 1);

            throw $e;
        }

        if (isset($data['returnJson']) && $data['returnJson'] === 'array') {
            $data['json'] = Json::decode($data['json'], true);
        } else if (isset($data['returnJson']) && $data['returnJson'] === 'formatted') {
            $data['json'] = $this->basepackages->utils->formatJson($data);
        }

        $this->addResponse('Success', 0, ['json' => $data['json']]);

        return $data['json'];
    }

    public function getModuleTypes()
    {
        return
            [
                'components'    =>
                    [
                        'id'    => 'components',
                        'name'  => 'Components'
                    ],
                'packages'      =>
                    [
                        'id'    => 'packages',
                        'name'  => 'Packages'
                    ],
                'middlewares'   =>
                    [
                        'id'    => 'middlewares',
                        'name'  => 'Middlewares'
                    ],
                'views'         =>
                    [
                        'id'    => 'views',
                        'name'  => 'Views'
                    ],
            ];
    }

    public function getDefaultSettings()
    {
        $defaultSettings = [];

        return Json::encode($defaultSettings);
    }

    public function getDefaultDependencies($type)
    {
        $defaultDependencies =
            [
                'core'              => [],
                'components'        => [],
                'packages'          => [],
                'middlewares'       => [],
                'views'             => [],
                'external'          => []
            ];

        if ($type === 'views') {
            unset($defaultDependencies['external']);
        }

        return Json::encode($defaultDependencies);
    }

    protected function updateModuleJson($data)
    {
        $jsonFile = $this->getModuleJsonFileLocation($data);

        $data = $this->basepackages->utils->jsonDecodeData($data);

        $jsonContent = [];
        $jsonContent["name"] = $data["name"];
        if ($data['module_type'] === 'components') {
            $jsonContent["route"] = $data["route"];
        } else {
            $jsonContent["display_name"] = $data["display_name"];
        }
        $jsonContent["description"] = $data["description"];
        $jsonContent["module_type"] = $data["module_type"];
        $jsonContent["app_type"] = $data["app_type"];
        $jsonContent["category"] = $data["category"];
        $jsonContent["version"] = $data["version"];
        $jsonContent["repo"] = $data["repo"];
        if ($data['module_type'] !== 'views') {
            $jsonContent["class"] = $data["class"];
        }
        $jsonContent["dependencies"] = $data["dependencies"];
        if ($data['module_type'] === 'components') {
            $jsonContent["menu"] = $data["menu"];
        }
        $jsonContent["settings"] = $data["settings"];

        $jsonContent = Json::encode($jsonContent, JSON_UNESCAPED_SLASHES);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);
        $jsonContent = $this->basepackages->utils->formatJson(['json' => $jsonContent]);

        try {
            $this->localContent->write($jsonFile, $jsonContent);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write json content to file: ' . $jsonFile);

            return false;
        }

        return true;
    }

    protected function getModuleJsonFileLocation($data)
    {
        if ($data['module_type'] === 'components') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Components/';
        } else if ($data['module_type'] === 'packages') {
            if ($data['app_type'] === 'core' &&
                ($data['category'] === 'basepackages' ||
                 $data['category'] === 'providers')
            ) {
                if ($data['category'] === 'basepackages') {
                    $moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Basepackages/';
                } else if ($data['category'] === 'providers') {
                    $moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/';
                }
            } else {
                $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Packages/';
            }
        } else if ($data['module_type'] === 'middlewares') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Middlewares/';
        } else if ($data['module_type'] === 'views') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Views/';
        }

        if ($data['module_type'] === 'packages' &&
            ($data['category'] === 'basepackages' ||
             $data['category'] === 'providers')
        ) {
            return
                $moduleLocation .
                ucfirst($data['name']) . '/' .
                substr($data['module_type'], 0, -1) . '.json';
        } else {
            if ($data['module_type'] === 'components') {
                $routeArr = explode('/', $data['route']);

                foreach ($routeArr as &$path) {
                    $path = ucfirst($path);
                }

                $routePath = implode('/', $routeArr) . '/Install/';
            } else if ($data['module_type'] === 'middlewares') {
                $routePath = $data['name'] . '/Install/';
            } else if ($data['module_type'] === 'packages') {
                $pathArr = preg_split('/(?=[A-Z])/', $data['name'], -1, PREG_SPLIT_NO_EMPTY);

                $routePath = implode('/', $pathArr) . '/Install/';
            } else if ($data['module_type'] === 'views') {
                $routePath = $data['name'] . '/';
            }

            return
                $moduleLocation .
                $routePath .
                substr($data['module_type'], 0, -1) . '.json';
        }
    }

    protected function generateNewFiles($data)
    {
        //
    }

    protected function getNewFilesLocation($data)
    {
        if ($data['module_type'] === 'components') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Components/';
        } else if ($data['module_type'] === 'packages') {
            if ($data['app_type'] === 'core' &&
                ($data['category'] === 'basepackages' ||
                 $data['category'] === 'providers')
            ) {
                if ($data['category'] === 'basepackages') {
                    $moduleLocation = 'system/Base/Providers/BasepackagesServiceProvider/Packages/';
                } else if ($data['category'] === 'providers') {
                    $moduleLocation = 'system/Base/Providers/';
                }
            } else {
                $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Packages/';
            }
        } else if ($data['module_type'] === 'middlewares') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Middlewares/';
        } else if ($data['module_type'] === 'views') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Views/';
        }

        if ($data['module_type'] === 'packages' &&
            ($data['category'] === 'basepackages' ||
             $data['category'] === 'providers')
        ) {
            return
                $moduleLocation .
                ucfirst($data['name']) . '.php';
        } else {
            if ($data['module_type'] === 'components') {
                $routeArr = explode('/', $data['route']);

                foreach ($routeArr as &$path) {
                    $path = ucfirst($path);
                }

                $routePath = implode('/', $routeArr) . '/';
            } else if ($data['module_type'] === 'middlewares') {
                $routePath = $data['name'] . '/';
            } else if ($data['module_type'] === 'packages') {
                $pathArr = preg_split('/(?=[A-Z])/', $data['name'], -1, PREG_SPLIT_NO_EMPTY);

                $routePath = implode('/', $pathArr) . '/';
            } else if ($data['module_type'] === 'views') {
                $routePath = $data['name'] . '/';
            }

            return
                $moduleLocation .
                $routePath .
                substr($data['module_type'], 0, -1) . '.json';
        }
    }
}