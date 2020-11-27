<?php

namespace System\Base\Providers\ModulesServiceProvider;

use GuzzleHttp\Client;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Packages\Admin\Modules;
use System\Base\BasePackage;
use System\Base\Providers\CoreServiceProvider\Core;

class Installer extends BasePackage
{
    protected $applicationName = null;

    protected $postData;

    // protected $postDataArr;

    protected $downloadClient;

    protected $fileSystem;

    protected $zip;

    protected $backupLocation;

    protected $downloadLocation;

    protected $dependenciesToDownload = [];

    protected $modulesToInstall = [];

    public function runProcess($postData)
    {
        $this->postData = $postData;

        $this->downloadClient = new Client();

        $this->fileSystem = new Filesystem(new Local(base_path('/')));

        $this->zip = new \ZipArchive;

        if (!$this->fileSystem->has('.backups')) {
            $this->fileSystem->createDir('.backups');
        }

        if (!$this->fileSystem->has('.downloads')) {
            $this->fileSystem->createDir('.downloads');
        }

        if (!$this->{$this->postData['type']}->getById($this->postData['id'])) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = ucfirst($this->postData['type']) . ' id incorrect!';

            return $this->packagesData;
        }

        if ($this->postData['type'] === 'core') {

            $this->modulesToInstall[0] =
                $this->{$this->postData['type']}->getById($this->postData['id'])->getAllArr();

        } else {

            $this->modulesToInstall[0] =
                $this->{$this->postData['type']}->getById($this->postData['id'])->getAllArr();

            $this->modulesToInstall[0]['type'] = $this->postData['type'];

            if (is_object($this->checkDependencies())) {

                return $this->packagesData;
            }

            // Sort using type, so we first install application, then components and so on.
            $this->modulesToInstall =
                msort(array_merge($this->modulesToInstall, $this->dependenciesToDownload), 'type');
        }

        if (count($this->modulesToInstall) > 0) {

            // $this->createBackup();

            if ($this->postData['process'] === 'install') {
                $this->processInstall();
            } else if ($this->postData['process'] === 'update') {
                $this->processUpdate();
            }

            // $this->deleteDownloads();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Module & it\'s dependencies installed!';

            return $this->packagesData;
        }

        $this->packagesData->responseCode = 1;

        $this->packagesData->responseMessage = 'Nothing to do!';

        return $this->packagesData;
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
    protected function checkDependencies()
    {
        // $this->getLatestRepositoryModulesData();

        ${$this->postData['type']} =
            $this->{$this->postData['type']}->getById($this->postData['id'])->getAllArr();

        $dependencies =
            json_decode(${$this->postData['type']}['dependencies'], true);

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

    protected function checkRegisteredDependencies($dependencies)
    {
        $found = true;

        $this->dependenciesToDownload = [];

        foreach ($dependencies as $dependencyKey => $dependencyValue) {
            if ($dependencyKey === 'application') {
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

    protected function createBackup()
    {
        $this->backupLocation = '.backups/';

        $now = new \DateTime('now');

        $this->zip->open(base_path($this->backupLocation . '/' . $now->format('Y_m_d_H_i_s') . '.zip'), $this->zip::CREATE);

        $framework = [];
        $framework['dir'] = [];
        $framework['file'] = [];

        $rootContents = $this->fileSystem->listContents('/');

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
            $rootDirContents[$dirValue] = $this->fileSystem->listContents($dirValue, true);
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

    protected function downloadPackagesAndDependencies($module)
    {
        try {
            $downloadedContents = $this->downloadClient
                    ->request('GET', $module['repo'] . '/archive/master.zip')
                    ->getBody()
                    ->getContents();

        } catch (\Exception $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            return $this->packagesData;
        }

        $this->downloadLocation = '.downloads/';

        if (!$this->fileSystem->has($this->downloadLocation . ucfirst($module['type']))) {
            $this->fileSystem->createDir($this->downloadLocation . ucfirst($module['type']));
            $this->downloadLocation = $this->downloadLocation . ucfirst($module['type']);
        } else {
            $this->downloadLocation = $this->downloadLocation . ucfirst($module['type']);
        }

        if (!$this->fileSystem->has($this->downloadLocation . '/' . $module['name'])) {
            $this->fileSystem->createDir($this->downloadLocation . '/' . $module['name']);
            $this->downloadLocation = $this->downloadLocation . '/' . $module['name'];
        } else {
            $this->downloadLocation = $this->downloadLocation . '/' . $module['name'];
        }

        if (!$this->fileSystem->has($this->downloadLocation . '/' . $module['version'])) {
            $this->fileSystem->createDir($this->downloadLocation . '/' . $module['version']);
            $this->downloadLocation = $this->downloadLocation . '/' . $module['version'];
        } else {
            $this->downloadLocation = $this->downloadLocation . '/' . $module['version'];
        }

        // if (!$this->fileSystem->has($this->downloadLocation . '/' . $module['repobranch'])) {
        //  $this->fileSystem->createDir($this->downloadLocation . '/' . $module['repobranch']);
        //  $this->downloadLocation = $this->downloadLocation . '/' . $module['repobranch'];
        // } else {
        //  $this->downloadLocation = $this->downloadLocation . '/' . $module['repobranch'];
        // }

        // var_dump($this->downloadLocation);

        $this->fileSystem->put(
            $this->downloadLocation . '/master.zip',
            $downloadedContents
        );
    }

    protected function extractDownloadedPackagesAndDependencies($name, $repoName, $type)
    {
        if ($this->zip->open($this->downloadLocation . '/master.zip')) {
            $this->zip->extractTo($this->downloadLocation);

            if ($type === 'core') {
                $extractedLocation
                    = $this->downloadLocation . '/' . strtolower($repoName) .
                    '-master/';
            } else if ($type === 'application') {
                $extractedLocation
                    = $this->downloadLocation . '/' . strtolower($repoName) .
                    '-master/' . $type . 's/';
            } else {
                $extractedLocation
                    = $this->downloadLocation . '/' . strtolower($repoName) .
                    '-master/' . $type . '/';
            }

            return $this->fileSystem->listContents($extractedLocation, true);
        } else {

            return false;
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

                $this->fileSystem->createDir($destDir . '/' . $content['basename']);

                array_push($installedFiles['dir'], $destDir . '/' . $content['basename']);

            } else if ($content['type'] === 'file') {

                if ($content['basename'] !== '.gitkeep') {

                    $this->fileSystem->copy($content['path'], $destDir . '/' . $content['basename']);

                    array_push($installedFiles['files'], $destDir . '/' . $content['basename']);
                }

            }
        }

        return $installedFiles;
        // if ($type === 'components' || $type === 'packages' || $type === 'middlewares') {
        //  if ($type === 'components') {
        //      $this->fileSystem->put(
        //          $type . '/'. $this->applicationName . '/Install/' . $this->componentName . '/files.info', json_encode($installedFiles)
        //      );
        //  } else if ($type === 'packages') {
        //      $this->fileSystem->put(
        //          $type . '/'. $this->applicationName . '/Install/' . $this->packageName . '/files.info', json_encode($installedFiles)
        //      );
        //  } else if ($type === 'middlewares') {
        //      $this->fileSystem->put(
        //          $type . '/'. $this->applicationName . '/Install/' . $this->middlewareName . '/files.info', json_encode($installedFiles)
        //      );
        //  }
        // } else {
        //  $this->fileSystem->put($type . '/'. $this->applicationName . '/files.info', json_encode($installedFiles));
        // }
    }

    protected function deleteDownloads()
    {
        $downloadsToDelete = $this->fileSystem->listContents('.downloads', true);

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
                $this->fileSystem->delete($file);
            }
        }

        if (count($downloadedFiles['dir']) > 0) {
            foreach ($downloadedFiles['dir'] as $dirKey => $dir) {
                $this->fileSystem->deleteDir($dir);
            }
        }
    }

    protected function rollBack()
    {
        //
    }


    // protected function getPostDataArr()
    // {
    //  if ($this->postData['type'] === 'applications') {
    //      $this->applicationName = $this->postData['name'];
    //  }

    //  $this->postDataArr = [];

    //  if (isset($this->postData['dependencies'])) {
    //      foreach ($this->postData as $postDataKey => $postDataValue) {
    //          if ($postDataKey !== 'dependencies' || $postDataValue === '') {
    //              $this->postDataArr[0][$postDataKey] = $postDataValue;
    //          } else if ($postDataKey === 'dependencies' & $postDataValue !== '') {
    //              $dependenciesCount = 1;
    //              foreach ($postDataValue as $dependenciesKey => $dependenciesValue) {
    //                  $this->postDataArr[$dependenciesCount]['name'] = $dependenciesValue['name'];
    //                  $this->postDataArr[$dependenciesCount]['version'] = $dependenciesValue['version'];
    //                  $this->postDataArr[$dependenciesCount]['repo'] = $dependenciesValue['repo'];

    //                  $repo = explode('/', $this->postDataArr[$dependenciesCount]['repo']);
    //                  $this->postDataArr[$dependenciesCount]['modulereponame']
    //                      = end($repo);

    //                  if ($dependenciesKey !== 'core') {
    //                      $this->postDataArr[$dependenciesCount]['type'] = $dependenciesKey . 's';
    //                  } else {
    //                      $this->postDataArr[$dependenciesCount]['type'] = $dependenciesKey;
    //                  }

    //                  if ($dependenciesKey === 'application') {
    //                      $this->applicationName = $dependenciesValue['name'];
    //                  }

    //                  $dependenciesCount = $dependenciesCount + 1;
    //              }
    //          }
    //      }
    //  }

    //  $this->packagesData->responseCode = 0;
    // }

    // protected function checkDuplicateInstalledModules()
    // {
    //  $installedCore = $this->core->getCoreInfo();
    //  $installedApplications = $this->applications->getAllApplications();
    //  $installedComponents = $this->components->getAllComponents();
    //  $installedPackages = $this->packages->getAllPackages();
    //  $installedViews = $this->views->getAllViews();

    //  //$this->postDataArr[0]['type'] !== 'applications' = New application dont need to check components and views.
    //  foreach ($this->postDataArr as $postDataKey => $postDataValue) {
    //      if ($postDataValue['type'] === 'core') {
    //          foreach ($installedCore as $installedCoreKey => $core) {
    //              if ($core->get('name') === $postDataValue['name'] &&
    //                  $core->get('repo') === $postDataValue['repo']
    //                 ) {
    //                  if (!$this->moduleNeedsUpgrade($postDataValue, $core)) {
    //                      unset($this->postDataArr[$postDataKey]);
    //                  }
    //              }
    //          }
    //      } else if ($postDataValue['type'] === 'applications') {
    //          foreach ($installedApplications as $installedApplicationKey => $installedApplication) {
    //              if ($installedApplication->get('name') === $postDataValue['name'] &&
    //                  $installedApplication->get('repo') === $postDataValue['repo']
    //                 ) {
    //                  if (!$this->moduleNeedsUpgrade($postDataValue, $installedApplication)) {
    //                      unset($this->postDataArr[$postDataKey]);
    //                  }
    //              }
    //          }
    //      } else if ($this->postDataArr[0]['type'] !== 'applications' && $postDataValue['type'] === 'components') {
    //          foreach ($installedComponents as $installedComponentKey => $installedComponent) {
    //              if ($installedComponent->get('name') === $postDataValue['name'] &&
    //                  $installedComponent->get('repo') === $postDataValue['repo']
    //                 ) {
    //                  if (!$this->moduleNeedsUpgrade($postDataValue, $installedComponent)) {
    //                      unset($this->postDataArr[$postDataKey]);
    //                  }
    //              }
    //          }
    //      } else if ($postDataValue['type'] === 'packages') {
    //          foreach ($installedPackages as $installedPackageKey => $installedPackage) {
    //              if ($installedPackage->get('name') === $postDataValue['name'] &&
    //                  $installedPackage->get('repo') === $postDataValue['repo']
    //                 ) {
    //                  if (!$this->moduleNeedsUpgrade($postDataValue, $installedPackage)) {
    //                      unset($this->postDataArr[$postDataKey]);
    //                  }
    //              }
    //          }
    //      } else if ($this->postDataArr[0]['type'] !== 'applications' && $postDataValue['type'] === 'views') {
    //          foreach ($installedViews as $installedViewKey => $installedView) {
    //              if ($installedView->get('name') === $postDataValue['name'] &&
    //                  $installedView->get('repo') === $postDataValue['repo']
    //                 ) {
    //                  if (!$this->moduleNeedsUpgrade($postDataValue, $installedView)) {
    //                      unset($this->postDataArr[$postDataKey]);
    //                  }
    //              }
    //          }
    //      }
    //  }
    //  // var_dump($this->postDataArr);
    // }

    // protected function moduleNeedsUpgrade($newModule, $installedModule)
    // {
    //  if ($newModule['version'] !== $installedModule->getAllArr()['version']) {

    //      $newModuleVersion = explode('.', $newModule['version']);

    //      $installedModuleVersion = explode('.', $installedModule->getAllArr()['version']);

    //      if ($newModuleVersion[0] > $installedModuleVersion[0]) {
    //          return true;
    //      } else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
    //                 $newModuleVersion[1] > $installedModuleVersion[1]
    //                ) {
    //          return true;
    //      } else if ($newModuleVersion[0] === $installedModuleVersion[0] &&
    //                 $newModuleVersion[1] === $installedModuleVersion[1] &&
    //                 $newModuleVersion[2] > $installedModuleVersion[2]
    //              ) {
    //          return true;
    //      }
    //  } else {
    //      return false;
    //  }
    // }
    //

}