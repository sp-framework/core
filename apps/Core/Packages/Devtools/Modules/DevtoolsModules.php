<?php

namespace Apps\Core\Packages\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\Model\AppsCoreDevtoolsModulesBundles;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use System\Base\BasePackage;
use z4kn4fein\SemVer\Version;

class DevtoolsModules extends BasePackage
{
    protected $modelToUse = AppsCoreDevtoolsModulesBundles::class;

    protected $packageName = 'bundles';

    public $bundles;

    protected $api;

    protected $apiConfig;

    public function addModule($data)
    {
        if ($data['type'] === 'bundles') {
            $data = $this->checkTypeAndCategory($data);

            if ($this->add($data)) {
                $this->addResponse('Bundle added');

                return;
            }

            $this->addResponse('Error adding bundle', 1);

            return;
        }

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

        if ($module) {
            $this->addResponse('Module already exists!', 1);

            return false;
        }

        $data['name'] = ucfirst(trim(str_replace('(Clone)', '', $data['name'])));
        $data['repo'] = trim(str_replace('(clone)', '', $data['repo']));
        $data['installed'] = '1';
        $data['updated_by'] = '0';

        if ($data['type'] === 'views' && $data['base_view_module_id'] == 0) {
            $data['view_modules_version'] = '0.0.0.0';
        }

        if ($data['apps'] === '') {
            $data['apps'] = Json::encode([]);
        }

        try {
            if ($this->modules->{$data['type']}->add($data) &&
                $this->updateModuleJson($data) &&
                $this->generateNewFiles($data)
            ) {
                if ($data['createrepo'] == true) {
                    if (!$this->checkRepo($data)) {
                        $newRepo = $this->createRepo($data);

                        $this->addResponse('Module added & created new repo.', 0, ['newRepo' => $newRepo]);

                        return;
                    }
                }

                $this->addResponse('Module added');

                return;
            }
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);
        }
    }

    public function updateModule($data)
    {
        if ($data['type'] === 'bundles') {
            $data = $this->checkTypeAndCategory($data);

            if ($this->update($data)) {
                $this->addResponse('Bundle updated');

                return;
            }

            $this->addResponse('Error updating bundle', 1);

            return;
        }

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
                if ($data['type'] === 'components') {
                    $this->addUpdateComponentMenu($data);
                }

                if ($data['createrepo'] == true) {
                    if (!$this->checkRepo($data)) {
                        $newRepo = $this->createRepo($data);

                        $this->addResponse('Module added & created new repo.', 0, ['newRepo' => $newRepo]);

                        return;
                    }
                }

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
        if (isset($data['app_type']) && str_contains($data['app_type'], '"data"')) {
            $data['app_type'] = Json::decode($data['app_type'], true);
            if (isset($data['app_type']['data'][0])) {
                $data['app_type'] = $data['app_type']['data'][0];
            } else if (isset($data['app_type']['newTags'][0])) {
                $appType = $this->apps->types->getFirst('app_type', strtolower($data['app_type']['newTags'][0]));

                if (!$appType) {
                    $this->apps->types->add(
                        [
                            'app_type'  => strtolower($data['app_type']['newTags'][0]),
                            'name'      => $data['app_type']['newTags'][0]
                        ]
                    );
                }

                $data['app_type'] = strtolower($data['app_type']['newTags'][0]);
            }
        }

        if (isset($data['category']) && str_contains($data['category'], '"data"')) {
            $data['category'] = Json::decode($data['category'], true);
            if (isset($data['category']['data'][0])) {
                $data['category'] = $data['category']['data'][0];
            } else if (isset($data['category']['newTags'][0])) {
                $data['category'] = strtolower($data['category']['newTags'][0]);
            }
        }

        return $data;
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

    public function getDefaultDependencies($type = null)
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

        if ($type && $type === 'views') {
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

        if ($data['module_type'] === 'views') {
            if (isset($data['base_view_module_id']) &&
                $data['base_view_module_id'] != '0'
            ) {
                $data = $this->mergeViewSettings($data);
            }
        }

        $jsonContent["settings"] = $data["settings"];
        if ($data['module_type'] === 'components') {
            $jsonContent["widgets"] = $data["widgets"];
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

    protected function mergeViewSettings($data)
    {
        $baseView = $this->modules->views->getViewById($data['base_view_module_id']);

        if ($baseView) {
            $baseViewJsonLocation = $this->getModuleJsonFileLocation($baseView);

            try {
                $baseViewJson = Json::decode($this->localContent->read($baseViewJsonLocation), true);
            } catch (FilesystemException | UnableToReadFile $exception) {
                $this->addResponse('Unable to read base view json content to file: ' . $baseViewJsonLocation);

                return false;
            }

            $baseView = $this->basepackages->utils->jsonDecodeData($baseView);
            $baseViewJson = $this->basepackages->utils->jsonDecodeData($baseViewJson);

            //Overwrite baseview with original json file because only developer changes the json file that is imported into the db via module installer on install or update.
            $baseView['settings']['head']['link'] = $baseViewJson['settings']['head']['link'];
            $baseView['settings']['head']['script'] = $baseViewJson['settings']['head']['script'];
            $baseView['settings']['footer']['script'] = $baseViewJson['settings']['footer']['script'];

            $subViews = $this->modules->views->getViewsByBaseViewModuleId($baseView['id']);

            $assets = ['link', 'head', 'footer'];
            $envs = ['dev', 'prod'];

            foreach ($subViews as $subView) {
                $subView = $this->basepackages->utils->jsonDecodeData($subView);

                foreach ($assets as $asset) {
                    foreach ($envs as $env) {
                        if ($asset === 'link') {
                            if (isset($subView['settings']['head']['link']['href']['assets'][$env])) {
                                $dataAssets = $subView['settings']['head']['link']['href']['assets'][$env];
                            }
                            if (isset($baseView['settings']['head']['link']['href']['assets'][$env])) {
                                $baseViewAssets = &$baseView['settings']['head']['link']['href']['assets'][$env];
                            }
                        } else if ($asset === 'head') {
                            if (isset($subView['settings']['head']['script']['src']['assets'][$env])) {
                                $dataAssets = $subView['settings']['head']['script']['src']['assets'][$env];
                            }
                            if (isset($baseView['settings']['head']['script']['src']['assets'][$env])) {
                                $baseViewAssets = &$baseView['settings']['head']['script']['src']['assets'][$env];
                            }
                        } else if ($asset === 'footer') {
                            if (isset($subView['settings']['footer']['script']['src']['assets'][$env])) {
                                $dataAssets = $subView['settings']['footer']['script']['src']['assets'][$env];
                            }
                            if (isset($baseView['settings']['footer']['script']['src']['assets'][$env])) {
                                $baseViewAssets = &$baseView['settings']['footer']['script']['src']['assets'][$env];
                            }
                        }

                        $this->mergeViewAssets($data, $dataAssets, $baseViewAssets);
                    }
                }
            }

            $this->modules->views->update($baseView);
        }

        return $data;
    }

    protected function mergeViewAssets(&$data, &$dataAssets, &$baseViewAssets)
    {
        if (isset($dataAssets) && is_array($dataAssets) && count($dataAssets) > 0) {
            foreach ($dataAssets as $dataAsset) {
                $found = false;

                if (isset($dataAsset['asset'])) {
                    foreach ($baseViewAssets as $baseViewAsset) {
                        if (isset($baseViewAsset['asset'])) {
                            if ($dataAsset['asset'] === $baseViewAsset['asset']) {
                                $found = true;
                            }
                        }
                    }
                }

                if (!$found) {
                    $dataAsset['route'] = '/' . $data['route'];
                    array_push($baseViewAssets, $dataAsset);
                }
            }
        }
    }

    protected function getModuleJsonFileLocation(&$data)
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
                $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

                $routePath = implode('/', $pathArr) . '/Install/';
            } else if ($data['module_type'] === 'views') {
                if ($data['base_view_module_id'] == 0) {
                    $routePath = $data['name'] . '/';
                } else {
                    $baseView = $this->modules->views->getViewById($data['base_view_module_id']);

                    $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

                    if (count($pathArr) > 1) {
                        foreach ($pathArr as &$path) {
                            $path = strtolower($path);
                        }
                    } else {
                        $pathArr[0] = strtolower($pathArr[0]);
                    }

                    $data['route'] = implode('/', $pathArr);

                    return $moduleLocation . $baseView['name'] . '/html/' . $data['route'] . '/' . substr($data['module_type'], 0, -1) . '.json';
                }
            }

            return
                $moduleLocation .
                $routePath .
                substr($data['module_type'], 0, -1) . '.json';
        }
    }

    protected function generateNewFiles($data)
    {
        $moduleFilesLocation = $this->getNewFilesLocation($data);

        $method = 'generateNew' . ucfirst($data['type']) . 'Files';

        $this->{$method}($moduleFilesLocation, $data);

        return true;
    }

    protected function getNewFilesLocation($data, $public = false)
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

            if ($public) {
                $moduleLocation = 'public/' . $data['app_type'] . '/' . strtolower($data['name']) . '/';

                return $moduleLocation;
            }
        }

        if ($data['module_type'] === 'packages' &&
            ($data['category'] === 'basepackages' ||
             $data['category'] === 'providers')
        ) {
            return
                $moduleLocation .
                ucfirst($data['name']);
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
                if ($data['base_view_module_id'] == 0) {
                    $routePath = $data['name'] . '/';
                } else {
                    $baseView = $this->modules->views->getViewById($data['base_view_module_id']);

                    $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

                    if (count($pathArr) > 1) {
                        foreach ($pathArr as &$path) {
                            $path = strtolower($path);
                        }
                    } else {
                        $pathArr[0] = strtolower($pathArr[0]);
                    }

                    $data['route'] = implode('/', $pathArr);

                    $routePath = $baseView['name'] . '/html/' . $data['route'] . '/';
                }
            }

            return $moduleLocation . $routePath;
        }
    }

    protected function generateNewComponentsFiles($moduleFilesLocation, $data)
    {
        $this->addUpdateComponentMenu($data);

        $componentName = ucfirst(Arr::last(explode('/', $data['route']))) . 'Component';

        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/Component.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base component file.');

            return false;
        }

        $data['class'] = explode('\\', $data['class']);
        unset($data['class'][Arr::lastKey($data['class'])]);
        $namespaceClass = implode('\\', $data['class']);

        $file = str_replace('"NAMESPACE"', 'namespace ' . $namespaceClass, $file);
        $file = str_replace('"COMPONENTNAME"', $componentName, $file);

        try {
            $this->localContent->write($moduleFilesLocation . $componentName . '.php', $file);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module component file');

            return false;
        }

        return true;
    }

    protected function generateNewViewsFiles($moduleFilesLocation, $data)
    {
        if ($data['base_view_module_id'] == 0) {
            $modulePublicFilesLocation = $this->getNewFilesLocation($data, true);
            try {
                if (is_string($data['settings'])) {
                    $data['settings'] = Json::decode($data['settings'], true);
                }

                $this->localContent->createDirectory($moduleFilesLocation . 'html');
                $this->localContent->createDirectory($moduleFilesLocation . 'html/layouts');

                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/layout.txt');
                foreach ($data['settings']['layouts'] as $layout) {
                    $this->localContent->write($moduleFilesLocation . 'html/layouts/' . $layout['view'] . '.html', $file);
                }

                $this->localContent->createDirectory($modulePublicFilesLocation . 'css');
                $this->localContent->createDirectory($modulePublicFilesLocation . 'fonts');
                $this->localContent->createDirectory($modulePublicFilesLocation . 'images');
                $this->localContent->createDirectory($modulePublicFilesLocation . 'js');
                $this->localContent->createDirectory($modulePublicFilesLocation . 'sounds');
            } catch (FilesystemException | UnableToCreateDirectory $exception) {
                $this->addResponse('Unable to create view directories in public folder.');

                return false;
            }
        } else {
            try {
                $this->localContent->write($moduleFilesLocation . 'view.html', $data['name'] . ' Main View');
                $this->localContent->write($moduleFilesLocation . 'list.html', $data['name'] . ' List View');
            } catch (FilesystemException | UnableToReadFile | UnableToWriteFile $exception) {
                $this->addResponse('Unable to read/write module base html files.');

                return false;
            }
        }

        return true;
    }

    protected function addUpdateComponentMenu($data)
    {
        if ($data['menu_id'] != '' && $data['menu_id'] != '0') {
            if (!isset($data['is_clone']) ||
                (isset($data['is_clone']) && $data['is_clone'] == false)
            ) {
                $menu = $this->basepackages->menus->getById($data['menu_id']);

                if ($menu) {
                    if ($data['menu'] == 'false') {
                        $this->basepackages->menus->remove($data['menu_id']);

                        $module = $this->modules->{$data['type']}->getById($data['id']);

                        $module['menu_id'] = null;
                        $module['menu'] = null;

                        $this->modules->{$data['type']}->update($module);

                        return;
                    }
                }
            }
        }

        if ($data['menu'] != 'false' && $data['menu'] != '') {
            $data['menu'] = Json::decode($data['menu'], true);

            if (isset($menu)) {
                $this->basepackages->menus->updateMenu($data['menu_id'], $data['app_type'], $data['menu']);

                return;
            } else {
                $menu = $this->basepackages->menus->addMenu($data['app_type'], $data['menu']);

                if ($menu) {
                    $module = $this->modules->{$data['type']}->packagesData->last;

                    $module['menu_id'] = $menu['id'];
                }

                $this->modules->{$data['type']}->update($module);
            }
        }
    }

    public function syncLabels($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListLabels';
            $args = [$this->apiConfig['org_user'], strtolower($data['name'])];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $labels = $this->api->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getmessage(), 1);

            return false;
        }

        if ($labels) {
            $this->addResponse('Labels Synced', 0, ['labels' => $labels]);

            return true;
        }

        $this->addResponse('Error syncing labels', 1);
    }

    public function getLabelIssues($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        //Get Latest Release (Last One, we need to sync issues since that release)
        $latestRelease = $this->getLatestRelease($data);

        if (!$latestRelease) {
            $since = null;
        }

        $since = $latestRelease['created_at'];

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListIssues';
            $args = [$this->apiConfig['org_user'], strtolower($data['name']), 'closed', implode(',', $data['labels']), null, null, null, $since];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        $issues = $this->api->useMethod($collection, $method, $args)->getResponse(true);

        if ($issues) {
            $this->addResponse('Issues Synced', 0, ['issues' => $issues]);

            return true;
        }

        $this->addResponse('Error syncing issues', 1);
    }

    protected function getLatestRelease($data)
    {
        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGetLatestRelease';
            $args = [$this->apiConfig['org_user'], strtolower($data['name'])];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $latestRelease = $this->api->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);
        }

        if (isset($latestRelease)) {
            return $latestRelease;
        }

        return false;
    }

    protected function initApi($data)
    {
        if (!isset($data['api_id']) || !isset($data['name'])) {
            $this->addResponse('API information not provided', 1, []);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] == '0') {
            $this->addResponse('This is local module and not remote module, cannot sync labels.', 1, []);

            return false;
        }

        $this->api = $this->basepackages->api->useApi($data['api_id'], true);

        $this->apiConfig = $this->api->getApiConfig();

        if ($this->apiConfig['auth_type'] === 'auth' &&
            ((!$this->apiConfig['username'] || $this->apiConfig['username'] === '') &&
             (!$this->apiConfig['password'] || $this->apiConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return false;
        } else if ($this->apiConfig['auth_type'] === 'access_token' &&
                   (!$this->apiConfig['access_token'] || $this->apiConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return false;
        } else if ($this->apiConfig['auth_type'] === 'autho' &&
                   (!$this->apiConfig['authorization'] || $this->apiConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return false;
        }

        return true;
    }

    public function bumpVersion($data)
    {
        if (!$this->initApi($data['api_id'])) {
            return false;
        }

        $latestRelease = $this->getLatestRelease($data);

        if ($latestRelease) {
            $latestReleaseVersion = $latestRelease['name'];
        } else {
            return false;
        }

        if ($data['type'] === 'core') {
            $currentVersion = $this->core->getVersion();
        } else {
            $module = $this->modules->{$data['type']}->getById($data['id']);

            $currentVersion = $module['version'];
        }

        $compareVersion = Version::compare(Version::parse($currentVersion), Version::parse($latestReleaseVersion));

        if ($compareVersion !== 0) {
            $currentVersion = $latestReleaseVersion;
        }

        if ($compareVersion === 1) {
            $compareVersion = 2;
        }

        if (!isset($currentVersion)) {
            $this->addResponse('Could not retrieve current version', 2);

            return false;
        }

        $version = Version::parse($currentVersion);
        $bump = 'getNext' . ucfirst($data['bump']) . 'Version';
        $newVersion = $version->$bump();

        if ($newVersion) {
            $this->addResponse('New Version', $compareVersion, ['currentVersion' => $currentVersion, 'newVersion' => $newVersion->__toString()]);

            return $newVersion->__toString();
        }

        $this->addResponse('Could not retrieve current/next version', 2);
    }

    public function syncBranches($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoListBranches';
            $args = [$this->apiConfig['org_user'], strtolower($data['name'])];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $branches = $this->api->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($branches) {
            $this->addResponse('Labels Synced', 0, ['branches' => $branches]);

            return true;
        }

        $this->addResponse('Error syncing branches', 1);
    }

    public function generateRelease($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoCreateRelease';

            // $draft = false;
            // if (isset($data['draft'])) {
            //     if ($data['draft'] == '1') {
            //         $draft = true;
            //     }
            // }

            $prerelease = false;
            if (isset($data['prerelease']) && $data['prerelease'] == 'true') {
                $prerelease = true;
            }

            $name = $this->bumpVersion($data);

            $args =
                [
                    $this->apiConfig['org_user'],
                    strtolower($data['name']),
                    [
                        'body'              => $data['release_notes'],
                        'draft'             => false,
                        'name'              => $name,
                        'prerelease'        => $prerelease,
                        'tag_name'          => $name,
                        'target_commitish'  => $data['branch']
                    ]
                ];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $newRelease = $this->api->useMethod($collection, $method, $args)->getResponse(true);

            if ($newRelease) {
                $this->addResponse('Generated New Release!', 0, ['newRelease' => $newRelease]);

                return true;
            }
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return;
        }

        $this->addResponse('Error generating new release', 1);
    }

    public function publishBundleJson($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        $repo = $this->checkRepo($data);

        //Create new repo if doesnt exist.
        if (!$repo) {
            $newRepo = $this->createRepo($data);
        }

        $data['repo'] = Arr::last(explode('/', $data['repo']));

        //Check for bundle.json file
        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGetContents';
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $file = $this->api->useMethod($collection, $method, [$this->apiConfig['org_user'], strtolower($data['repo']), 'bundle.json'])->getResponse(true);
        } catch (\throwable $e) {
            if ($e->getCode() !== 404) {
                $this->addResponse($e->getMessage(), 1);

                return;
            }
        }

        $jsonContent = [];
        $jsonContent["name"] = $data["name"];
        $jsonContent["description"] = $data["description"];
        $jsonContent["module_type"] = $data["module_type"];
        $jsonContent["app_type"] = $data["app_type"];
        $jsonContent["repo"] = $data["repo"];
        $jsonContent["bundle_modules"] = Json::decode($data["bundle_modules"], true);

        $jsonContent = Json::encode($jsonContent, JSON_UNESCAPED_SLASHES);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);
        $jsonContent = $this->basepackages->utils->formatJson(['json' => $jsonContent]);

        //Create File if not found
        if (!isset($file)) {
            $method = 'repoCreateFile';
            $args =
                [
                    $this->apiConfig['org_user'],
                    strtolower($data['repo']),
                    'bundle.json',
                    [
                        'message' => $data['comment'],
                        'content' => base64_encode($jsonContent)
                    ]
                ];
        } else {
            $method = 'repoUpdateFile';
            $args =
                [
                    $this->apiConfig['org_user'],
                    strtolower($data['repo']),
                    'bundle.json',
                    [
                        'message' => $data['comment'],
                        'content' => base64_encode($jsonContent),
                        'sha' => $file['sha']
                    ]
                ];
        }

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $file = $this->api->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return;
        }

        $this->addResponse('Added/Updated bundle.json to repository', 0, []);
    }

    protected function checkRepo($data)
    {
        if (!$this->apiConfig && !$this->initApi($data)) {
            return false;
        }

        $data['repo'] = Arr::last(explode('/', $data['repo']));

        //Check Repo if exists
        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGet';
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $repo = $this->api->useMethod($collection, $method, [$this->apiConfig['org_user'], strtolower($data['repo'])])->getResponse(true);
        } catch (\throwable $e) {
            if ($e->getCode() === 404 && $data['createrepo'] == false) {
                $this->addResponse('Repository does not exist. Please check create repo and publish again.' . $e->getMessage(), 1);

                return false;
            }
        }

        if (isset($repo)) {
            return $repo;
        }

        return false;
    }

    protected function createRepo($data)
    {
        if (!$this->apiConfig && !$this->initApi($data)) {
            return false;
        }

        $data['repo'] = Arr::last(explode('/', $data['repo']));

        if (strtolower($this->apiConfig['provider']) === 'gitea') {
            $collection = 'OrganizationApi';
            $method = 'createOrgRepo';

            $args =
                [
                    $this->apiConfig['org_user'],
                    [
                        "auto_init"         => true,
                        "default_branch"    => "main",
                        "description"       => $data['repo'],
                        "name"              => $data['repo'],
                        "private"           => false,
                    ]
                ];
        } else if (strtolower($this->apiConfig['provider']) === 'github') {
            //For github
        }

        try {
            $newRepo = $this->api->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if (isset($newRepo)) {
            return $newRepo;
        }

        return false;
    }
}