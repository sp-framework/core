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
            $this->updateCore($data);
        } else if ($data['type'] === 'components') {
            $this->updateComponent($data);
        } else if ($data['type'] === 'packages') {
            $this->updatePackage($data);
        } else if ($data['type'] === 'middlewares') {
            $this->updateMiddleware($data);
        } else if ($data['type'] === 'views') {
            $this->updateView($data);
        }
    }

    public function updateModule($data)
    {
        if ($data['type'] === 'core') {
            $this->updateCore($data);
        } else if ($data['type'] === 'components') {
            $this->updateComponent($data);
        } else if ($data['type'] === 'packages') {
            $this->updatePackage($data);
        } else if ($data['type'] === 'middlewares') {
            $this->updateMiddleware($data);
        } else if ($data['type'] === 'views') {
            $this->updateView($data);
        }
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

    protected function updateCore(array $data)
    {
        if ($this->core->update($data)) {
            $this->addResponse('Updated');

            return;
        }

        $this->addResponse('Error Updating', 1);
    }

    protected function updateComponent(array $data)
    {
        $component = $this->modules->components->getById($data['id']);

        $component = array_merge($component, $data);

        $this->modules->components->update($component);

        $pathArr = explode('/', str_replace('\\', '/', $data['class']));

        if (Str::endsWith(Arr::last($pathArr), 'Component')) {
            $pathArr[0] = strtolower($pathArr[0]);

            unset($pathArr[Arr::lastKey($pathArr)]);

            $path = implode('/', $pathArr) . '/Install/component.json';

            try {
                $jsonFile = $this->localContent->fileExists($path);
            } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
                throw $exception;
            }

            $jsonContent = [];
            $jsonContent['route'] = $component['route'];
            $jsonContent['name'] = $component['name'];
            $jsonContent['description'] = $component['description'];
            $jsonContent['app_type'] = $component['app_type'];
            $jsonContent['category'] = $component['category'];
            $jsonContent['version'] = $component['version'];
            $jsonContent['repo'] = $component['repo'];
            $jsonContent['class'] = $component['class'];
            $jsonContent['dependencies'] = $component['dependencies'];
            $jsonContent['menu'] = $component['menu'];
            $jsonContent['settings'] = $component['settings'];
            $jsonContent = Json::encode($jsonContent, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

            $jsonContent = str_replace('\\"', '"', $jsonContent);
            $jsonContent = str_replace('"{', '{', $jsonContent);
            $jsonContent = str_replace('}"', '}', $jsonContent);

            try {
                $this->localContent->write($path, $jsonContent);
            } catch (FilesystemException | UnableToWriteFile $exception) {
                throw $exception;
            }
        }
    }

    protected function updatePackage(array $data)
    {
        $package = $this->modules->packages->getById($data['id']);

        $package = array_merge($package, $data);

        $this->modules->packages->update($package);

        $pathArr = explode('/', str_replace('\\', '/', $data['class']));

        $pathArr[0] = strtolower($pathArr[0]);

        unset($pathArr[Arr::lastKey($pathArr)]);

        $path = implode('/', $pathArr) . '/Install/package.json';

        try {
            $jsonFile = $this->localContent->fileExists($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            throw $exception;
        }

        $jsonContent = [];
        $jsonContent['name'] = $package['name'];
        $jsonContent['display_name'] = $package['display_name'];
        $jsonContent['description'] = $package['description'];
        $jsonContent['app_type'] = $package['app_type'];
        $jsonContent['category'] = $package['category'];
        $jsonContent['version'] = $package['version'];
        $jsonContent['repo'] = $package['repo'];
        $jsonContent['class'] = $package['class'];
        $jsonContent['settings'] = $package['settings'];

        $jsonContent = Json::encode($jsonContent, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);

        try {
            $this->localContent->write($path, $jsonContent);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    protected function updateMiddleware(array $data)
    {
        $middleware = $this->modules->middlewares->getById($data['id']);

        $middleware = array_merge($middleware, $data);

        $this->modules->middlewares->update($middleware);

        $pathArr = explode('/', str_replace('\\', '/', $data['class']));

        $pathArr[0] = strtolower($pathArr[0]);

        unset($pathArr[Arr::lastKey($pathArr)]);

        $path = implode('/', $pathArr) . '/Install/middleware.json';

        try {
            $jsonFile = $this->localContent->fileExists($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            throw $exception;
        }

        $jsonContent = [];
        $jsonContent['name'] = $middleware['name'];
        $jsonContent['display_name'] = $middleware['display_name'];
        $jsonContent['description'] = $middleware['description'];
        $jsonContent['app_type'] = $middleware['app_type'];
        $jsonContent['category'] = $middleware['category'];
        $jsonContent['version'] = $middleware['version'];
        $jsonContent['repo'] = $middleware['repo'];
        $jsonContent['class'] = $middleware['class'];
        $jsonContent['settings'] = $middleware['settings'];

        $jsonContent = Json::encode($jsonContent, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);

        try {
            $this->localContent->write($path, $jsonContent);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    protected function updateView(array $data)
    {
        $view = $this->modules->views->getById($data['id']);

        $view = array_merge($view, $data);

        if ($this->modules->views->update($view)) {
            if ($this->updateModuleJson($data)) {
                $this->addResponse('Module updated');

                return;
            }
        }

        $this->addResponse('Error updating Module', 1);
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

    public function getDefaultSettings($type)
    {
        $defaultSettings = [];

        return Json::encode($defaultSettings);
    }

    public function getDefaultDependencies()
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

        return Json::encode($defaultDependencies);
    }

    protected function updateModuleJson($data)
    {
        $jsonFile = $this->getModuleJsonFileLocation($data);

        if ($data['module_type'] === 'components') {
        } else if ($data['module_type'] === 'packages') {
        } else if ($data['module_type'] === 'middlewares') {
        } else if ($data['module_type'] === 'views') {

            $data = $this->jsonDecodeData($data);

            $jsonContent = [];
            $jsonContent["name"] = $data["name"];
            $jsonContent["display_name"] = $data["display_name"];
            $jsonContent["description"] = $data["description"];
            $jsonContent["module_type"] = $data["module_type"];
            $jsonContent["app_type"] = $data["app_type"];
            $jsonContent["category"] = $data["category"];
            $jsonContent["version"] = $data["version"];
            $jsonContent["repo"] = $data["repo"];
            $jsonContent["dependencies"] = $data["dependencies"];
            $jsonContent["settings"] = $data["settings"];

        }

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
            $this->view->moduleMenu = $data['menu'];
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
}