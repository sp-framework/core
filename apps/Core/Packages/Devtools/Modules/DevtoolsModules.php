<?php

namespace Apps\Core\Packages\Devtools\Modules;

use Apps\Core\Packages\Devtools\Modules\Settings;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Phalcon\Filter\Validation\Validator\PresenceOf;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use System\Base\BasePackage;
use z4kn4fein\SemVer\Version;

class DevtoolsModules extends BasePackage
{
    protected $apiClient;

    protected $apiClientConfig;

    protected $newFiles = [];

    protected $newDirs = [];

    protected $settings = Settings::class;

    protected $releases = null;

    protected $latestRelease = null;

    public function addModule($data)
    {
        if ($data['api_id'] != '0' &&
            ($data['repo'] === 'https://.../' || $data['repo'] === '')
        ) {
            $this->addResponse('Repository is not local, please provide correct module repo url.', 1);

            return false;
        }

        if (!isset($data['module_type']) ||
            (isset($data['module_type']) &&
             ($data['module_type'] === 'components' || $data['module_type'] === 'apps_types')
            )
        ) {
            $ignoreChars = [' '];
        } else {
            $ignoreChars = [''];
        }

        if (!checkCtype($data['name'], 'alpha', $ignoreChars)) {
            $this->addResponse('Name cannot have special chars or numbers.', 1);

            return false;
        }

        $data = $this->checkAppType($data);

        if (!$data) {
            return true;
        }

        $data = $this->checkModuleTypeAndCategory($data);

        $data['name'] = ucfirst(trim(str_replace('(Clone)', '', $data['name'])));
        $data['repo'] = trim(str_replace('(clone)', '', $data['repo']));
        $data['installed'] = '1';
        $data['updated_by'] = '0';

        if ($data['module_type'] === 'bundles') {
            if ($this->modules->{$data['module_type']}->add($data)) {
                if ($data['createrepo'] == true) {
                    if (!$this->checkRepo($data)) {
                        $newRepo = $this->createRepo($data);

                        $this->addResponse('Bundle added & created new repo.',
                                           0,
                                           [
                                            'newRepo'   => $newRepo,
                                            'bundle'    => $this->modules->{$data['module_type']}->packagesData->last
                                           ]
                                        );

                        return;
                    }
                }

                $this->addResponse('Bundle added', 0, ['bundle'    => $this->modules->{$data['module_type']}->packagesData->last]);

                return;
            }

            $this->addResponse('Error adding bundle', 1);

            return;
        }

        if ($data['module_type'] === 'core') {
            $this->addResponse('Core already exists!', 1);

            return false;
        }

        if ($data['module_type'] === 'components') {
            $moduleMethod = 'get' . ucfirst(substr($data['module_type'], 0, -1)) . 'ByAppTypeAndRepoAndRoute';
            $module = $this->modules->{$data['module_type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['route']);
        } else if ($data['module_type'] === 'packages' || $data['module_type'] === 'middlewares') {
            $moduleMethod = 'get' . ucfirst(substr($data['module_type'], 0, -1)) . 'ByAppTypeAndRepoAndClass';
            $module = $this->modules->{$data['module_type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['class']);
        } else if ($data['module_type'] === 'views') {
            $moduleMethod = 'get' . ucfirst(substr($data['module_type'], 0, -1)) . 'ByAppTypeAndRepoAndName';
            $module = $this->modules->{$data['module_type']}->{$moduleMethod}($data['app_type'], $data['repo'], $data['name']);
        }

        if ($module) {
            $this->addResponse('Module already exists!', 1);

            return false;
        }

        if ($data['module_type'] !== 'views') {
            $data['class'] = $this->generateModuleClass($data);
        }

        if ($data['module_type'] === 'views' && $data['base_view_module_id'] == 0) {
            $data['view_modules_version'] = '0.0.0';
        }

        if ($data['apps'] === '') {
            $data['apps'] = $this->helper->encode([]);
        }

        try {
            if ($this->modules->{$data['module_type']}->add($data) &&
                $this->updateModuleJson($data, false, true) &&
                $this->generateNewFiles($data)
            ) {
                if ($data['createrepo'] == true) {
                    if ($data['module_type'] === 'views' && $data['base_view_module_id'] == 0) {//Create public repository as well
                        if (!$this->checkRepo($data)) {
                            $newRepo['base'] = $this->createRepo($data);
                        }

                        $data['repo'] = $data['repo'] . '-public';
                        if (!$this->checkRepo($data)) {
                            $newRepo['public'] = $this->createRepo($data);
                        }

                        $this->addResponse('Module added & created new repo.',
                                           0,
                                           [
                                            'newFiles'  => $this->newFiles,
                                            'newDirs'   => $this->newDirs,
                                            'newRepo'   => $newRepo
                                           ]
                                        );

                        return;
                    } else {
                        if (!$this->checkRepo($data)) {
                            $newRepo = $this->createRepo($data);

                            $this->addResponse('Module added & created new repo.',
                                               0,
                                               [
                                                'newFiles'  => $this->newFiles,
                                                'newDirs'   => $this->newDirs,
                                                'newRepo'   => $newRepo
                                               ]
                                            );

                            return;
                        }
                    }
                }

                $this->addResponse('Module added',
                                   0,
                                   [
                                    'newFiles'  => $this->newFiles,
                                    'newDirs'   => $this->newDirs
                                   ]
                                );
                return;
            }
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return;
        }

        $this->addResponse('Error adding Module', 1);
    }

    public function updateModule($data)
    {
        if ($data['api_id'] != '0' &&
            ($data['repo'] === 'https://.../' || $data['repo'] === '')
        ) {
            $this->addResponse('Repository is not local, please provide correct module repo url.', 1);

            return false;
        }

        if (!isset($data['module_type']) ||
            (isset($data['module_type']) &&
             ($data['module_type'] === 'components' || $data['module_type'] === 'apps_types')
            )
        ) {
            $ignoreChars = [' '];
        } else {
            $ignoreChars = [''];
        }

        if (!checkCtype($data['name'], 'alpha', $ignoreChars)) {
            $this->addResponse('Name cannot have special chars or numbers.', 1);

            return false;
        }

        $data = $this->checkAppType($data);

        if (!$data) {
            return true;
        }

        $data = $this->checkModuleTypeAndCategory($data);

        if ($data['module_type'] === 'bundles') {
            if ($this->modules->{$data['module_type']}->update($data)) {
                if ($data['createrepo'] == true) {
                    if (!$this->checkRepo($data)) {
                        $newRepo = $this->createRepo($data);

                        $this->addResponse('Bundle updated & created new repo.',
                                           0,
                                           [
                                            'newRepo'   => $newRepo,
                                            'bundle'    => $this->modules->{$data['module_type']}->packagesData->last
                                           ]
                                        );

                        return;
                    }
                }

                $this->addResponse('Bundle updated', 0, ['bundle'    => $this->modules->{$data['module_type']}->packagesData->last]);

                return;
            }

            $this->addResponse('Error updating bundle', 1);

            return;
        }

        try {
            if ($data['module_type'] === 'core') {
                if ($this->updateModuleJson($data)) {
                    $this->addResponse('Module updated');

                    return;
                }
            } else {
                $module = $this->modules->{$data['module_type']}->getById($data['id']);

                $module = array_merge($module, $data);

                if ($this->modules->{$data['module_type']}->update($module) &&
                    $this->updateModuleJson($data, false, true)
                ) {
                    if ($data['module_type'] === 'components') {
                        $this->addUpdateComponentMenu($data);
                    }
                    if ($data['module_type'] === 'views') {
                        $viewsSettings = $this->modules->viewsSettings->getViewsSettingsByViewId($data['id']);

                        if ($viewsSettings) {
                            foreach ($viewsSettings as $setting) {
                                if (is_string($data['settings'])) {
                                    $data['settings'] = $this->helper->decode($data['settings'], true);
                                }

                                $setting['settings'] = array_replace($setting['settings'], $data['settings']);

                                $this->modules->viewsSettings->updateViewsSettings($setting);
                            }
                        }
                    }

                    if ($data['createrepo'] == true && strtolower($data['name']) !== 'core') {
                        if ($data['module_type'] === 'views' && $data['base_view_module_id'] == 0) {//Create public repository as well
                            if (!$this->checkRepo($data)) {
                                $newRepo['base'] = $this->createRepo($data);
                            }

                            $data['repo'] = $data['repo'] . '-public';
                            if (!$this->checkRepo($data)) {
                                $newRepo['public'] = $this->createRepo($data);
                            }

                            $this->addResponse('Module updated & created new repo.',
                                               0,
                                               [
                                                'newFiles'  => $this->newFiles,
                                                'newDirs'   => $this->newDirs,
                                                'newRepo'   => $newRepo
                                               ]
                                            );

                            return;
                        } else {
                            if (!$this->checkRepo($data)) {
                                $newRepo = $this->createRepo($data);

                                $this->addResponse('Module updated & created new repo.',
                                                   0,
                                                   [
                                                    'newFiles'  => $this->newFiles,
                                                    'newDirs'   => $this->newDirs,
                                                    'newRepo'   => $newRepo
                                                   ]
                                                );

                                return;
                            }
                        }
                    }

                    if (strtolower($data['name']) === 'core' &&
                        $this->core->getVersion() !== $data['version']
                    ) {
                        $core = $this->core->core;

                        $core['version'] = $data['version'];

                        $this->core->update($core);
                    }

                    $this->addResponse('Module updated');

                    return;
                }
            }
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return;
        }

        $this->addResponse('Error updating Module', 1);
    }

    public function removeModule($data)
    {
        if ($data['module_type'] !== 'core') {
            if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
                $this->apps->types->removeAppType($data);
                $this->addResponse('Removed app type from DB. Remove files manually...');

                return true;
            }

            $module = $this->modules->{$data['module_type']}->getById($data['id']);
        } else {
            $this->addResponse('Cannot remove Core!.', 1);

            return false;
        }

        if ($module && $this->modules->{$data['module_type']}->remove($module['id'])) {
            $this->addResponse('Removed module from DB. Remove files manually...');
        } else {
            $this->addResponse('Error removing module.', 1);
        }
    }

    protected function checkAppType($data)
    {
        if (isset($data['app_type']) &&
            str_contains($data['app_type'], '"data"')
        ) {
            $data['app_type'] = $this->helper->decode($data['app_type'], true);

            if (isset($data['app_type']['data'][0])) {
                if (!checkCtype($data['app_type']['data'][0], 'alpha', [''])) {
                    $this->addResponse('AppType cannot have special chars or numbers.', 1);

                    return false;
                }

                $data['app_type'] = $data['app_type']['data'][0];
            } else if (isset($data['app_type']['newTags'][0])) {
                if (!checkCtype($data['app_type']['newTags'][0], 'alpha', [''])) {
                    $this->addResponse('AppType cannot have special chars or numbers.', 1);

                    return false;
                }

                $appType = $this->apps->types->getFirst('app_type', strtolower($data['app_type']['newTags'][0]));

                if (!$appType) {
                    $appType =
                        [
                            'name'          => $data['app_type']['newTags'][0],
                            'app_type'      => strtolower($data['app_type']['newTags'][0]),
                            'description'   => 'Added via devtools module add.',
                            'version'       => $data['version'],
                            'api_id'        => $data['api_id'],
                            'repo'          => $data['repo'],
                            'updated_by'    => '0',
                            'installed'     => '1'
                        ];

                    $this->apps->types->add($appType);

                    $this->addUpdateAppTypeFiles($appType);
                }

                $data['app_type'] = strtolower($data['app_type']['newTags'][0]);
            }
        } else if (isset($data['app_type']) &&
                   !isset($data['module_type'])
        ) {
            if (!checkCtype($data['app_type'], 'alpha', [''])) {
                $this->addResponse('AppType cannot have special chars or numbers.', 1);

                return false;
            }

            if (isset($data['id'])) {
                $appType = $this->apps->types->getAppTypeById($data['id']);
            }

            if (isset($appType) && strtolower($appType['app_type']) !== 'core') {
                $appType['name'] = $data['name'];
                $appType['app_type'] = strtolower($data['app_type']);
                $appType['description'] = $data['description'];
                $appType['version'] = $data['version'];
                $appType['api_id'] = $data['api_id'];
                $appType['repo'] = $data['repo'];

                $this->apps->types->update($appType);

                $this->addUpdateAppTypeFiles($appType);

                $this->addResponse('Updated new app type');
            } else {
                $data['app_type'] = strtolower($data['app_type']);
                $data['updated_by'] = '0';
                $data['installed'] = '1';

                $this->apps->types->add($data);

                $this->addUpdateAppTypeFiles($data);
            }

            if ($data['createrepo'] == true) {
                if (!$this->checkRepo($data)) {
                    $newRepo = $this->createRepo($data);

                    $this->addResponse('Added new app type', 0, ['newRepo' => $newRepo]);
                }
            }

            return false;
        }

        return $data;
    }

    protected function addUpdateAppTypeFiles($appType)
    {
        $directories = ['Components', 'Middlewares', 'Packages', 'Views', 'Install'];

        foreach ($directories as $directory) {
            try {
                $path = 'apps/' . ucfirst($appType['app_type']) . '/' . $directory;

                $dirExists = $this->localContent->directoryExists($path);

                if (!$dirExists) {//addGitkeep
                    $this->localContent->write($path . '/.gitkeep', '');
                }

                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/ApptypesGitignore.txt');
                $this->localContent->write('apps/' . ucfirst($appType['app_type']) . '/.gitignore', $file);
            } catch (FilesystemException | UnableToCheckExistence | UnableToWriteFile $exception) {
                $this->addResponse('Unable to write json content to file: .gitkeep for apptypes');

                return false;
            }
        }

        $jsonFile = 'apps/' . ucfirst($appType['app_type']) . '/Install/type.json';

        $jsonContent["app_type"] = $appType["app_type"];
        $jsonContent["name"] = $appType["name"];
        $jsonContent["description"] = $appType["description"];
        $jsonContent["version"] = $appType["version"];
        $jsonContent["repo"] = $appType["repo"];

        $jsonContent = $this->helper->encode($jsonContent, JSON_UNESCAPED_SLASHES);
        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);
        $jsonContent = str_replace('\\n', '', $jsonContent);
        $jsonContent = $this->basepackages->utils->formatJson(['json' => $jsonContent]);

        try {
            $this->localContent->write($jsonFile, $jsonContent);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write json content to file: ' . $jsonFile);

            return false;
        }

        return true;
    }

    protected function checkModuleTypeAndCategory($data)
    {
        if (isset($data['category']) && str_contains($data['category'], '"data"')) {
            $data['category'] = $this->helper->decode($data['category'], true);
            if (isset($data['category']['data'][0])) {
                $data['category'] = $data['category']['data'][0];
            } else if (isset($data['category']['newTags'][0])) {
                $data['category'] = strtolower($data['category']['newTags'][0]);
            }
        }

        if ($data['module_type'] === 'packages') {//Possible error can happen when cloning core modules.
            if (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'providers') {
                $data['app_type'] = 'core';
            }
        }

        return $data;
    }

    public function getModuleTypes()
    {
        return
            [
                'apptypes'    =>
                    [
                        'id'    => 'apptypes',
                        'name'  => 'App Types'
                    ],
                'bundles'    =>
                    [
                        'id'    => 'bundles',
                        'name'  => 'Bundles'
                    ],
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

        return $this->helper->encode($defaultSettings);
    }

    public function getDefaultDependencies($type = null)
    {
        $defaultDependencies =
            [
                'core'                      => [],
                'apptype'                   => [],
                'components'                => [],
                'packages'                  => [],
                'middlewares'               => [],
                'views'                     => [],
                'external'                  => [
                    'composer'              => [
                        'require'           => []
                    ],
                    'config'                => [
                        'allow-plugins'     => []
                    ],
                    'extra'                 => [
                        'patches'           => []
                    ]
                ]
            ];

        if ($type && $type === 'views') {
            unset($defaultDependencies['external']);
        }

        return $this->helper->encode($defaultDependencies);
    }

    protected function updateModuleJson($data, $viaGenerateRelease = false, $viewPublic = false)
    {
        $jsonFile = $this->getModuleJsonFileLocation($data);

        if ($viaGenerateRelease) {
            try {
                $jsonContent = $this->helper->decode($this->localContent->read($jsonFile), true);
            } catch (FilesystemException | UnableToWriteFile $exception) {
                $this->addResponse('Unable to read json content to file: ' . $jsonFile);

                return false;
            }

            $jsonContent['version'] = $data['version'];

            if (isset($jsonContent['dependencies']['views']) &&
                count($jsonContent['dependencies']['views']) === 0
            ) {
                $data['base_view_module_id'] = 0;
            }

            $repo = $jsonContent['repo'];
        } else {
            $data = $this->jsonData($data, true);
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
                $jsonContent["is_subview"] = $data["is_subview"] == '0' ? false : true;
            }

            $jsonContent["settings"] = $data["settings"];
            if ($data['module_type'] === 'components') {
                $jsonContent["widgets"] = $data["widgets"];
            }
        }

        $jsonContent = $this->helper->encode($jsonContent, JSON_UNESCAPED_SLASHES);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);
        $jsonContent = str_replace('\\n', '', $jsonContent);
        $jsonContent = $this->basepackages->utils->formatJson(['json' => $jsonContent]);

        try {
            $this->localContent->write($jsonFile, $jsonContent);

            if ($data['module_type'] === 'views' && $viewPublic) {
                $jsonFile = $this->getNewFilesLocation($data, true);

                if (!str_contains($data['repo'], '-public')) {
                    $jsonContent = str_replace($data['repo'], $data['repo'] . '-public', $jsonContent);
                }

                $this->localContent->write($jsonFile . 'view.json', $jsonContent);
            }
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write json content to file: ' . $jsonFile);

            return false;
        }

        if ($viaGenerateRelease) {
            return $jsonContent;
        }

        return true;
    }

    protected function mergeViewSettings($data)
    {
        $baseView = $this->modules->views->getViewById($data['base_view_module_id']);

        if ($baseView) {
            $baseViewJsonLocation = $this->getModuleJsonFileLocation($baseView);

            try {
                $baseViewJson = $this->helper->decode($this->localContent->read($baseViewJsonLocation), true);
            } catch (FilesystemException | UnableToReadFile $exception) {
                $this->addResponse('Unable to read base view json content to file: ' . $baseViewJsonLocation);

                return false;
            }

            $baseView = $this->jsonData($baseView, true);
            $baseViewJson = $this->jsonData($baseViewJson, true);

            //Overwrite baseview with original json file because only developer changes the json file that is imported into the db via module installer on install or update.
            $baseView['settings']['head']['link'] = $baseViewJson['settings']['head']['link'];
            $baseView['settings']['head']['script'] = $baseViewJson['settings']['head']['script'];
            $baseView['settings']['footer']['script'] = $baseViewJson['settings']['footer']['script'];

            $subViews = $this->modules->views->getViewsByBaseViewModuleId($baseView['id']);

            $assets = ['link', 'head', 'footer'];
            $envs = ['dev', 'prod'];

            foreach ($subViews as $subView) {
                $subView = $this->jsonData($subView, true);

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
        if ($data['module_type'] === 'apptypes') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/';
        } else if ($data['module_type'] === 'components') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Components/';
        } else if ($data['module_type'] === 'packages') {
            if ($data['app_type'] === 'core' &&
                (str_starts_with($data['category'], 'basepackages') ||
                 $data['category'] === 'providers')
            ) {
                if ($data['category'] === 'basepackagesApis') {
                    $moduleLocation = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Basepackages/ApiClientServices/Apis/';
                } else if (str_starts_with($data['category'], 'basepackages')) {
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
            (str_starts_with($data['category'], 'basepackages') ||
            $data['category'] === 'providers')
        ) {
            if ($data['category'] === 'basepackagesApis') {
                $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);
                $path = implode('/', $pathArr);
            } else {
                $path = ucfirst($data['name']);
            }

            return
                $moduleLocation .
                $path . '/' .
                substr($data['module_type'], 0, -1) . '.json';
        } else {
            if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
                return $moduleLocation . 'Install/type.json';
            } else if ($data['module_type'] === 'components') {
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

        $method = 'generateNew' . ucfirst($data['module_type']) . 'Files';

        $this->{$method}($moduleFilesLocation, $data);

        return true;
    }

    protected function getNewFilesLocation($data, $viewPublic = false)
    {
        if ($data['module_type'] === 'components') {
            $moduleLocation = 'apps/' . ucfirst($data['app_type']) . '/Components/';
        } else if ($data['module_type'] === 'packages') {
            if ($data['app_type'] === 'core' &&
                (str_starts_with($data['category'], 'basepackages') ||
                 $data['category'] === 'providers')
            ) {
                if ($data['category'] === 'basepackagesApis') {
                    $moduleLocation = 'system/Base/Providers/BasepackagesServiceProvider/Packages/ApiClientServices/Apis/';
                } else if (str_starts_with($data['category'], 'basepackages')) {
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

            if ($viewPublic) {
                $moduleLocation = 'public/' . $data['app_type'] . '/' . strtolower($data['name']) . '/';

                return $moduleLocation;
            }
        }

        if ($data['module_type'] === 'packages' &&
            (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'providers')
        ) {
            if (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'basepackagesApis') {
                $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

                if ($data['category'] !== 'basepackagesApis') {
                    unset($pathArr[$this->helper->lastKey($pathArr)]);
                }

                return
                    $moduleLocation .
                    implode('/', $pathArr) . '/';
            }

            return
                $moduleLocation .
                ucfirst($data['name']) . 'ServiceProvider/';
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
                $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

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

        $componentName = ucfirst($this->helper->last(explode('/', $data['route']))) . 'Component';

        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/Component.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base component file.');

            return false;
        }

        $data['class'] = explode('\\', $data['class']);
        unset($data['class'][$this->helper->lastKey($data['class'])]);
        $namespaceClass = implode('\\', $data['class']);

        $file = str_replace('"NAMESPACE"', 'namespace ' . $namespaceClass, $file);
        $file = str_replace('"COMPONENTNAME"', $componentName, $file);

        try {
            $this->localContent->write($moduleFilesLocation . $componentName . '.php', $file);
            array_push($this->newFiles, $moduleFilesLocation . $componentName . '.php');
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module component file');

            return false;
        }

        if (isset($data['widgets']) && $data['widgets'] !== '') {
            $data['widgets'] = $this->helper->decode($data['widgets'], true);

            try {
                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/ComponentWidget.txt');
            } catch (FilesystemException | UnableToReadFile $exception) {
                $this->addResponse('Unable to read module base component widget file.');

                return false;
            }


            $file = str_replace('"NAMESPACE"', 'namespace ' . $namespaceClass, $file);

            foreach ($data['widgets'] as $widget) {
                try {
                    $methodFile = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/ComponentWidgetMethod.txt');
                } catch (FilesystemException | UnableToReadFile $exception) {
                    $this->addResponse('Unable to read module base component widget method file.');

                    return false;
                }

                $methodFile = str_replace('"WIDGETNAME"', $widget['method'], $methodFile);

$file .= '
' . $methodFile;
            }

            $file .= '}';
        }

        try {
            $this->localContent->write($moduleFilesLocation . 'Widgets.php', $file);
            array_push($this->newFiles, $moduleFilesLocation . 'Widgets.php');
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module component file');

            return false;
        }

        return true;
    }

    protected function generateNewMiddlewaresFiles($moduleFilesLocation, $data)
    {
        //Package File
        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/Middleware.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base package file.');

            return false;
        }

        $data['class'] = explode('\\', $data['class']);
        unset($data['class'][$this->helper->lastKey($data['class'])]);
        $namespaceClass = implode('\\', $data['class']);

        $file = str_replace('"NAMESPACE"', 'namespace ' . $namespaceClass . ';', $file);
        $file = str_replace('"MIDDLEWARENAME"', $data['name'], $file);
        $fileName = $moduleFilesLocation . $data['name'] . '.php';

        try {
            $this->localContent->write($fileName, $file);

            array_push($this->newFiles, $fileName);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module middleware file');

            return false;
        }
    }

    protected function generateNewPackagesFiles($moduleFilesLocation, $data)
    {
        //Package File
        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/Package.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base package file.');

            return false;
        }

        $data['class'] = explode('\\', $data['class']);
        if (!str_starts_with($data['category'], 'basepackages')) {
            unset($data['class'][$this->helper->lastKey($data['class'])]);
        }
        $namespaceClass = implode('\\', $data['class']);

        $file = str_replace('"NAMESPACE"', 'namespace ' . $namespaceClass . ';', $file);
        if ($data['category'] === 'basepackagesApis') {
            $file = str_replace('"PACKAGENAME"', 'Apis' . ucfirst($data['name']), $file);
        } else {
            $file = str_replace('"PACKAGENAME"', ucfirst($data['name']), $file);
        }
        $file = str_replace('"PACKAGENAMELC"', strtolower($data['name']), $file);

        if (str_starts_with($data['category'], 'basepackages')) {
            if ($data['category'] === 'basepackagesApis') {
                $fileName = $moduleFilesLocation . 'Apis' . ucfirst($data['name']) . '.php';
            } else {
                $fileName = $moduleFilesLocation . $this->helper->last(preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY)) . '.php';
            }
        } else {
            $fileName = $moduleFilesLocation . ucfirst($data['name']) . '.php';
        }

        try {
            $this->localContent->write($fileName, $file);

            array_push($this->newFiles, $fileName);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module package file');

            return false;
        }

        $this->generateNewPackagesInstallFiles($data, $moduleFilesLocation);
        $this->generateNewPackagesModelFiles($data, $moduleFilesLocation);
    }

    protected function generateNewPackagesInstallFiles($data, $moduleFilesLocation)
    {
        if ($data['category'] === 'basepackagesApis') {//We do not generate schema for Repositories as it already exists.
            return true;
        }

        //Install Schema File
        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/PackageInstallSchema.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base package file.');

            return false;
        }

        if (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'providers') {
            // str_starts_with($data['category'], 'basepackages')
            $moduleFilesLocation = $this->getModuleJsonFileLocation($data);
            $moduleFilesLocation = str_replace('Register/Modules/Packages', 'Schema', $moduleFilesLocation);
            $moduleFilesLocation = rtrim(str_replace('package.json', '', $moduleFilesLocation), '/');

            $fileName = $moduleFilesLocation . '.php';
            $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
            $moduleFilesLocationClass = str_replace('\\' . $data['name'], '', $moduleFilesLocationClass);
            $moduleSchemaClass = $moduleFilesLocationClass . '\\' . $data['name'];
        } else {
            $moduleFilesLocation = $moduleFilesLocation . 'Install/Schema';
            $fileName = $moduleFilesLocation . '/' . $data['name'] . '.php';
            $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
            $moduleFilesLocationClass = str_replace('\\' . $data['name'], '', $moduleFilesLocationClass);
            $moduleSchemaClass = $moduleFilesLocationClass . '\\' . $data['name'];
        }

        $file = str_replace('"NAMESPACE"', 'namespace ' . $moduleFilesLocationClass . ';', $file);
        $file = str_replace('"PACKAGESCHEMANAME"', $data['name'], $file);

        try {
            $this->localContent->write($fileName, $file);
            array_push($this->newFiles, $fileName);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module package file');

            return false;
        }

        //Package Installer File only for apps.
        if (!str_starts_with($data['category'], 'basepackages') && $data['category'] !== 'providers') {
            try {
                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/PackageInstallPackage.txt');
            } catch (FilesystemException | UnableToReadFile $exception) {
                $this->addResponse('Unable to read module base package file.');

                return false;
            }

            if ($data['category'] !== str_starts_with($data['category'], 'basepackages') && $data['category'] !== 'providers') {
                $moduleFilesLocation = str_replace('/Schema', '', $moduleFilesLocation);
                $fileName = $moduleFilesLocation . '/' . 'Package.php';
                $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
                $moduleFilesLocationClass = str_replace('\\' . $data['name'], '', $moduleFilesLocationClass);
                $file = str_replace('"NAMESPACE"', 'namespace ' . $moduleFilesLocationClass . ';', $file);
                $file = str_replace('"PACKAGESCHEMACLASS"', $moduleSchemaClass . ';', $file);
                $file = str_replace('"PACKAGESCHEMANAME"', $data['name'], $file);

                try {
                    $this->localContent->write($fileName, $file);
                    array_push($this->newFiles, $fileName);
                } catch (FilesystemException | UnableToWriteFile $exception) {
                    $this->addResponse('Unable to write module package file');

                    return false;
                }
            }
        }

        return true;
    }

    protected function generateNewPackagesModelFiles($data, $moduleFilesLocation)
    {
        //Install Model File
        try {
            $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/PackageModel.txt');
        } catch (FilesystemException | UnableToReadFile $exception) {
            $this->addResponse('Unable to read module base package file.');

            return false;
        }

        if (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'providers') {
            if (str_starts_with($data['category'], 'basepackages')) {
                if ($data['category'] === 'basepackagesApis') {
                    $moduleFilesLocation = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Model/ApiClientServices/Apis/';
                    $pathArr = preg_split('/(?=[A-Z])/', $data['name'], -1, PREG_SPLIT_NO_EMPTY);
                    unset($pathArr[$this->helper->lastKey($pathArr)]);

                    $fileName = $moduleFilesLocation . implode('/', $pathArr) . '/' . '/BasepackagesApiClientServicesApis' . ucfirst($data['name']) . '.php';
                    $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation) . '/' . implode('/', $pathArr) . '/');
                    $className = 'BasepackagesApiClientServicesApis' . ucfirst($data['name']);
                } else {
                    $moduleFilesLocation = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Model/';
                    $fileName = $moduleFilesLocation . '/Basepackages' . ucfirst($data['name']) . '.php';
                    $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
                    $className = 'Basepackages' . ucfirst($data['name']);
                }
            } else if ($data['category'] === 'providers') {
                $moduleFilesLocation = 'system/Base/Providers/' . ucfirst($data['name']) . 'ServiceProvider/Model/';
                $fileName = $moduleFilesLocation . '/ServiceProvider' . ucfirst($data['name']) . '.php';

                $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
                $className = 'ServiceProvider' . ucfirst($data['name']);
            }
        } else {
            $moduleFilesLocation = $moduleFilesLocation . 'Model';
            $fileName = $moduleFilesLocation . '/' . 'Apps' . ucfirst($data['app_type']) . ucfirst($data['name']) . '.php';
            $moduleFilesLocationClass = str_replace('/', '\\', ucfirst($moduleFilesLocation));
            $moduleFilesLocationClass = str_replace('\\' . ucfirst($data['name']), '', $moduleFilesLocationClass);
            $className = 'Apps' . ucfirst($data['app_type']) . ucfirst($data['name']);
        }

        $file = str_replace('"NAMESPACE"', 'namespace ' . rtrim($moduleFilesLocationClass, '\\') . ';', $file);
        $file = str_replace('"PACKAGEMODELNAME"', $className, $file);

        try {
            $this->localContent->write($fileName, $file);
            array_push($this->newFiles, $fileName);
        } catch (FilesystemException | UnableToWriteFile $exception) {
            $this->addResponse('Unable to write module package file');

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
                    $data['settings'] = $this->helper->decode($data['settings'], true);
                }

                $this->localContent->createDirectory($moduleFilesLocation . 'html');
                array_push($this->newDirs, $moduleFilesLocation . 'html');
                $this->localContent->createDirectory($moduleFilesLocation . 'html/layouts');
                array_push($this->newDirs, $moduleFilesLocation . 'html/layouts');
                $this->localContent->createDirectory($moduleFilesLocation . 'html/common');
                array_push($this->newDirs, $moduleFilesLocation . 'html/common');
                $this->localContent->write($moduleFilesLocation . 'html/common/.gitkeep', '');

                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/ViewsLayout.txt');

                foreach ($data['settings']['layouts'] as $layout) {
                    $this->localContent->write($moduleFilesLocation . 'html/layouts/' . $layout['view'] . '.html', $file);
                    array_push($this->newFiles, $moduleFilesLocation . 'html/layouts/' . $layout['view'] . '.html');
                }

                $this->localContent->createDirectory($modulePublicFilesLocation . 'css');
                array_push($this->newDirs, $modulePublicFilesLocation . 'css');
                $this->localContent->write($modulePublicFilesLocation . 'css/.gitkeep', '');

                $this->localContent->createDirectory($modulePublicFilesLocation . 'fonts');
                array_push($this->newDirs, $modulePublicFilesLocation . 'fonts');
                $this->localContent->write($modulePublicFilesLocation . 'fonts/.gitkeep', '');

                $this->localContent->createDirectory($modulePublicFilesLocation . 'images');
                array_push($this->newDirs, $modulePublicFilesLocation . 'images');
                $this->localContent->write($modulePublicFilesLocation . 'images/.gitkeep', '');

                $this->localContent->createDirectory($modulePublicFilesLocation . 'js');
                array_push($this->newDirs, $modulePublicFilesLocation . 'js');
                $this->localContent->write($modulePublicFilesLocation . 'js/.gitkeep', '');

                $this->localContent->createDirectory($modulePublicFilesLocation . 'sounds');
                array_push($this->newDirs, $modulePublicFilesLocation . 'sounds');
                $this->localContent->write($modulePublicFilesLocation . 'sounds/.gitkeep', '');

                $file = $this->localContent->read('apps/Core/Packages/Devtools/Modules/Files/ViewsGitignore.txt');
                $this->localContent->write($moduleFilesLocation . '.gitignore', $file);
            } catch (FilesystemException | UnableToCreateDirectory | UnableToWriteFile | UnableToReadFile $exception) {
                $this->addResponse('Unable to create view directories or read/write module base html files in public folder.');

                return false;
            }
        } else {
            try {
                $this->localContent->write($moduleFilesLocation . 'view.html', $data['name'] . ' Main View');
                array_push($this->newFiles, $moduleFilesLocation . 'view.html');
                $this->localContent->write($moduleFilesLocation . 'list.html', $data['name'] . ' List View');
                array_push($this->newFiles, $moduleFilesLocation . 'list.html');
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

                        $module = $this->modules->{$data['module_type']}->getById($data['id']);

                        $module['menu_id'] = null;
                        $module['menu'] = null;

                        $this->modules->{$data['module_type']}->update($module);

                        return;
                    }
                }
            }
        }

        if ($data['menu'] != 'false' && $data['menu'] != '') {
            $data['menu'] = $this->helper->decode($data['menu'], true);

            if (isset($menu)) {
                $this->basepackages->menus->updateMenu($data['menu_id'], $data['app_type'], $data['menu']);

                return;
            } else {
                $menu = $this->basepackages->menus->addMenu($data['app_type'], $data['menu']);

                if ($menu) {
                    $module = $this->modules->{$data['module_type']}->packagesData->last;

                    $module['menu_id'] = $menu['id'];
                }

                $this->modules->{$data['module_type']}->update($module);
            }
        }
    }

    public function syncLabels($data)
    {
        if (isset($data['module_type']) &&
            $data['module_type'] === 'apptypes'
        ) {
            $module = $this->apps->types->getAppTypeById($data['id']);
        } else {
            $module = $this->modules->{$data['module_type']}->getById($data['id']);
        }

        if (!$module) {
            $this->addResponse('Bundle not found.', 1);

            return false;
        }

        if (!$this->initApi($module)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListLabels';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListLabelsForRepo';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                strtolower($this->helper->last(explode('/', $module['repo'])))
            ];

        try {
            $labels = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getmessage(), 1);

            return false;
        }

        if ($labels) {
            $labelsArr = ['labels' => $labels];
            $this->addResponse('Labels Synced', 0, $labelsArr);

            return $labelsArr;
        }

        $this->addResponse('Error syncing labels or no labels configured.', 1);
    }

    public function getMilestoneLabelIssues($data, $viaGenerateRelease = false)
    {
        if ($viaGenerateRelease) {
            $data['milestone'] = $data['version'];
            $module = $data;
            $status = 'open';
        } else {
            if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
                $module = $this->apps->types->getAppTypeById($data['id']);
            } else {
                $module = $this->modules->{$data['module_type']}->getById($data['id']);
            }

            if (!$module) {
                $this->addResponse('Bundle not found.', 1);

                return false;
            }

            if (!$this->initApi($module)) {
                $this->addResponse('Could not initialize the API assigned to this module.', 1);

                return false;
            }

            //Get Latest Release (Last One, we need to sync issues since that release)
            $this->getReleases($module, true);

            $status = 'closed';
        }

        $currentMilestones = $this->syncMilestones($data);

        $found = false;

        if ($currentMilestones && isset($currentMilestones['milestones'])) {
            array_walk($currentMilestones['milestones'], function($milestone) use(&$found, $data) {
                if ((int) $data['milestone'] !== 0) {
                    $milestoneIdentifier = $milestone['number'] ?? $milestone['id'];

                    if ((int) $milestoneIdentifier === (int) $data['milestone']) {
                        $found = true;
                    }
                } else {
                    if ($milestone['title'] === $data['milestone']) {
                        $found = true;
                    }
                }
            });
        }

        if (!$found) {
            $this->addResponse('Error syncing issues as milestone does not exists', 1);

            return false;
        }

        if (!$this->latestRelease) {
            $since = null;
        } else {
            $since = $this->latestRelease['created_at'];
        }

        if (!isset($data['label'])) {
            $data['label'] = null;
        } else if (is_array($data['label'])) {
            $data['label'] = implode(',', $data['label']);
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListIssues';
            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $module['repo']))),
                    $status,
                    $data['label'],
                    null,
                    null,
                    (int) $data['milestone'],
                    $since
                ];
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListForRepo';
            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $module['repo']))),
                    (int) $data['milestone'],
                    $status,
                    null,
                    null,
                    null,
                    $data['label'],
                    'created',
                    'desc',
                    $since
                ];
        }

        try {
            $issues = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if ($issues) {
                $this->addResponse('Issues Synced', 0, ['issues' => $issues]);

                return $issues;
            }

            $this->addResponse('No issues found with selected milestone/label', 1);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    protected function getReleases($module, $getLatestRelease = false)
    {
        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoListReleases';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposListReleases';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                strtolower($module['repo'])
            ];

        try {
            $this->releases = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            if ($e->getCode() === 404) {
                return false;
            }

            $this->addResponse($e->getMessage(), 1);
        }

        if (isset($this->releases) && is_array($this->releases)) {
            if (count($this->releases) === 1 || $getLatestRelease) {
                $this->latestRelease = $this->releases[0];
            }
        }
    }

    protected function initApi($data)
    {
        if ($this->apiClient && $this->apiClientConfig) {
            return true;
        }

        if (!isset($data['api_id']) || !isset($data['name'])) {
            $this->addResponse('API information not provided', 1, []);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] == '0') {
            $this->addResponse('This is local module and not remote module, cannot sync.', 1, []);

            return false;
        }

        $this->apiClient = $this->basepackages->apiClientServices->useApi($data['api_id'], true);

        $this->apiClientConfig = $this->apiClient->getApiConfig();

        if ($this->apiClientConfig['auth_type'] === 'auth' &&
            ((!$this->apiClientConfig['username'] || $this->apiClientConfig['username'] === '') &&
            (!$this->apiClientConfig['password'] || $this->apiClientConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'access_token' &&
                  (!$this->apiClientConfig['access_token'] || $this->apiClientConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'autho' &&
                  (!$this->apiClientConfig['authorization'] || $this->apiClientConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return false;
        }

        return true;
    }

    public function bumpVersion($data)
    {
        if (isset($data['bump']) && $data['bump'] === 'custom') {
            return false;
        } else if (!isset($data['bump']) ||
                   (isset($data['bump']) && $data['bump'] === '')
        ) {
            $this->addResponse('Bump version to what?', 1);

            return false;
        }

        if (!isset($data['repo'])) {
            if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
                $module = $this->apps->types->getAppTypeById($data['id']);
            } else {
                $module = $this->modules->{$data['module_type']}->getById($data['id']);
            }

            if (!$module) {
                $this->addResponse('Module not found.', 1);

                return false;
            }

            if (!$this->initApi($module)) {
                $this->addResponse('Could not initialize the API assigned to this module.', 1);

                return false;
            }

            $module['repo'] = $this->helper->last(explode('/', $module['repo']));
            $module['bump'] = $data['bump'];
        } else {
            $module = $data;
        }

        if (isset($data['preReleasePrefix']) || isset($data['buildMetaPrefix'])) {
            if (isset($data['preReleasePrefix'])) {
                if (!checkCtype($data['preReleasePrefix'], 'alpha', ['.'])) {
                    $this->addResponse('Pre release prefix cannot have special chars or numbers (except .(dot)).', 1);
                    return false;
                }
                $module['preReleasePrefix'] = $data['preReleasePrefix'];
            }
            if (isset($data['buildMetaPrefix'])) {
                if (!checkCtype($data['buildMetaPrefix'], 'alpha', ['.'])) {
                    $this->addResponse('Build meta prefix cannot have special chars or numbers (except .(dot)).', 1);
                    return false;
                }
                $module['buildMetaPrefix'] = $data['buildMetaPrefix'];
            }
        }

        $this->getReleases($module, true);

        if ($this->latestRelease) {
            $latestReleaseVersion = $this->latestRelease['name'];
        } else {
            $moduleVersion = $module['version'];

            if (isset($module['preReleasePrefix']) || isset($module['buildMetaPrefix'])) {
                if (isset($module['preReleasePrefix'])) {
                    $moduleVersion = $moduleVersion . '-' . $module['preReleasePrefix'];
                }

                $parsedVersion = Version::parse($moduleVersion);

                $parsedVersionString = null;

                if (isset($module['preReleasePrefix'])) {
                    $parsedVersion = $parsedVersion->getNextPreReleaseVersion();
                }

                $parsedVersionString = $parsedVersion->__toString();

                if (isset($module['buildMetaPrefix'])) {
                    $module['buildMetaPrefix'] = explode('.', $module['buildMetaPrefix']);

                    array_walk($module['buildMetaPrefix'], function(&$prefix) {
                        if ($prefix === 'now') {//now
                            $prefix = time();
                        }
                        if ($prefix === 'dom') {//day of month
                            $prefix = (\Carbon\Carbon::now())->day;
                        }
                        if ($prefix === 'moy') {//month of year
                            $prefix = (\Carbon\Carbon::now())->month;
                        }
                        if ($prefix === 'wom') {//week of month
                            $prefix = (\Carbon\Carbon::now())->weekOfMonth;
                        }
                        if ($prefix === 'woy') {//week of year
                            $prefix = (\Carbon\Carbon::now())->weekOfYear;
                        }
                    });
                    $parsedVersionString = $parsedVersionString . '+' . implode('.', $module['buildMetaPrefix']);
                }
            }

            $this->addResponse('No version found on remote repository.', 1, ['release' => $parsedVersionString ?? $moduleVersion]);

            return ['release' => $parsedVersionString ?? $moduleVersion];
        }

        if (isset($module['module_type']) && $module['module_type'] === 'core') {
            $currentVersion = $this->core->getVersion();
        } else {
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

        $versionIsDraft = false;
        $versionMessage = 'New Version';

        if ($this->latestRelease && $this->latestRelease['draft'] == true) {
            $newVersion = $currentVersion;
            $versionIsDraft = true;
            $versionMessage = 'Remote Version is Draft';
            $compareVersion = 3;
        } else {
            if (isset($module['preReleasePrefix']) || isset($module['buildMetaPrefix'])) {
                $moduleVersion = $currentVersion;

                $module['bump'] = explode('_', $module['bump']);

                if (count($module['bump']) > 1) {
                    $moduleVersionArr = explode('-', $moduleVersion);
                    if (count($moduleVersionArr) === 2) {
                        $version = Version::parse($moduleVersionArr[0]);

                        $bump = 'getNext' . ucfirst($module['bump'][0]) . 'Version';
                        $moduleVersion = $version->$bump();

                        if ($module['bump'][1] === 'preRelease' ||
                            $module['bump'][1] === 'preReleaseBuildMeta'
                        ) {
                            if (isset($module['preReleasePrefix'])) {
                                $moduleVersion = $moduleVersion->__toString() . '-' . $module['preReleasePrefix'];
                                $moduleVersion = Version::parse($moduleVersion);
                            }

                            $moduleVersion = $moduleVersion->getNextPreReleaseVersion();
                        }

                        $moduleVersion = $moduleVersion->__toString();
                    }
                }

                if (isset($module['preReleasePrefix']) && !isset($module['buildMetaPrefix'])) {
                    $moduleVersion = explode('+', $moduleVersion)[0];
                }

                $parsedVersion = Version::parse($moduleVersion);

                if (!$parsedVersion->isPreRelease() && isset($module['preReleasePrefix'])) {
                    $parsedVersion = Version::parse($parsedVersion->__toString() . '-' . $module['preReleasePrefix']);
                }

                if (isset($module['preReleasePrefix']) && count($module['bump']) === 1) {
                    $parsedVersion = $parsedVersion->getNextPreReleaseVersion();
                }

                $newVersion = $parsedVersion->__toString();

                if (!$parsedVersion->getBuildMeta() && isset($module['buildMetaPrefix'])) {
                    $module['buildMetaPrefix'] = explode('.', $module['buildMetaPrefix']);

                    array_walk($module['buildMetaPrefix'], function(&$prefix) {
                        if ($prefix === 'now') {//now
                            $prefix = time();
                        }
                        if ($prefix === 'dom') {//day of month
                            $prefix = (\Carbon\Carbon::now())->day;
                        }
                        if ($prefix === 'moy') {//month of year
                            $prefix = (\Carbon\Carbon::now())->month;
                        }
                        if ($prefix === 'wom') {//week of month
                            $prefix = (\Carbon\Carbon::now())->weekOfMonth;
                        }
                        if ($prefix === 'woy') {//week of year
                            $prefix = (\Carbon\Carbon::now())->weekOfYear;
                        }
                    });

                    if (!isset($module['preReleasePrefix'])) {
                        $newVersion = explode('-', $newVersion);
                        $newVersion = $newVersion[0];
                    }

                    $newVersion = $newVersion . '+' . implode('.', $module['buildMetaPrefix']);
                } else if ($parsedVersion->getBuildMeta() &&
                           isset($module['buildMetaPrefix']) &&
                           str_contains($module['buildMetaPrefix'], 'now')
                ) {
                    $module['buildMetaPrefix'] = explode('.', $module['buildMetaPrefix']);

                    $newVersion = explode('+', $newVersion)[0];

                    array_walk($module['buildMetaPrefix'], function(&$prefix) {
                        if ($prefix === 'now') {//now
                            $prefix = time();
                        }
                        if ($prefix === 'dom') {//day of month
                            $prefix = (\Carbon\Carbon::now())->day;
                        }
                        if ($prefix === 'moy') {//month of year
                            $prefix = (\Carbon\Carbon::now())->month;
                        }
                        if ($prefix === 'wom') {//week of month
                            $prefix = (\Carbon\Carbon::now())->weekOfMonth;
                        }
                        if ($prefix === 'woy') {//week of year
                            $prefix = (\Carbon\Carbon::now())->weekOfYear;
                        }
                    });

                    if (!isset($module['preReleasePrefix'])) {
                        $newVersion = explode('-', $newVersion);
                        $newVersion = $newVersion[0];
                    }

                    $newVersion = $newVersion . '+' . implode('.', $module['buildMetaPrefix']);
                }
            } else {
                $version = Version::parse($currentVersion);
                if ($version->isPreRelease()) {
                    $newVersion = $version->withoutSuffixes();
                } else {
                    $bump = 'getNext' . ucfirst($module['bump']) . 'Version';
                    $newVersion = $version->$bump();
                }
                $newVersion = $newVersion->__toString();
            }
        }

        if (isset($newVersion)) {
            $versionData = ['currentVersion' => $currentVersion, 'newVersion' => $newVersion, 'versionIsDraft' => $versionIsDraft];

            $this->addResponse(
                $versionMessage,
                $compareVersion,
                $versionData
            );

            return $versionData;
        }

        $this->addResponse('Could not retrieve current/next version', 2);
    }

    public function syncBranches($data)
    {
        if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
            $module = $this->apps->types->getAppTypeById($data['id']);
        } else {
            $module = $this->modules->{$data['module_type']}->getById($data['id']);
        }

        if (!$module) {
            $this->addResponse('Module not found.', 1);

            return false;
        }

        if (!$this->initApi($module)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                strtolower($this->helper->last(explode('/', $module['repo'])))
            ];

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoListBranches';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'GitApi';
            $method = 'gitListMatchingRefs';

            $args = array_merge($args,
                [
                    'heads'
                ]
            );
        }

        try {
            $branches = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if (strtolower($this->apiClientConfig['provider']) === 'github') {
                if ($branches && is_array($branches) && count($branches) > 0) {

                    foreach ($branches as &$branch) {
                        if (isset($branch['ref'])) {
                            $branch['ref'] = explode('/', $branch['ref']);
                            $branch['name'] = $this->helper->last($branch['ref']);
                        }
                    }
                }
            }
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

    public function syncMilestones($data)
    {
        if (isset($data['module_type']) && $data['module_type'] === 'apptypes') {
            $module = $this->apps->types->getAppTypeById($data['id']);
        } else {
            $module = $this->modules->{$data['module_type']}->getById($data['id']);
        }

        if (!$module) {
            $this->addResponse('Module not found.', 1);

            return false;
        }

        if (!$this->initApi($module)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueGetMilestonesList';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListMilestones';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                strtolower($this->helper->last(explode('/', $module['repo']))),
                'open'
            ];

        try {
            $milestones = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($milestones) {
            $this->addResponse('Milestones Synced', 0, ['milestones' => $milestones]);

            return ['milestones' => $milestones];
        }

        $this->addResponse('Error syncing milestones or no milestones configured.', 1);
    }

    public function generateRelease($data)
    {
        if ($data['module_type'] === 'apptypes') {
            $module = $this->apps->types->getAppTypeById($data['id']);
            $module['module_type'] = 'apptypes';
        } else {
            $module = $this->modules->{$data['module_type']}->getById($data['id']);
        }

        if ($module['app_type'] === 'core' &&
            strtolower($module['name']) !== 'core'
        ) {
            $this->addResponse('Release for core modules are generated via Core package.', 1);

            return false;
        }

        if (!$module) {
            $this->addResponse('Module not found.', 1);

            return false;
        }

        if (!$this->initApi($module)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        $prerelease = false;
        if (($data['bump'] == 'preRelease' ||
             $data['bump'] == 'buildMeta' ||
             $data['bump'] == 'preReleaseBuildMeta' ||
             $data['bump'] == 'major_preRelease' ||
             $data['bump'] == 'major_buildMeta' ||
             $data['bump'] == 'major_preReleaseBuildMeta' ||
             $data['bump'] == 'minor_preRelease' ||
             $data['bump'] == 'minor_buildMeta' ||
             $data['bump'] == 'minor_preReleaseBuildMeta' ||
             $data['bump'] == 'patch_preRelease' ||
             $data['bump'] == 'patch_buildMeta' ||
             $data['bump'] == 'patch_preReleaseBuildMeta') ||
            (isset($data['force-mark-prerelease']) && $data['force-mark-prerelease'] == 'true')
        ) {
            $prerelease = true;
        }

        $versionData = $this->bumpVersion($data);

        $name = $module['version'];
        if (isset($versionData['newVersion'])) {
            $name = $versionData['newVersion'];
        } else if (isset($versionData['release'])) {
            $name = $versionData['release'];
        }
        //We first update Json File with the updated version and we push the json file with the new version and
        //then we generate latest release using the updated json file, else the json file on remote consist of older version
        //and needs to be manually pushed before release.
        //NOTE: The API key used should have write permissions to the branch which is used for generating the release.
        if (!$this->latestRelease ||
            ($this->latestRelease && $data['mark-as-draft'] == 'false')
        ) {
            //Check for any open issues against the milestone that was created during draft creation.
            if ($this->getMilestoneLabelIssues($module, true)) {
                $this->addResponse('Milestone ' . $module['version'] . ' has a issues open. Please close those issues before generating release.', 1);

                return false;
            }

            $module['version'] = $name;
            $module['branch'] = $data['branch'];
            //Check for any open pull request against the branch we have to create release from.
            if (!$this->checkPullRequests($module)) {
                return false;
            }

            if ($data['module_type'] === 'apptypes') {
                $module['module_type'] = 'apptypes';
            }

            $module['commit_message'] = 'Update json file version for release ' . $module['version'];

            if ($data['module_type'] === 'bundles') {
                $this->commitBundleJson($module);
            } else {
                $reposArr = [$module['repo']];

                if ($data['module_type'] === 'views') {
                    $fileLocation = explode('/Default/', $this->getModuleJsonFileLocation($module));
                } else {
                    $fileLocation = explode('/Install/', $this->getModuleJsonFileLocation($module));
                }

                if (count($fileLocation) > 1) {
                    $fileLocation = $data['module_type'] === 'views' ? 'view.json' : 'Install/' . $fileLocation[1];
                } else if (count($fileLocation) === 1) {
                    $fileLocation = $fileLocation[0];
                }

                if (isset($data['module_type']) &&
                    $data['module_type'] === 'views' &&
                    isset($data['base_view_module_id']) &&
                    $data['base_view_module_id'] == 0
                ) {
                    array_push($reposArr, $module['repo'] . '-public');
                }

                foreach ($reposArr as $repo) {
                    if (str_contains($repo, '-public')) {
                        $jsonContent = $this->updateModuleJson($module, true, true);
                    } else {
                        $jsonContent = $this->updateModuleJson($module, true);
                    }

                    if (!$jsonContent) {
                        return false;
                    }

                    //Check for json file on remote
                    if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                        \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Gitea\ObjectSerializer::setUrlEncoding(false);

                        $collection = 'RepositoryApi';
                        $method = 'repoGetContents';
                    } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                        $collection = 'ReposApi';
                        $method = 'reposGetContent';
                    }

                    try {
                        $file = $this->apiClient->useMethod(
                            $collection,
                            $method,
                            [
                                $this->apiClientConfig['org_user'],
                                strtolower($this->helper->last(explode('/', $repo))),
                                $fileLocation,
                                $module['branch']
                            ]
                        )->getResponse(true);
                    } catch (\throwable $e) {
                        $this->addResponse($e->getMessage(), 1);

                        return;
                    }

                    $base64EncodedJsonContent = base64_encode($jsonContent);

                    if (str_replace(["\n", "\r"], '', $file['content']) !== $base64EncodedJsonContent ||
                        $module['version'] === '0.0.0'
                    ) {
                        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                            $collection = 'RepositoryApi';
                            $method = 'repoUpdateFile';
                        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                            $collection = 'ReposApi';
                            $method = 'reposCreateOrUpdateFileContents';
                        }

                        $args =
                            [
                                $this->apiClientConfig['org_user'],
                                strtolower($this->helper->last(explode('/', $repo))),
                                $fileLocation,
                                [
                                    'message'   => $module['commit_message'],
                                    'content'   => $base64EncodedJsonContent,
                                    'branch'    => $module['branch'],
                                    'sha'       => $file['sha']
                                ]
                            ];

                        try {
                            $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                        } catch (\throwable $e) {
                            $this->addResponse($e->getMessage(), 1);

                            return;
                        }
                    }
                }
            }
        }

        //Now we generate release after updating the json file.
        $reposArr = [$module['repo']];

        if (isset($data['module_type']) &&
            $data['module_type'] === 'views' &&
            isset($module['base_view_module_id']) &&
            $module['base_view_module_id'] == 0
        ) {
            array_push($reposArr, $module['repo'] . '-public');
        }

        $releaseData = [];

        foreach ($reposArr as $repo) {
            $args = [];
            $args = array_merge($args,
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $repo)))
                ]
            );

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                if ($this->latestRelease && $this->latestRelease['draft'] == true) {
                    $collection = 'RepositoryApi';
                    $method = 'repoEditRelease';
                    $args = array_merge($args, [$this->latestRelease['id']]);
                } else {
                    $collection = 'RepositoryApi';
                    $method = 'repoCreateRelease';
                }
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                if ($this->latestRelease && $this->latestRelease['draft'] == true) {
                    $collection = 'ReposApi';
                    $method = 'reposUpdateRelease';
                    $args = array_merge($args, [$this->latestRelease['id']]);
                } else {
                    $collection = 'ReposApi';
                    $method = 'reposCreateRelease';
                }
            }

            $args = array_merge($args,
                [
                    [
                        'body'              => $data['release_notes'],
                        'draft'             => (isset($data['mark-as-draft']) && $data['mark-as-draft'] == 'true') ? true : false,
                        'name'              => $name,
                        'prerelease'        => $prerelease,
                        'tag_name'          => $name,
                        'target_commitish'  => $data['branch']
                    ]
                ]
            );

            try {
                $newRelease = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                if ($newRelease) {
                    if ($data['module_type'] === 'apptypes') {
                        $this->apps->types->updateAppType($module);
                    } else {
                        $this->modules->{$data['module_type']}->update($module);

                        if (strtolower($module['name']) === 'core' &&
                            $this->core->getVersion() !== $module['version']
                        ) {
                            $core = $this->core->core;

                            $core['version'] = $module['version'];

                            $this->core->update($core);
                        }
                    }

                    if ($data['module_type'] === 'views' && str_contains($repo, '-public')) {
                        $releaseData = array_merge($releaseData, ['newReleasePublic' => $newRelease]);
                    } else {
                        $releaseData = array_merge($releaseData, ['newRelease' => $newRelease]);
                    }

                    if (isset($data['mark-as-draft']) && $data['mark-as-draft'] == 'true') {
                        $newMilestone = $this->createReleaseMilestone($versionData, $data, $module, $repo);

                        if ($newMilestone && is_array($newMilestone) && isset($newMilestone['newMilestone'])) {
                            $releaseData = array_merge($releaseData, $newMilestone);
                        }
                    } else {
                        $this->closeReleaseMilestone($versionData, $data, $module, $repo);
                    }
                }
            } catch (\throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return;
            }
        }

        if (count($releaseData) > 0 && isset($releaseData['newRelease'])) {
            $this->addResponse('Generated New Release!', 0, $releaseData);

            return true;
        }

        $this->addResponse('Error generating new release', 1);
    }

    protected function createReleaseMilestone($versionData, $data, $module, $repo)
    {
        if (isset($versionData['newVersion'])) {
            $version = $versionData['newVersion'];
        } else if (isset($versionData['release'])) {
            $version = $versionData['release'];
        }

        $currentMilestones = $this->syncMilestones($data);

        $found = false;

        if ($currentMilestones && isset($currentMilestones['milestones'])) {
            array_walk($currentMilestones['milestones'], function($milestone) use(&$found, $version) {
                if ($milestone['title'] === $version) {
                    $found = $milestone['number'] ?? $milestone['id'];
                }
            });
        }

        if (!$found) {
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'IssueApi';
                $method = 'issueCreateMilestone';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'IssuesApi';
                $method = 'issuesCreateMilestone';
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $repo))),
                    [
                        'title'         => $version,
                        'state'         => 'open',
                        'description'   => 'Tracking milestone for version ' . $version
                    ]
                ];

            try {
                $newMilestone = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                return ['newMilestone' => $newMilestone];
            } catch (\throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            $this->addResponse('Error generating new label', 1);
        }

        return true;
    }

    protected function closeReleaseMilestone($versionData, $data, $module, $repo)
    {
        if (isset($versionData['newVersion'])) {
            $version = $versionData['newVersion'];
        } else if (isset($versionData['release'])) {
            $version = $versionData['release'];
        }

        $currentMilestones = $this->syncMilestones($data);

        $found = false;

        if ($currentMilestones && isset($currentMilestones['milestones'])) {
            array_walk($currentMilestones['milestones'], function($milestone) use(&$found, $version) {
                if ($milestone['title'] === $version) {
                    $found = $milestone['number'] ?? $milestone['id'];
                }
            });
        }

        if ($found) {
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'IssueApi';
                $method = 'issueEditMilestone';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'IssuesApi';
                $method = 'issuesUpdateMilestone';
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $repo))),
                    $found,
                    [
                        'state'         => 'closed',
                    ]
                ];
            try {
                $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
            } catch (\throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            return true;
        }

        return false;
    }

    public function commitBundleJson($data)
    {
        $bundle = $this->modules->bundles->getById($data['id']);

        if (!$bundle) {
            $this->addResponse('Bundle not found.', 1);

            return false;
        }

        $bundle['createrepo'] = false;

        if (isset($data['createrepo'])) {
            $bundle['createrepo'] = $data['createrepo'];
        }

        $bundle['commit_message'] = $data['commit_message'];

        if (!$this->initApi($bundle)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        $repo = $this->checkRepo($bundle);

        //Create new repo if doesnt exist.
        if (!$repo && $bundle['createrepo'] == 'false') {
            return false;
        } else if (!$repo) {
            $newRepo = $this->createRepo($bundle);

            if (!$newRepo) {
                return false;
            }
        }

        //Check for bundle.json file
        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGetContents';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposGetContent';
        }

        try {
            $file = $this->apiClient->useMethod(
                $collection,
                $method,
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $bundle['repo']))),
                    'bundle.json',
                    $data['branch'] ?? 'dev'
                ]
            )->getResponse(true);
        } catch (\throwable $e) {
            if ($e->getCode() !== 404) {
                $this->addResponse($e->getMessage(), 1);

                return;
            }
        }

        $jsonContent = [];
        $jsonContent["name"] = $bundle["name"];
        $jsonContent["description"] = $bundle["description"];
        $jsonContent["module_type"] = $bundle["module_type"];
        $jsonContent["app_type"] = $bundle["app_type"];
        $jsonContent["repo"] = $bundle["repo"];
        $jsonContent["version"] = $data["version"] ?? $bundle["version"];
        if (is_string($bundle["bundle_modules"])) {
            $bundle["bundle_modules"] = $this->helper->decode($bundle["bundle_modules"], true);
        }
        $jsonContent["bundle_modules"] = $bundle["bundle_modules"];

        $jsonContent = $this->helper->encode($jsonContent, JSON_UNESCAPED_SLASHES);

        $jsonContent = str_replace('\\"', '"', $jsonContent);
        $jsonContent = str_replace('"{', '{', $jsonContent);
        $jsonContent = str_replace('}"', '}', $jsonContent);
        $jsonContent = $this->basepackages->utils->formatJson(['json' => $jsonContent]);
        $base64EncodedJsonContent = base64_encode($jsonContent);

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoCreateFile';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposCreateOrUpdateFileContents';
        }

        //Create File if not found
        if (!isset($file)) {
            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $bundle['repo']))),
                    'bundle.json',
                    [
                        'message'   => $bundle['commit_message'],
                        'content'   => $base64EncodedJsonContent,
                        'branch'    => $data['branch'] ?? 'dev'
                    ]
                ];
        } else {
            if (str_replace(["\n", "\r"], '', $file['content']) === $base64EncodedJsonContent) {
                $this->addResponse('Local and remote files are same. Nothing to commit!');

                return true;
            }

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $method = 'repoUpdateFile';
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $bundle['repo']))),
                    'bundle.json',
                    [
                        'message'   => $bundle['commit_message'],
                        'content'   => $base64EncodedJsonContent,
                        'sha'       => $file['sha'],
                        'branch'    => $data['branch'] ?? 'dev'
                    ]
                ];
        }

        try {
            $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return;
        }

        $this->addResponse('Added/Updated bundle.json to repository', 0, []);
    }

    protected function checkRepo($data)
    {
        if (!$this->initApi($data)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return true;
        }

        //Check Repo if exists
        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoGet';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposGet';
        }

        try {
            $repo =
                $this->apiClient->useMethod(
                    $collection,
                    $method,
                    [
                        $this->apiClientConfig['org_user'],
                        strtolower($this->helper->last(explode('/', $data['repo'])))
                    ]
                )->getResponse(true);
        } catch (\throwable $e) {
            if ($e->getCode() === 404 && $data['createrepo'] == 'false') {
                $this->addResponse('Repository does not exist. Please check create repo and try again.' . $e->getMessage(), 1);

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
        if (!$this->initApi($data)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        $data['repo'] = $this->helper->last(explode('/', $data['repo']));

        $repoArgs =
            [
                "name"              => $data['repo'],
                "description"       => $data['repo'],
                "auto_init"         => true,
                "private"           => false
            ];

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'OrganizationApi';
            $method = 'createOrgRepo';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposCreateInOrg';

            $repoArgs = array_merge($repoArgs,
                [
                    "has_issues"        => true,
                    "has_projects"      => true
                ]
            );
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $repoArgs
            ];

        try {
            $newRepo = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if (isset($newRepo)) {
            $this->createRepoDevBranch($data);

            return $newRepo;
        }

        return false;
    }

    protected function checkPullRequests($data)
    {
        if (!$this->initApi($data)) {
            $this->addResponse('Could not initialize the API assigned to this module.', 1);

            return false;
        }

        $reposArr = [$data['repo']];

        if (isset($data['module_type']) &&
            $data['module_type'] === 'views' &&
            isset($data['base_view_module_id']) &&
            $data['base_view_module_id'] == 0
        ) {
            array_push($reposArr, $data['repo'] . '-public');
        }

        $pendingPullRequest = false;

        foreach ($reposArr as $repo) {
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoListPullRequests';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'PullsApi';
                $method = 'pullsList';
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    strtolower($this->helper->last(explode('/', $repo))),
                    'open'
                ];

            try {
                $pullRequests = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                if ($pullRequests) {
                    foreach ($pullRequests as $pullRequest) {
                        if (isset($pullRequest['base']['ref']) &&
                            $pullRequest['base']['ref'] === $data['branch']
                        ) {
                            $pendingPullRequest = true;

                            break;
                        }
                    }
                }
            } catch (\throwable $e) {
                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            if ($pendingPullRequest) {
                $this->addResponse('Branch ' . $data['branch'] . ' has a pull request pending for repo ' . $repo . '. Please merge before generating release.', 1);

                return false;
            }
        }

        return true;
    }

    protected function createRepoDevBranch($data)
    {
        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'RepositoryApi';
            $method = 'repoCreateBranch';

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    $data['repo'],
                    [
                        "new_branch_name"   => 'dev'
                    ]
                ];
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            //Get Main Branch Ref
            $collection = 'GitApi';
            $method = 'gitGetRef';

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    $data['repo'],
                    'heads/main'
                ];
        }

        try {
            $mainRef = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if (strtolower($this->apiClientConfig['provider']) === 'github') {
                if ($mainRef && isset($mainRef['object']['sha'])) {
                    //Create Dev Branch
                    $collection = 'GitApi';
                    $method = 'gitCreateRef';

                    $args =
                        [
                            $this->apiClientConfig['org_user'],
                            $data['repo'],
                            [
                                'ref' => 'refs/heads/dev',
                                'sha' => $mainRef['object']['sha']
                            ]
                        ];

                    $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                }
            }
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }
    }

    public function generateModuleClass($data)
    {
        $this->validation->init()->add('app_type', PresenceOf::class, ["message" => "Please provide app type."]);
        $this->validation->add('module_type', PresenceOf::class, ["message" => "Please provide module type."]);

        if (!$this->validateData($data)) {
            return false;
        }

        if ($data['module_type'] === 'components') {
            $this->validation->add('route', PresenceOf::class, ["message" => "Please provide route."]);

            if (!$this->validateData($data)) {
                return false;
            }
        } else if ($data['module_type'] === 'packages') {
            $this->validation->add('name', PresenceOf::class, ["message" => "Please provide name."]);
            $this->validation->add('category', PresenceOf::class, ["message" => "Please provide category."]);

            if (!$this->validateData($data)) {
                return false;
            }
        } else if ($data['module_type'] === 'middlewares') {
            $this->validation->add('name', PresenceOf::class, ["message" => "Please provide name."]);

            if (!$this->validateData($data)) {
                return false;
            }
        }

        if ($data['module_type'] === 'components') {
            $routeArr = explode('/', trim($data['route'], '/'));

            array_walk($routeArr, function(&$route) {
                $route = ucfirst($route);
            });

            array_push($routeArr, $this->helper->last($routeArr) . 'Component');

            $class = 'Apps\\';

            $class .= ucfirst($data['app_type']) . '\\' . ucfirst($data['module_type']) . '\\' . implode('\\', $routeArr);
        } else if ($data['module_type'] === 'packages') {
            $class = '';

            if ($data['category'] === 'providers') {
                $class .= 'System\Base\Providers\\' . ucfirst($data['name']) . 'ServiceProvider' . '\\' . ucfirst($data['name']);
            } else if (str_starts_with($data['category'], 'basepackages') || $data['category'] === 'basepackagesApis') {
                if ($data['category'] === 'basepackagesApis') {
                    $pathArr = preg_split('/(?=[A-Z])/', ucfirst($data['name']), -1, PREG_SPLIT_NO_EMPTY);

                    $routePath = implode('\\', $pathArr) . '\\Apis' . ucfirst($data['name']);

                    $class .= 'System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\\' . $routePath;
                } else {
                    $class .= 'System\Base\Providers\BasepackagesServiceProvider\Packages\\' . ucfirst($data['name']);
                }
            } else {
                $name = lcfirst($data['name']);
                $name = preg_split('/(?=[A-Z])/', $name);
                $name[0] = ucfirst($name[0]);

                $class .= 'Apps\\' . ucfirst($data['app_type']) . '\Packages\\' . implode('\\', $name) . '\\' . ucfirst($data['name']);
            }
        } else if ($data['module_type'] === 'middlewares') {
            $class = 'Apps\\' . ucfirst($data['app_type']) . '\Middlewares\\' . ucfirst($data['name']) . '\\' . ucfirst($data['name']);
        }

        $this->addResponse('Generated Class', 0, ['class' => $class]);

        return $class;
    }

    public function generateModuleRepoUrl($data)
    {
        $this->validation->init()->add('api_id', PresenceOf::class, ["message" => "Please provide api id."]);
        $this->validation->add('app_type', PresenceOf::class, ["message" => "Please provide app type."]);
        $this->validation->add('module_type', PresenceOf::class, ["message" => "Please provide module type."]);

        if (!$this->validateData($data)) {
            return false;
        }

        if ($data['api_id'] == '0') {
            $this->addResponse('Generated local repo', 0, ['repo' => 'https://.../']);

            return true;
        }

        if ($data['module_type'] === 'components') {
            $this->validation->add('route', PresenceOf::class, ["message" => "Please provide route."]);

            if (!$this->validateData($data)) {
                return false;
            }
        } else {
            $this->validation->add('name', PresenceOf::class, ["message" => "Please provide name."]);

            if ($data['module_type'] !== 'bundles' && $data['module_type'] !== 'apps_types') {
                $this->validation->add('category', PresenceOf::class, ["message" => "Please provide category."]);
            }

            if ($data['module_type'] === 'views' && isset($data['is_subview']) && $data['is_subview'] == 'true') {
                $this->validation->add('base_view_module_id', PresenceOf::class, ["message" => "Please provide main view module id."]);
            }

            if (!$this->validateData($data)) {
                return false;
            }

            if ($data['module_type'] === 'views' &&
                strtolower($data['name']) === 'public'
            ) {
                $this->addResponse('Public keyword for module type views is reserved', 1);

                return false;
            }

            if ($data['module_type'] === 'views' && isset($data['is_subview']) && $data['is_subview'] == 'true') {
                $baseView = $this->modules->views->getViewById($data['base_view_module_id']);
            }

            if ($data['module_type'] === 'components' || $data['module_type'] === 'apps_types') {
                $ignoreChars = [' '];
            } else {
                $ignoreChars = [''];
            }

            if (!checkCtype($data['name'], 'alpha', $ignoreChars)) {
                $this->addResponse('Name cannot have special chars or numbers.', 1);

                return false;
            }
        }

        $api = $this->basepackages->apiClientServices->getApiById($data['api_id']);

        if (!$api) {
            $this->addResponse('API id incorrect', 1);

            return false;
        }

        $url = $api['repo_url'];

        if ($data['module_type'] === 'components') {
            $data['route'] = str_replace('/', '', trim($data['route'], '/'));

            $url .= '/' . $data['app_type'] . '-' . $data['module_type'] . '-' . $data['category'] . '-' . $data['route'];
        } else if ($data['module_type'] === 'apps_types') {
            if ($data['app_type'] === 'core') {
                $url = 'https://.../';
            } else {
                $url .= '/' . $data['app_type'];
            }
        } else {
            $name = lcfirst($data['name']);
            $name = preg_split('/(?=[A-Z])/', $name);

            if ($data['module_type'] !== 'bundles') {
                if ($name[0] !== 'core') {
                    if (isset($baseView) && isset($baseView['name'])) {
                        $name = strtolower($baseView['name'] . '-' . implode('', $name));
                    } else {
                        $name = strtolower(implode('', $name));
                    }

                    $url .= '/' . $data['app_type'] . '-' . $data['module_type'] . '-' . $data['category'] . '-' . $name;
                }
            } else {
                $url .= '/' . $data['app_type'] . '-' . $data['module_type'] . '-' . strtolower(implode('', $name));
            }
        }

        $this->addResponse('Generated Repo Url', 0, ['repo' => $url]);

        return true;
    }

    public function checkVersion($data)
    {
        if (!isset($data['version']) ||
            (isset($data['version']) && $data['version'] === '')
        ) {
            $this->addResponse('Please provide version.', 1);

            return false;
        }

        try {
            $version = Version::parse($data['version']);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        $this->addResponse('Version correct');

        return $data;
    }

    protected function validateData($data)
    {
        $validated = $this->validation->validate($data)->jsonSerialize();

        if (count($validated) > 0) {
            $messages = 'Error: ';

            foreach ($validated as $key => $value) {
                $messages .= $value['message'] . ' ';
            }

            $this->addResponse($messages, 1, []);

            return false;
        }

        return true;
    }

    public function getAvailableApis($getAll = false, $returnApis = true)
    {
        $apis = [];
        $apis[0]['id'] = 0;
        $apis[0]['name'] = 'Local Modules';
        $apis[0]['data']['url'] = 'https://.../';

        $apisArr = [];

        if (!$getAll) {
            $package = $this->getPackage();
            if (isset($package['settings']) &&
                isset($package['settings']['api_clients']) &&
                is_array($package['settings']['api_clients']) &&
                count($package['settings']['api_clients']) > 0
            ) {
                foreach ($package['settings']['api_clients'] as $key => $clientId) {
                    $client = $this->basepackages->apiClientServices->getApiById($clientId);

                    if ($client) {
                        array_push($apisArr, $client);
                    }
                }
            }
        } else {
            $apisArr = $this->basepackages->apiClientServices->getAll()->apiClientServices;
        }

        if (count($apisArr) > 0) {
            foreach ($apisArr as $api) {
                if ($api['category'] === 'repos') {
                    $useApi = $this->basepackages->apiClientServices->useApi([
                            'config' =>
                                [
                                    'id'           => $api['id'],
                                    'category'     => $api['category'],
                                    'provider'     => $api['provider'],
                                    'checkOnly'    => true//Set this to check if the API exists and can be instantiated.
                                ]
                        ]);

                    if ($useApi) {
                        $apiConfig = $useApi->getApiConfig();

                        $apis[$api['id']]['id'] = $apiConfig['id'];
                        $apis[$api['id']]['name'] = $apiConfig['name'];
                        $apis[$api['id']]['data']['url'] = $apiConfig['repo_url'];
                    }
                }
            }
        }

        if ($returnApis) {
            return $apis;
        }

        return $apisArr;
    }

    public function getAvailableReleaseTypes()
    {
        return
            [
                'major' => [
                    'id'    => 'major',
                    'name'  => 'MAJOR'
                ],
                'minor' => [
                    'id'    => 'minor',
                    'name'  => 'MINOR'
                ],
                'patch' => [
                    'id'    => 'patch',
                    'name'  => 'PATCH'
                ],
                'preRelease' => [
                    'id'    => 'preRelease',
                    'name'  => 'PRE RELEASE'
                ],
                'buildMeta' => [
                    'id'    => 'buildMeta',
                    'name'  => 'BUILD META'
                ],
                'preReleaseBuildMeta' => [
                    'id'    => 'preReleaseBuildMeta',
                    'name'  => 'PRE RELEASE + BUILD META'
                ],
                'major_preRelease' => [
                    'id'    => 'major_preRelease',
                    'name'  => 'MAJOR + PRE RELEASE'
                ],
                'major_buildMeta' => [
                    'id'    => 'major_buildMeta',
                    'name'  => 'MAJOR + BUILD META'
                ],
                'major_preReleaseBuildMeta' => [
                    'id'    => 'major_preReleaseBuildMeta',
                    'name'  => 'MAJOR + PRE RELEASE + BUILD META'
                ],
                'minor_preRelease' => [
                    'id'    => 'minor_preRelease',
                    'name'  => 'MINOR + PRE RELEASE'
                ],
                'minor_buildMeta' => [
                    'id'    => 'minor_buildMeta',
                    'name'  => 'MINOR + BUILD META'
                ],
                'minor_preReleaseBuildMeta' => [
                    'id'    => 'minor_preReleaseBuildMeta',
                    'name'  => 'MINOR + PRE RELEASE + BUILD META'
                ],
                'patch_preRelease' => [
                    'id'    => 'patch_preRelease',
                    'name'  => 'PATCH + PRE RELEASE'
                ],
                'patch_buildMeta' => [
                    'id'    => 'patch_buildMeta',
                    'name'  => 'PATCH + BUILD META'
                ],
                'patch_preReleaseBuildMeta' => [
                    'id'    => 'patch_preReleaseBuildMeta',
                    'name'  => 'PATCH + PRE RELEASE + BUILD META'
                ],
                'custom' => [
                    'id'    => 'custom',
                    'name'  => 'CUSTOM (NON SEMANTIC VERSION)'
               ]
            ];
    }
}