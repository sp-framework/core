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

class Modules extends BasePackage
{
    public function updateModules(array $data)
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
            $jsonContent['sub_category'] = $component['sub_category'];
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
        $jsonContent['sub_category'] = $package['sub_category'];
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
        $jsonContent['sub_category'] = $middleware['sub_category'];
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

        $this->modules->views->update($view);

        $pathArr[0] = 'apps';
        $pathArr[1] = ucfirst($view['app_type']);
        $pathArr[2] = ucfirst('Views');
        $pathArr[3] = ucfirst($view['name']);

        $path = implode('/', $pathArr) . '/view.json';

        try {
            $jsonFile = $this->localContent->fileExists($path);
        } catch (FilesystemException | UnableToRetrieveMetadata $exception) {
            throw $exception;
        }

        $jsonContent = [];
        $jsonContent['name'] = $view['name'];
        $jsonContent['display_name'] = $view['display_name'];
        $jsonContent['description'] = $view['description'];
        $jsonContent['app_type'] = $view['app_type'];
        $jsonContent['category'] = $view['category'];
        $jsonContent['sub_category'] = $view['sub_category'];
        $jsonContent['version'] = $view['version'];
        $jsonContent['repo'] = $view['repo'];
        $jsonContent['dependencies'] = $view['dependencies'];
        $jsonContent['settings'] = $view['settings'];

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
            $data['json'] = $this->formatJson($data);
        }

        $this->addResponse('Success', 0, ['json' => $data['json']]);

        return $data['json'];
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