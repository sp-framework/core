<?php

namespace System\Base\Providers\ModulesServiceProvider;

use Phalcon\Helper\Json;
use System\Base\BasePackage;
use z4kn4fein\SemVer\Version;

class Installer extends BasePackage
{
    protected $api;

    protected $apiConfig;

    protected $process;

    protected $downloadLocation = 'var/tmp/installer/';

    protected $zip;

    protected $zipFiles = [];

    protected $modulesToProcess = [];

    protected $preCheckResult = [];

    protected $modulesToInstallOrUpdate = [];

    protected $runProcessPrecheckProgressMethods;

    protected $installProgressMethods;

    protected $updatedBy;

    public function init($process = 'precheck')
    {
        $this->process = $process;

        $this->zip = new \ZipArchive;

        if (!$this->localContent->fileExists($this->downloadLocation)) {
            $this->localContent->createDirectory($this->downloadLocation);
        }

        $this->basepackages->progress->init(null, 'modulesinstaller');

        if ($this->basepackages->progress->checkProgressFile()) {
            $this->basepackages->progress->deleteProgressFile();
        }

        if ($process === 'runprecheck') {
            $this->registerRunProcessPrecheckProgressMethods();
        } else if ($process === 'runprocess') {
            $this->registerRunProcessProgressMethods();
        }

        if ($this->auth->account()) {//For Autoupdate
            $this->updatedBy = $this->auth->account()['id'];
        } else {
            $this->updatedBy = '0';
        }

        return $this;
    }

    protected function withProgress($method, $arguments)
    {
        if (method_exists($this, $method)) {
            if (is_array($arguments)) {
                $arguments = [$arguments];
            }

            $this->basepackages->progress->updateProgress($method, null, false);

            $call = call_user_func_array([$this, $method], $arguments);

            $this->basepackages->progress->updateProgress($method, $call, false);

            return $call;
        }
    }

    public function runProcess(array $data)
    {
        if (!isset($data['queue']) || isset($data['queue']) && !is_array($data['queue'])) {
            $this->addResponse('Nothing to process. Add something to queue!', 1);

            $this->basepackages->progress->preCheckComplete(false);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        set_time_limit(300);//5 mins

        if ($this->process === 'runprecheck') {
            $this->basepackages->progress->preCheckComplete();

            foreach ($this->runProcessPrecheckProgressMethods as $method) {
                if ($this->withProgress($method['method'], $data) === false) {
                     return false;
                }

                usleep(500);
            }
        }
    }

    protected function analyseQueueData($data)
    {
        foreach ($data['queue'] as $task => $modules) {
            if ($task === 'install' || $task === 'update') {
                if (count($modules) > 0) {
                    foreach ($modules as $moduleType => $moduleList) {
                        if (count($moduleList) > 0) {
                            foreach ($moduleList as $module) {
                                $moduleInfo = $this->modules->$moduleType->getById($module);

                                if ($moduleInfo) {
                                    if (array_key_exists('files', $moduleInfo)) {
                                        unset($moduleInfo['files']);
                                    }

                                    if (!$moduleInfo['repo_details']) {
                                        $moduleName = $moduleInfo['name'];

                                        $moduleInfo = $this->modules->manager->getModuleInfo(
                                            [
                                                'module_type'   => $moduleType,
                                                'module_id'     => $moduleInfo['id'],
                                                'sync'          => true
                                            ]
                                        );

                                        if (!$moduleInfo) {
                                            $this->addResponse('Could not retrieve repository information for module: ' . $moduleName);

                                            return false;
                                        }
                                    } else {
                                        if (is_string($moduleInfo['repo_details'])) {
                                            try {
                                                $moduleInfo['repo_details'] = Json::decode($moduleInfo['repo_details'], true);
                                            } catch (\Exception $e) {
                                                $this->addResponse('Could not retrieve repository information for module: ' . $moduleInfo['name']);

                                                return false;
                                            }
                                        }
                                    }

                                    array_push($this->modulesToInstallOrUpdate, $moduleInfo);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    protected function downloadModulesFromRepo($data)
    {
        //Needs subProgress for downloading multiple modules from different repos
        foreach ($this->modulesToInstallOrUpdate as $key => $moduleToInstallOrUpdate) {
            // remove old data so there is no conflict
            $files = $this->basepackages->utils->scanDir($this->downloadLocation . $moduleToInstallOrUpdate['repo_details']['details']['name']);
            if (count($files['files']) > 0) {
                foreach ($files['files'] as $file) {
                    $this->localContent->delete($file);
                }
            }
            if (count($files['dirs']) > 0) {
                foreach ($files['dirs'] as $dir) {
                    $this->localContent->deleteDirectory($dir);
                }
            }

            $this->localContent->createDirectory($this->downloadLocation . $moduleToInstallOrUpdate['repo_details']['details']['name']);

            if (!$this->initApi($moduleToInstallOrUpdate['api_id'])) {
                $this->basepackages->progress->resetProgress();

                return false;
            }

            if (strtolower($this->apiConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGetArchive';
                $args = [$this->apiConfig['org_user'], $moduleToInstallOrUpdate['repo_details']['details']['name'], $moduleToInstallOrUpdate['repo_details']['latestRelease']['tag_name'] . '.zip'];
            } else if (strtolower($this->apiConfig['provider']) === 'github') {
                //For github
            }

            try {
                $latestRelease = $this->api->useMethod($collection, $method, $args)->getResponse();
                $file = $this->downloadLocation . $moduleToInstallOrUpdate['repo_details']['details']['name'] . '/' . $moduleToInstallOrUpdate['repo_details']['latestRelease']['tag_name'] . '.zip';

                $this->localContent->write($file, $latestRelease->getContents());
            } catch (\throwable $e) {
                $this->basepackages->progress->resetProgress();

                $this->addResponse($e->getMessage(), 1);

                return false;
            }

            $this->zipFiles[$key]['location'] = base_path($this->downloadLocation . $moduleToInstallOrUpdate['repo_details']['details']['name'] . '/');
            $this->zipFiles[$key]['file'] = base_path($file);
            $this->zipFiles[$key]['name'] = $moduleToInstallOrUpdate['repo_details']['details']['name'];

            $this->modulesToProcess[$moduleToInstallOrUpdate['repo_details']['details']['name']]['location'] =
                $this->downloadLocation . $moduleToInstallOrUpdate['repo_details']['details']['name'] . '/' . $moduleToInstallOrUpdate['repo_details']['details']['name'] . '/';
            $this->modulesToProcess[$moduleToInstallOrUpdate['repo_details']['details']['name']]['module'] = $moduleToInstallOrUpdate;
        }

        return true;
    }

    protected function extractModulesDownloadedFromRepo()
    {
        if (count($this->zipFiles) === 0) {
            return true;
        }

        foreach ($this->zipFiles as $key => $fileInformation) {
            if (!$this->zip->open($fileInformation['file'])) {
                $this->addResponse('Error reading downloaded zip file for module : ' . $fileInformation['name'], 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }

            if (!$this->zip->extractTo($fileInformation['location'])) {
                $this->addResponse('Error unzipping downloaded file for module : ' . $fileInformation['name'], 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }
        }

        return true;
    }

    protected function checkDependencies()
    {
        if (count($this->modulesToProcess) === 0) {
            return true;
        }

        // var_dump($this->modulesToProcess);die();
        foreach ($this->modulesToProcess as $moduleName => $module) {
            $this->preCheckResult[$moduleName] = [];
            $this->preCheckResult[$moduleName]['module'] = $module['module'];
            $this->preCheckResult[$moduleName]['result'] = 'success';
            $this->preCheckResult[$moduleName]['logs'] = '';

            $names = explode('-', $moduleName);

            try {
                $jsonFile = Json::decode($this->localContent->read($module['location'] . 'Install/' . substr($names[1], 0, -1) . '.json'), true);
            } catch (\throwable $e) {
                $this->preCheckResult[$moduleName]['result'] = 'error';
                $this->preCheckResult[$moduleName]['logs'] .= 'Reading module ' . $moduleName . ' install JSON file resulted in error. ';

                continue;//move onto the next one.
            }

            // Make sure we have an entry for all module dependencies. Even if there are no dependencies, we need to make sure we have empty array for them.
            if (!isset($jsonFile['dependencies']) ||
                (isset($jsonFile['dependencies']) &&
                 (!isset($jsonFile['dependencies']['core']) ||
                  !isset($jsonFile['dependencies']['components']) ||
                  !isset($jsonFile['dependencies']['packages']) ||
                  !isset($jsonFile['dependencies']['middlewares']) ||
                  !isset($jsonFile['dependencies']['views'])))
            ) {
                $this->preCheckResult[$moduleName]['result'] = 'error';
                $this->preCheckResult[$moduleName]['logs'] .= 'Reading module ' . $moduleName . ' dependencies resulted in error. ';

                continue;//move onto the next one.
            }

            //Check Core dependency
            if (Version::greaterThan($jsonFile['dependencies']['core']['version'], $this->core->getVersion())) {
                //Lets check for latest release on Core as required version is greater than installed version.
                $core = $this->modules->packages->getNamePackage('core');
                $core = $this->modules->manager->updateModuleRepoDetails($core);

                if ($core) {
                    $this->preCheckResult[$moduleName]['result'] = 'warning';
                    $this->preCheckResult[$moduleName]['logs'] .= 'Module ' . $moduleName . ' needs core version : ' . $core['update_version'] . '. Installed version of core is : ' . $core['version'] . '. Adding Core to list of updates. ';

                    $this->preCheckResult['core'] = [];
                    $this->preCheckResult['core']['module'] = $core;
                    $this->preCheckResult['core']['result'] = 'warning';
                    $name = $module['module']['name'];
                    if (isset($module['module']['display_name'])) {
                        $name = $module['module']['display_name'];
                    }
                    $this->preCheckResult['core']['logs'] = 'Added to queue as required by module : ' . $name . '. ';
                }
            }

            //
        }

        var_dump($this->preCheckResult);die();
        // $this->getLatestRepositoryModulesData();

        ${$this->postData()['type']} =
            $this->{$this->postData()['type']}->getById($this->postData()['id'])->getAllArr();

        $dependencies =
            json_decode(${$this->postData()['type']}['dependencies'], true);

        $checkForDependencies = $this->checkRegisteredDependencies($dependencies);

        if (is_array($checkForDependencies)) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Dependency ' . $checkForDependencies['name'] . ' not found!<br>' .
                'Check repository ' . $checkForDependencies['repo'] . ' for further details.';

            return $this->packagesData;
        }

        return true;
    }

    protected function checkRegisteredDependencies($dependencies)
    {
        $found = true;

        $this->dependenciesToDownload = [];

        foreach ($dependencies as $dependencyKey => $dependencyValue) {
            if ($dependencyKey === 'app') {
                $dependencyKey = $dependencyKey . 's';
            }

            if (!isset($dependencyValue['name'])) { //if dependency is an array
                foreach ($dependencyValue as $multiDependencyKey => $multiDependencyValue) {

                    $thisDependency =
                        $this->{$dependencyKey}->getAll(
                            [
                                'name' => $multiDependencyValue['name'],
                                'repo' => $multiDependencyValue['repo'],
                                'version' => $multiDependencyValue['version']
                            ]
                        );

                    if (!$thisDependency) {

                        $thisDependency =
                            $this->{$dependencyKey}->getAll(
                                [
                                    'name' => $multiDependencyValue['name'],
                                    'repo' => $multiDependencyValue['repo'],
                                    'update_version' => $multiDependencyValue['version'],
                                ]
                            );

                        if ($thisDependency) {

                            if ($thisDependency[0]->get('update_available') === 1) {

                                $multiDependencyValue['type'] = $dependencyKey;
                                $multiDependencyValue['id'] = $thisDependency[0]->get('id');
                                array_push($this->dependenciesToDownload, $multiDependencyValue);
                            }

                            $found = true;

                        } else {
                            $found = $multiDependencyValue;

                            return $found;
                        }
                    } else {
                        if ($thisDependency[0]->get('installed') === 0) {

                            $multiDependencyValue['type'] = $dependencyKey;
                            $multiDependencyValue['id'] = $thisDependency[0]->get('id');
                            array_push($this->dependenciesToDownload, $multiDependencyValue);
                        }

                        $found = true;
                    }
                }
            } else {

                $thisDependency =
                    $this->{$dependencyKey}->getAll(
                        [
                            'name' => $dependencyValue['name'],
                            'repo' => $dependencyValue['repo'],
                            'version' => $dependencyValue['version'],
                        ]
                    );

                if (!$thisDependency) {

                    $thisDependency =
                        $this->{$dependencyKey}->getAll(
                            [
                                'name' => $dependencyValue['name'],
                                'repo' => $dependencyValue['repo'],
                                'update_version' => $dependencyValue['version'],
                            ]
                        );

                        if ($thisDependency) {

                            if ($thisDependency[0]->get('update_available') === 1) {

                                $dependencyValue['type'] = $dependencyKey;
                                $dependencyValue['id'] = $thisDependency[0]->get('id');
                                array_push($this->dependenciesToDownload, $dependencyValue);

                            }

                            $found = true;

                        } else {
                            $found = $dependencyValue;

                            return $found;
                        }

                } else {
                    if ($thisDependency[0]->get('installed') === 0) {

                        $dependencyValue['type'] = $dependencyKey;
                        $dependencyValue['id'] = $thisDependency[0]->get('id');
                        array_push($this->dependenciesToDownload, $dependencyValue);

                    }

                    $found = true;
                }
            }
        }

        return $found;
    }

    protected function getLatestRepositoryModulesData()
    {
        $repositories = getAllArr($this->repositories->getAll());

        if (count($repositories) > 0) {
            $modules = $this->packages->use(Modules::class);

            foreach ($repositories as $repositoryKey => $repositoryValue) {
                $sync = $modules->syncRemoteWithLocal($repositoryValue['id']);

                if ($sync->packagesData['responseCode'] === 1) {
                    $this->packagesData->responseCode = 1;

                    $this->packagesData->responseMessage = 'Error Syncing with repository ' . $repositoryValue['name'];

                    return $this->packagesData;
                }
            }
        }
    }

    protected function createBackup()
    {
        $this->backupLocation = '.backups/';

        $now = new \DateTime('now');

        $this->zip->open(base_path($this->backupLocation . '/' . $now->format('Y_m_d_H_i_s') . '.zip'), $this->zip::CREATE);

        $framework = [];
        $framework['dir'] = [];
        $framework['file'] = [];

        $rootContents = $this->localContent->listContents('/');

        foreach ($rootContents as $rootKey => $rootValue) {
            if ($rootValue['type'] === 'dir') {
                if ($rootValue['path'] !== '.backups' &&
                    $rootValue['path'] !== '.git' &&
                    $rootValue['path'] !== 'vendor'
                ) {
                    array_push($framework['dir'], $rootValue['path']);
                }
            } else if ($rootValue['type'] === 'file') {
                if ($rootValue['path'] !== '.gitignore' &&
                    $rootValue['path'] !== '.htaccess.example'
                ) {
                    array_push($framework['file'], $rootValue['path']);
                }
            }
        }

        foreach ($framework['dir'] as $dirKey => $dirValue) {
            $rootDirContents[$dirValue] = $this->localContent->listContents($dirValue, true);
        }

        foreach ($rootDirContents as $rootDirContentsKey => $rootDirContentsValue) {
            if (count($rootDirContentsValue) > 0) {
                foreach ($rootDirContentsValue as $subDirectoryKey => $subDirectory) {
                    if ($subDirectory['type'] === 'dir') {
                        $this->zip->addEmptyDir($subDirectory['dirname']);
                    } else if ($subDirectory['type'] === 'file') {
                        $this->zip->addFile($subDirectory['path']);
                    }
                }
            } else {
                $this->zip->addEmptyDir($rootDirContentsKey);
            }
        };

        foreach ($framework['file'] as $fileKey => $fileValue) {
            $this->zip->addFile($fileValue);
        }

        $this->zip->close();

        $this->packagesData->backupFile = $now->format('Y_m_d_H_i_s') . '.zip';

        $this->packagesData->responseCode = 0;
    }

    protected function processInstall()
    {
        foreach ($this->modulesToInstall as $moduleToInstallKey => $moduleToInstall) {

            $repoNameArr = explode('/', $moduleToInstall['repo']);
            $repoName = end($repoNameArr);

            $this->downloadPackagesAndDependencies($moduleToInstall);

            $contents = $this->extractDownloadedPackagesAndDependencies(
                $moduleToInstall['name'],
                $repoName,
                $moduleToInstall['type']
            );

            if ($contents) {
                $files = $this->copyFilesToDestination(
                    $contents,
                    $moduleToInstall['name'],
                    $repoName,
                    $moduleToInstall['type']
                );
            }


            $this->{$moduleToInstall['type']}->update(
                [
                    'id'        => $moduleToInstall['id'],
                    'installed' => 1,
                    'files'     => json_encode($files),
                ]
            );
        }
    }

    protected function processUpdate()
    {
        $files = [];

        foreach ($this->modulesToInstall as $moduleToInstallKey => $moduleToInstall) {
            //
            //Delete only files first
            //Check if directory is empty, then delete directory
            //check if directory is html_compiled, if so, then scan directory, delete all files first and then all directories.
            //Copy all files.
            //
            var_dump(json_decode($this->{$moduleToInstall['type']}->getById($moduleToInstall['id'])->getAllArr()['files'], true));
            // $repoNameArr = explode('/', $moduleToInstall['repo']);
            // $repoName = end($repoNameArr);

            // $this->downloadPackagesAndDependencies($moduleToInstall);

            // $contents = $this->extractDownloadedPackagesAndDependencies(
            //  $moduleToInstall['name'],
            //  $repoName,
            //  $moduleToInstall['type']
            // );

            // if ($contents) {
            //  $files = $this->copyFilesToDestination(
            //      $contents,
            //      $moduleToInstall['name'],
            //      $repoName,
            //      $moduleToInstall['type']
            //  );
            // }


            // $this->{$moduleToInstall['type']}->update(
            //  [
            //      'id'        => $moduleToInstall['id'],
            //      'installed' => 1,
            //      'files'     => json_encode($files),
            //  ]
            // );
        }
    }

    protected function copyFilesToDestination($contents, $name, $repoName, $type)
    {
        $installedFiles = [];
        $installedFiles['dir'] = [];
        $installedFiles['files'] = [];

        foreach ($contents as $contentKey => $content) {
            $destDir =
                str_replace(
                    $this->downloadLocation . '/' . strtolower($repoName) . '-master/',
                    '',
                    $content['dirname']
                );

            if ($content['type'] === 'dir') {

                $this->localContent->createDirectory($destDir . '/' . $content['basename']);

                array_push($installedFiles['dir'], $destDir . '/' . $content['basename']);

            } else if ($content['type'] === 'file') {

                if ($content['basename'] !== '.gitkeep') {

                    $this->localContent->copy($content['path'], $destDir . '/' . $content['basename']);

                    array_push($installedFiles['files'], $destDir . '/' . $content['basename']);
                }

            }
        }

        return $installedFiles;
        // if ($type === 'components' || $type === 'packages' || $type === 'middlewares') {
        //  if ($type === 'components') {
        //      $this->localContent->write(
        //          $type . '/'. $this->appName . '/Install/' . $this->componentName . '/files.info', json_encode($installedFiles)
        //      );
        //  } else if ($type === 'packages') {
        //      $this->localContent->write(
        //          $type . '/'. $this->appName . '/Install/' . $this->packageName . '/files.info', json_encode($installedFiles)
        //      );
        //  } else if ($type === 'middlewares') {
        //      $this->localContent->write(
        //          $type . '/'. $this->appName . '/Install/' . $this->middlewareName . '/files.info', json_encode($installedFiles)
        //      );
        //  }
        // } else {
        //  $this->localContent->write($type . '/'. $this->appName . '/files.info', json_encode($installedFiles));
        // }
    }

    protected function deleteDownloads()
    {
        $downloadsToDelete = $this->localContent->listContents('.downloads', true);

        $downloadedFiles = [];
        $downloadedFiles['dir'] = [];
        $downloadedFiles['files'] = [];

        foreach ($downloadsToDelete as $key => $value) {
            if ($value['type'] === 'dir') {
                array_push($downloadedFiles['dir'], $value['path']);
            } else if ($value['type'] === 'file') {
                array_push($downloadedFiles['files'], $value['path']);
            }
        }

        if (count($downloadedFiles['files']) > 0) {
            foreach ($downloadedFiles['files'] as $fileKey => $file) {
                $this->localContent->delete($file);
            }
        }

        if (count($downloadedFiles['dir']) > 0) {
            foreach ($downloadedFiles['dir'] as $dirKey => $dir) {
                $this->localContent->deleteDir($dir);
            }
        }
    }

    protected function rollBack()
    {
        //
    }

    protected function registerRunProcessPrecheckProgressMethods()
    {
        $this->runProcessPrecheckProgressMethods =
            [
                [
                    'method'    => 'analyseQueueData',
                    'text'      => 'Analyse Queue...'
                ],
                [
                    'method'    => 'downloadModulesFromRepo',
                    'text'      => 'Download module files from repository...'
                ],
                [
                    'method'    => 'extractModulesDownloadedFromRepo',
                    'text'      => 'Extract downloaded module zip files...'
                ],
                [
                    'method'    => 'checkDependencies',
                    'text'      => 'Checking dependencies...'
                ],
                [
                    'method'    => 'finishPrecheck',
                    'text'      => 'Fnishing up...'
                ]
            ];

        $this->basepackages->progress->registerMethods($this->runProcessPrecheckProgressMethods);
    }

    protected function registerRunProcessProgressMethods()
    {
        $this->runProcessProgressMethods =
            [
                [
                    'method'    => 'unzipBackupFiles',
                    'text'      => 'Unzipping backup file...'
                ],
                [
                    'method'    => 'performStructureRestore',
                    'text'      => 'Restore file structure...'
                ],
                [
                    'method'    => 'performDbRestore',
                    'text'      => 'Restore databases...'
                ]
            ];

        $this->basepackages->progress->registerMethods($this->runProcessProgressMethods);
    }

    protected function initApi($id)
    {
        $this->api = $this->basepackages->api->useApi($id, true);

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

    // protected function downloadPackagesAndDependencies($module)
    // {
    //     try {
    //         $downloadedContents = $this->remoteWebContent
    //                 ->request('GET', $module['repo'] . '/archive/master.zip')
    //                 ->getBody()
    //                 ->getContents();

    //     } catch (\Exception $e) {
    //         $this->packagesData->responseCode = 1;

    //         $this->packagesData->responseMessage = $e->getMessage();

    //         return $this->packagesData;
    //     }

    //     $this->downloadLocation = '.downloads/';

    //     if (!$this->localContent->fileExists($this->downloadLocation . ucfirst($module['type']))) {
    //         $this->localContent->createDirectory($this->downloadLocation . ucfirst($module['type']));
    //         $this->downloadLocation = $this->downloadLocation . ucfirst($module['type']);
    //     } else {
    //         $this->downloadLocation = $this->downloadLocation . ucfirst($module['type']);
    //     }

    //     if (!$this->localContent->fileExists($this->downloadLocation . '/' . $module['name'])) {
    //         $this->localContent->createDirectory($this->downloadLocation . '/' . $module['name']);
    //         $this->downloadLocation = $this->downloadLocation . '/' . $module['name'];
    //     } else {
    //         $this->downloadLocation = $this->downloadLocation . '/' . $module['name'];
    //     }

    //     if (!$this->localContent->fileExists($this->downloadLocation . '/' . $module['version'])) {
    //         $this->localContent->createDirectory($this->downloadLocation . '/' . $module['version']);
    //         $this->downloadLocation = $this->downloadLocation . '/' . $module['version'];
    //     } else {
    //         $this->downloadLocation = $this->downloadLocation . '/' . $module['version'];
    //     }

    //     // if (!$this->localContent->fileExists($this->downloadLocation . '/' . $module['repobranch'])) {
    //     //  $this->localContent->createDirectory($this->downloadLocation . '/' . $module['repobranch']);
    //     //  $this->downloadLocation = $this->downloadLocation . '/' . $module['repobranch'];
    //     // } else {
    //     //  $this->downloadLocation = $this->downloadLocation . '/' . $module['repobranch'];
    //     // }

    //     // var_dump($this->downloadLocation);

    //     $this->localContent->write(
    //         $this->downloadLocation . '/master.zip',
    //         $downloadedContents
    //     );
    // }

    // protected function extractDownloadedPackagesAndDependencies($name, $repoName, $type)
    // {
    //     if ($this->zip->open($this->downloadLocation . '/master.zip')) {
    //         $this->zip->extractTo($this->downloadLocation);

    //         if ($type === 'core') {
    //             $extractedLocation
    //                 = $this->downloadLocation . '/' . strtolower($repoName) .
    //                 '-master/';
    //         } else if ($type === 'app') {
    //             $extractedLocation
    //                 = $this->downloadLocation . '/' . strtolower($repoName) .
    //                 '-master/' . $type . 's/';
    //         } else {
    //             $extractedLocation
    //                 = $this->downloadLocation . '/' . strtolower($repoName) .
    //                 '-master/' . $type . '/';
    //         }

    //         return $this->localContent->listContents($extractedLocation, true);
    //     } else {

    //         return false;
    //     }
    // }
}