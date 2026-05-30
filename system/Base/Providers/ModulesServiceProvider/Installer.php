<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToListContents;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use System\Base\BasePackage;
use System\Base\Installer\Packages\Setup\Schema;
use System\Base\Providers\CoreServiceProvider\Install\Install as CoreInstall;
use xobotyi\rsync\Rsync;
use z4kn4fein\SemVer\Version;

class Installer extends BasePackage
{
    protected $queue;

    public static $trackCounter = 0;

    public $method;

    protected $apiClient;

    protected $apiClientConfig;

    protected $process;

    protected $downloadLocation = 'var/tmp/installer/';

    protected $zip;

    protected $zipFile = [];

    protected $modulesToProcess = [];

    protected $preCheckResult = [];

    protected $modulesToInstallOrUpdate;

    protected $runPrecheckProgressMethods;

    protected $runProcessProgressMethods;

    protected $updatedBy;

    protected $storesToIndex = [];

    protected $progressFileName = 'modulesinstaller';

    public function init($process = 'precheck')
    {
        $this->queue = $this->modules->queues->getActiveQueue();

        $this->process = $process;

        $this->zip = new \ZipArchive;

        try {
            if (!$this->localContent->directoryExists($this->downloadLocation)) {
                $this->localContent->createDirectory($this->downloadLocation);
            }
        } catch (FilesystemException | UnableToCheckExistence | \throwable $e) {
            throw $e;
        }

        $this->basepackages->progress->init(null, $this->progressFileName);

        if ($this->basepackages->progress->checkProgressFile()) {
            $this->basepackages->progress->deleteProgressFile(true);
        }

        if ($process === 'runprecheck') {
            $this->registerRunPrecheckProgressMethods();
        } else if ($process === 'runprocess') {
            $this->registerRunProcessProgressMethods();
        }

        if ($this->access->auth->account()) {//For Autoupdate
            $this->updatedBy = $this->access->auth->account()['id'];
        } else {
            $this->updatedBy = '0';
        }

        return $this;
    }

    protected function withProgress($method, $arguments)
    {
        $methodToCall = $method;

        if (str_contains($method, '-')) {
            $methodArr = explode('-', $method);
            $methodToCall = $methodArr[0];
        }

        if (method_exists($this, $methodToCall)) {
            $arguments['progressMethod'] = $method;

            $arguments = [$arguments];

            $this->basepackages->progress->updateProgress($method, null, false);

            $call = call_user_func_array([$this, $methodToCall], $arguments);

            $this->basepackages->progress->updateProgress($method, $call, false);

            return $call;
        }

        $this->basepackages->progress->resetProgress();

        return false;
    }

    public function runProcess(array $data)
    {
        if (!isset($this->queue['tasks']['analysed'])) {
            $this->addResponse('Queue needs to be analysed first!', 1);

            return false;
        }

        set_time_limit(600);//10 mins

        if ($this->process === 'runprecheck') {
            $this->basepackages->progress->preCheckComplete();

            $this->queue['results'] = arrayReplace($this->queue['results'], 'precheck_progress_logs', []);
            $this->queue['results'] = arrayReplace($this->queue['results'], 'precheck_logs', '-');
            $this->queue['results'] = arrayReplace($this->queue['results'], 'result_logs', '-');
            $this->modules->queues->update($this->queue);

            foreach ($this->runPrecheckProgressMethods as $method) {
                if ($this->withProgress($method['method'], $method['args'] ?? []) === false) {
                    if ($this->basepackages->progress->checkProgressFile()) {
                        $this->addProgressToResult(true);
                    }

                    $this->modules->queues->update($this->queue);

                    return false;
                }
            }

            $this->queue['status'] = 1;
            $this->queue['prechecked_at'] = date('c');
            $this->queue['prechecked_by'] = '-';

            if ($this->access->auth->account() && isset($this->access->auth->account()['id'])) {
                $this->queue['prechecked_by'] = $this->access->auth->account()['id'];
            }

            if ($this->basepackages->progress->checkProgressFile()) {
                $this->addProgressToResult();
            }

            $this->modules->queues->update($this->queue);

            $this->queue['prechecked_by'] = $this->access->auth->account()['email'];

            $this->addResponse('Precheck complete', 0, ['queue' => $this->queue]);
        } else if ($this->process === 'runprocess') {
            $this->basepackages->progress->preCheckComplete();

            $this->queue['results'] = arrayReplace($this->queue['results'], 'progress_progress_logs', []);
            $this->queue['results'] = arrayReplace($this->queue['results'], 'result_logs', '-');
            $this->modules->queues->update($this->queue);

            foreach ($this->runProcessProgressMethods as $method) {
                if ($this->withProgress($method['method'], $method['args'] ?? []) === false) {
                    if ($this->basepackages->progress->checkProgressFile()) {
                        $this->addProgressToResult(true);
                    }

                    $this->modules->queues->update($this->queue);

                    return false;
                }
            }

            $this->queue['status'] = 2;
            $this->queue['processed_at'] = date('c');
            $this->queue['processed_by'] = '-';

            if ($this->access->auth->account() && isset($this->access->auth->account()['id'])) {
                $this->queue['processed_by'] = $this->access->auth->account()['id'];
            }

            if ($this->basepackages->progress->checkProgressFile()) {
                $this->addProgressToResult();
            }

            $this->modules->queues->update($this->queue);

            $emailReport = false;
            if ($this->queue['settings']['emailReport'] !== '') {
                $emailReport = $this->emailReport();
            }

            $this->queue['processed_by'] = $this->access->auth->account()['email'];

            $this->addResponse('Process complete', 0, ['queue' => $this->queue, 'emailReport' => $emailReport]);
        }
    }

    protected function addProgressToResult($failed = false)
    {
        $progressFile = $this->basepackages->progress->getProgressFile();

        if (!$progressFile ||
            ($progressFile && !isset($progressFile['allProcesses']))
        ) {
            return;
        }

        if ($this->process === 'runprecheck') {
            foreach ($progressFile['allProcesses'] as $key => $progress) {
                if ($failed &&
                    (!isset($progress['callResult']) ||
                     isset($progress['callResult']) && $progress['callResult'] === false)
                ) {
                    return;
                }

                if (!isset($progress['args'])) {
                    continue;
                }

                if (!isset($this->queue['results'][$progress['args'][0]][$progress['args'][1]['module_type']][$progress['args'][1]['id']])) {
                    continue;
                }

                $progressResultResult = &$this->queue['results'][$progress['args'][0]][$progress['args'][1]['module_type']][$progress['args'][1]['id']];

                $progressResultResult['precheck_progress_logs'][$key]['progressTask'] = $progress['text'];
                $progressResultResult['precheck_progress_logs'][$key]['progressResult'] = $progress['callResult'] == true ? 'Pass' : 'Fail';
                $progressResultResult['precheck_progress_logs'][$key]['progressExecTime'] = $progress['callExecTime'];
            }
        } else if ($this->process === 'runprocess') {
            foreach ($progressFile['allProcesses'] as $key => $progress) {
                if ($failed &&
                    (!isset($progress['callResult']) ||
                     isset($progress['callResult']) && $progress['callResult'] === false)
                ) {
                    return;
                }

                if (!isset($progress['args'])) {
                    continue;
                }

                if (!isset($this->queue['results'][$progress['args'][0]][$progress['args'][1]['module_type']][$progress['args'][1]['id']])) {
                    continue;
                }

                $progressResultResult = &$this->queue['results'][$progress['args'][0]][$progress['args'][1]['module_type']][$progress['args'][1]['id']];

                $progressResultResult['process_progress_logs'][$key]['progressTask'] = $progress['text'];
                $progressResultResult['process_progress_logs'][$key]['progressResult'] = $progress['callResult'] == true ? 'Pass' : 'Fail';
                $progressResultResult['process_progress_logs'][$key]['progressExecTime'] = $progress['callExecTime'];
            }
        }
    }

    protected function precheckQueueData($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        if ($taskName === 'uninstall' || $taskName === 'remove') {
            $sync = false;
        } else {
            $sync = true;
        }

        $this->modulesToInstallOrUpdate = $this->modules->manager->getModuleInfo(
            [
                'module_type'   => $module['module_type'],
                'module_id'     => $module['id'],
                'sync'          => $sync
            ]
        );

        if ($this->modulesToInstallOrUpdate) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'pass';

            if ($sync) {
                if (is_string($this->modulesToInstallOrUpdate['repo_details'])) {
                    try {
                        $this->modulesToInstallOrUpdate['repo_details'] = $this->helper->decode($this->modulesToInstallOrUpdate['repo_details'], true);

                        return true;
                    } catch (\Exception $e) {
                        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                        $preCheckQueueLogs = &$this->queue['results']['first']['packages'][$module['id']]['precheck_logs'];

                        return $this->queueHasErrors(
                            $e->getMessage(),
                            $preCheckQueueLogs,
                            false
                        );
                    }
                } else {
                    return true;
                }
            } else {
                if (isset($this->modulesToInstallOrUpdate['required_by'])) {
                    $checkForCore = $this->preCheckModuleAppsBinding($taskName, $module, true);

                    if (!$checkForCore) {
                        return $checkForCore;
                    }
                }

                if ($this->queue['settings']['installer']['forceUninstall']) {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'] = 'Precheck ignored due to force uninstall.';

                    return true;
                }

                if (isset($this->modulesToInstallOrUpdate['apps']) ||
                    isset($this->modulesToInstallOrUpdate['required_by'])
                ) {
                    return $this->preCheckModuleAppsBinding($taskName, $module);
                }

                return true;
            }
        }

        if ($module['module_type'] === 'bundles') {
            $this->queue['results']['first']['packages'][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results']['first']['packages'][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'],
                $preCheckQueueLogs,
                true
            );
        } else if ($taskName === 'first' && strtolower($module['name']) === 'core') {
            $this->queue['results']['first']['packages'][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results']['first']['packages'][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'],
                $preCheckQueueLogs,
                true
            );
        } else {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'],
                $preCheckQueueLogs,
                true
            );
        }

        return false;
    }

    protected function preCheckModuleAppsBinding($taskName, $module, $checkForCore = false)
    {
        try {
            if (isset($this->modulesToInstallOrUpdate['apps'])) {
                if (is_string($this->modulesToInstallOrUpdate['apps'])) {
                    $this->modulesToInstallOrUpdate['apps'] = $this->helper->decode($this->modulesToInstallOrUpdate['apps'], true);
                }

                if (count($this->modulesToInstallOrUpdate['apps']) > 0) {
                    foreach ($this->modulesToInstallOrUpdate['apps'] as $appId => $appSettings) {
                        if (array_key_exists('enabled', $appSettings) && $appSettings['enabled'] == true) {
                            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                            $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                            return $this->queueHasErrors(
                                'Module ' . $this->modulesToInstallOrUpdate['name'] . ' (' . $this->modulesToInstallOrUpdate['module_type'] . ') is assigned to app with ID: ' . $appId . '. Either remove it via app or use force uninstall in the queue settings.',
                                $preCheckQueueLogs,
                                true
                            );
                        }
                    }
                }
            } else if (isset($this->modulesToInstallOrUpdate['required_by'])) {
                if (is_string($this->modulesToInstallOrUpdate['required_by'])) {
                    $this->modulesToInstallOrUpdate['required_by'] = $this->helper->decode($this->modulesToInstallOrUpdate['required_by'], true);
                }

                if (count($this->modulesToInstallOrUpdate['required_by']) > 0) {
                    foreach ($this->modulesToInstallOrUpdate['required_by'] as $moduleType => $requiredModulesArr) {
                        if (count($requiredModulesArr) > 0) {
                            foreach ($requiredModulesArr as $requiredModule) {
                                $moduleIsInstalled = $this->modules->$moduleType->getById($requiredModule);

                                if ($moduleIsInstalled &&
                                    $checkForCore &&
                                    $moduleIsInstalled['name'] === 'Core'
                                ) {
                                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                                    $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                                    return $this->queueHasErrors(
                                        'Module ' . $this->modulesToInstallOrUpdate['display_name'] . ' (' . $this->modulesToInstallOrUpdate['module_type'] . ') is required by ' . $moduleType . ' with ID: ' . $moduleIsInstalled['id'] . ' (' . $moduleIsInstalled['display_name'] . '). This module cannot be removed. Please remove the external module from the queue and contact developer if you want to get this external module removed.',
                                        $preCheckQueueLogs,
                                        true
                                    );
                                } else if ($moduleIsInstalled) {
                                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                                    $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                                    return $this->queueHasErrors(
                                        'Module ' . $this->modulesToInstallOrUpdate['display_name'] . ' (' . $this->modulesToInstallOrUpdate['module_type'] . ') is required by ' . $moduleType . ' with ID: ' . $moduleIsInstalled['id'] . ' (' . $moduleIsInstalled['display_name'] . '). Uninstall the module first or use force uninstall in the queue settings.',
                                        $preCheckQueueLogs,
                                        true
                                    );
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $e->getMessage(),
                $preCheckQueueLogs,
                true
            );
        }
    }

    protected function processExternalPackages($args)
    {
        $taskName = $args[0];
        $module = $args[1];
        $precheck = $args[2];
        $externalComposerFileName = str_replace('/', '_', $module['display_name']) . '_composer.json';

        if ($module['module_type'] === 'externals') {
            if ($precheck) {
                $this->queue['results'][$taskName]['externals'][$module['id']]['precheck'] = 'fail';
                $preCheckQueueLogs = &$this->queue['results'][$taskName]['externals'][$module['id']]['precheck_logs'];
            } else {
                $this->queue['results'][$taskName]['externals'][$module['id']]['result'] = 'fail';
                $resultQueueLogs = &$this->queue['results'][$taskName]['externals'][$module['id']]['result_logs'];
            }

            if ($taskName !== 'uninstall' && $taskName !== 'remove') {
                if (isset($module['hasPatch']) && $module['hasPatch'] === true) {
                    if (!isset($module['composerJsonFile']['extra']['patches'][$module['name']])) {
                        $this->cleanup(['composer']);

                        if ($precheck) {
                            return $this->queueHasErrors(
                                'External packages should have package defined, but are missing from the composer json file for : ' . $module['name'],
                                $preCheckQueueLogs
                            );
                        } else {
                            return $this->queueHasErrors(
                                'External packages should have package defined, but are missing from the composer json file for : ' . $module['name'],
                                $resultQueueLogs,
                                false
                            );
                        }
                    }

                    $patches = $this->basepackages->utils->scanDir($this->downloadLocation .
                                $module['root_module']['repo_details']['details']['name'] . '-' .
                                $module['root_module']['repo_details']['latestRelease']['name'] . '/' .
                                $module['root_module']['repo_details']['details']['name'] . '-' .
                                $module['root_module']['repo_details']['latestRelease']['name'] . '/external/patches/',
                               false
                    );

                    $patches['files'] = arrayFilterKeywords($patches['files'], ['.patch']);

                    if (count($patches['files']) === 0) {
                        $this->cleanup(['composer']);

                        if ($precheck) {
                            return $this->queueHasErrors(
                                'External package requires a patch which is missing from the repository : ' . $module['root_module']['repo'],
                                $preCheckQueueLogs
                            );
                        } else {
                            return $this->queueHasErrors(
                                'External package requires a patch which is missing from the repository : ' . $module['root_module']['repo'],
                                $resultQueueLogs,
                                false
                            );
                        }
                    }

                    $modulePatchName = str_replace(['/','-'], ['_','_'], $module['name']);
                    $patches['files'] = arrayFilterKeywords($patches['files'], [$modulePatchName]);

                    if (count($patches['files']) !== count($module['composerJsonFile']['extra']['patches'][$module['name']])) {
                        $this->cleanup(['composer']);

                        if ($precheck) {
                            return $this->queueHasErrors(
                                'External package number of patches do not match what is defined in the composer json file for repository : ' . $module['root_module']['repo'],
                                $preCheckQueueLogs
                            );
                        } else {
                            return $this->queueHasErrors(
                                'External package number of patches do not match what is defined in the composer json file for repository : ' . $module['root_module']['repo'],
                                $resultQueueLogs,
                                false
                            );
                        }
                    }

                    $foundAll = [];
                    foreach ($patches['files'] as $file) {
                        $fileName = $this->helper->last(explode('/', $file));
                        foreach ($module['composerJsonFile']['extra']['patches'][$module['name']] as $key => $patch) {
                            $foundAll[$key] = 'false';
                            if (str_contains($patch, $fileName)) {
                                $foundAll[$key] = 'true';
                            }
                        }
                    }

                    if (in_array('false', $foundAll)) {
                        $this->cleanup(['composer']);

                        if ($precheck) {
                            return $this->queueHasErrors(
                                'External package all patches not found in the external/patches directory as per the  composer json file for repository : ' . $module['root_module']['repo'],
                                $preCheckQueueLogs
                            );
                        } else {
                            return $this->queueHasErrors(
                                'External package all patches not found in the external/patches directory as per the  composer json file for repository : ' . $module['root_module']['repo'],
                                $resultQueueLogs,
                                false
                            );
                        }
                    }

                    try {
                        foreach ($patches['files'] as $file) {
                            $fileName = $this->helper->last(explode('/', $file));

                            $this->localContent->copy($file, 'external/patches/' . $fileName);
                        }
                    } catch (FilesystemException | UnableToCopyFile $e) {
                        $this->cleanup(['composer']);

                        return $this->queueHasErrors('Error copying file : ' . $fileName, $preCheckQueueLogs);
                    }
                }

                try {
                    $this->localContent->write('external/' . $externalComposerFileName, $this->helper->encode($module['composerJsonFile']));
                } catch (FilesystemException | UnableToWriteFile $e) {
                    $this->cleanup(['composer']);

                    if ($precheck) {
                        return $this->queueHasErrors(
                            'Error writing file external package composer file : ' . $externalComposerFileName,
                            $preCheckQueueLogs
                        );
                    } else {
                        return $this->queueHasErrors(
                            'Error writing file external package composer file : ' . $externalComposerFileName,
                            $resultQueueLogs,
                            false
                        );
                    }
                }
            }

            try {
                putenv('COMPOSER_HOME=' . base_path('external/'));
                if ($taskName !== 'uninstall' && $taskName !== 'remove') {
                    putenv('COMPOSER=' . $externalComposerFileName);
                }

                $stream = fopen(base_path('external/' . str_replace('_composer.json', '', $externalComposerFileName) . '.install'), 'w');

                $process = 'install';
                if ($taskName === 'uninstall' || $taskName === 'remove') {
                    $process = 'remove ' . $module['display_name'];
                }

                if ($precheck) {
                    $input = new \Symfony\Component\Console\Input\StringInput($process . ' --dry-run -d ' . base_path('external/'));
                } else {
                    $input = new \Symfony\Component\Console\Input\StringInput($process . ' -d ' . base_path('external/'));
                }

                $output = new \Symfony\Component\Console\Output\StreamOutput($stream);

                $application = new \Composer\Console\Application();
                $application->setAutoExit(false); // prevent `$application->run` method from exiting the script

                $app = $application->run($input, $output);

                $installLogs = $this->localContent->read('external/' . str_replace('_composer.json', '', $externalComposerFileName) . '.install');
            } catch (\throwable | UnableToReadFile | UnableToWriteFile $e) {
                $this->cleanup(['composer']);

                if ($precheck) {
                    return $this->queueHasErrors($e->getMessage(), $preCheckQueueLogs, $precheck);
                } else {
                    return $this->queueHasErrors($e->getMessage(), $resultQueueLogs, $precheck);
                }
            }

            if ($app !== 0) {
                $this->cleanup(['composer']);

                if ($precheck) {
                    return $this->queueHasErrors('Precheck for composer package failed : ' . $module['name'] . '<br>' . $installLogs, $preCheckQueueLogs);
                } else {
                    return $this->queueHasErrors('Precheck for composer package failed : ' . $module['name'] . '<br>' . $installLogs, $resultQueueLogs, false);
                }
            }

            if ($precheck) {
                $this->queue['results'][$taskName]['externals'][$module['id']]['precheck'] = 'pass';
                $this->queue['results'][$taskName]['externals'][$module['id']]['precheck_logs'] = $installLogs;
            } else {
                $this->queue['results'][$taskName]['externals'][$module['id']]['result'] = 'pass';
                $this->queue['results'][$taskName]['externals'][$module['id']]['result_logs'] = $installLogs;

                //Merge package information to composer.json
                if ($taskName !== 'uninstall' && $taskName !== 'remove') {
                    try {
                        $this->localContent->write('external/composer.json', $this->helper->encode(array_replace($this->getComposerJsonFile(), $module['composerJsonFile'])));
                    } catch (FilesystemException | UnableToWriteFile $e) {
                        return $this->queueHasErrors('Could not rewrite the composer.json file with new package information: ' . $module['name'], $resultQueueLogs, false);
                    }

                    //add to externals DB
                    try {
                        $installedComposerPackages = $this->getComposerPackageInfo();

                        $installedComposerPackages = $this->helper->decode($installedComposerPackages, true);
                    } catch (\throwable $e) {
                        return $this->queueHasErrors($e->getMessage(), $resultQueueLogs, false);
                    }

                    $externalPackageNameArr = explode('/', $module['name']);

                    $externalPackage = $this->modules->externals->getExternalByName($externalPackageNameArr[1]);

                    foreach ($installedComposerPackages['installed'] as $installedComposerPackagesKey => $installedComposerPackage) {
                        $installedComposerPackages[$installedComposerPackage['name']] = $installedComposerPackage;
                    }

                    unset($installedComposerPackages['installed']);

                    if ($externalPackage) {
                        $externalPackage['version'] =
                            (isset($installedComposerPackages[$module['name']]['version'])) ? $installedComposerPackages[$module['name']]['version'] : '';
                        $externalPackage['description'] =
                            (isset($installedComposerPackages[$module['name']]['description'])) ? $installedComposerPackages[$module['name']]['description'] : '';
                        $externalPackage['patches'] =
                            (isset($module['composerJsonFile']['extra']['patches'][$module['name']])) ?
                                $this->helper->encode($module['composerJsonFile']['extra']['patches'][$module['name']]) :
                                $this->helper->encode([]);
                        $externalPackage['abandoned'] =
                            (isset($installedComposerPackages[$module['name']]['abandoned'])) ? (($installedComposerPackages[$module['name']]['abandoned'] == true) ? 1 : 0) : 0;

                        if (isset($externalPackage['required_by'])) {
                            if (is_string($externalPackage['required_by'])) {
                                $externalPackage['required_by'] = $this->helper->decode($externalPackage['required_by'], true);
                            }

                            if (isset($externalPackage['required_by'][$module['root_module']['module_type']])) {
                                if (is_array($externalPackage['required_by'][$module['root_module']['module_type']]) &&
                                    count($externalPackage['required_by'][$module['root_module']['module_type']]) > 0
                                ) {
                                    if (!in_array($module['root_module']['id'], $externalPackage['required_by'][$module['root_module']['module_type']])) {
                                        array_push($externalPackage['required_by'][$module['root_module']['module_type']], $module['root_module']['id']);
                                    }
                                } else {
                                    $externalPackage['required_by'] = $this->helper->encode(['packages' => [$module['root_module']['id']]]);
                                }
                            } else {
                                $externalPackage['required_by'] = $this->helper->encode(['packages' => [$module['root_module']['id']]]);
                            }
                        } else {
                            $externalPackage['required_by'] = $this->helper->encode(['packages' => [$module['root_module']['id']]]);
                        }

                        $this->modules->externals->update($externalPackage);
                    } else {
                        $bindToModules = [];
                        if (isset($module['bind_to_modules']) && is_array($module['bind_to_modules']) && count($module['bind_to_modules']) > 0) {
                            foreach ($module['bind_to_modules'] as $bindToModule) {
                                if (!isset($bindToModules[$bindToModule['module_type']])) {
                                    $bindToModules[$bindToModule['module_type']] = [];
                                }

                                array_push($bindToModules[$bindToModule['module_type']], $bindToModule['id']);
                            }
                        }

                        $externalPackage =
                            [
                                'developer'             => $externalPackageNameArr[0],
                                'name'                  => $externalPackageNameArr[1],
                                'display_name'          => $module['name'],
                                'description'           =>
                                    (isset($installedComposerPackages[$module['name']]['description'])) ? $installedComposerPackages[$module['name']]['description'] : '',
                                'module_type'           => 'externals',
                                'app_type'              => 'core',
                                'version'               =>
                                    (isset($installedComposerPackages[$module['name']]['version'])) ? $installedComposerPackages[$module['name']]['version'] : '',
                                'patches'               =>
                                    (isset($module['composerJsonFile']['extra']['patches'][$module['name']])) ?
                                        $this->helper->encode($module['composerJsonFile']['extra']['patches'][$module['name']]) :
                                        $this->helper->encode([]),
                                'abandoned'             =>
                                    (isset($installedComposerPackages[$module['name']]['abandoned'])) ? (($installedComposerPackages[$module['name']]['abandoned'] == true) ? 1 : 0) : 0,
                                'installed'             => 1,
                                'required_by'           => $this->helper->encode($bindToModules),
                                'updated_by'            => 0
                            ];

                            if ($this->access->auth->account() && isset($this->access->auth->account()['id'])) {
                                $externalPackage['updated_by'] = $this->access->auth->account()['id'];
                            }

                        $this->modules->externals->add($externalPackage);
                    }
                }

                $this->cleanup(['composer']);
            }
        } else {
            $this->cleanup(['composer']);

            if ($precheck) {
                return $this->queueHasErrors('Incorrect external package type: ' . $module['name'], $preCheckQueueLogs);
            } else {
                return $this->queueHasErrors('Incorrect external package type: ' . $module['name'], $resultQueueLogs, false);
            }
        }

        return true;
    }

    protected function getComposerPackageInfo()
    {
        try {
            putenv('COMPOSER_HOME=' . base_path('external/'));

            $stream = fopen(base_path('external/composer.info'), 'w');
            $input = new \Symfony\Component\Console\Input\StringInput('show -f json -d ' . base_path('external/'));
            $output = new \Symfony\Component\Console\Output\StreamOutput($stream);

            $application = new \Composer\Console\Application();
            $application->setAutoExit(false); // prevent `$application->run` method from exiting the script

            $app = $application->run($input, $output);
        } catch (\throwable $e) {
            throw $e;
        }

        if ($app !== 0) {
            return false;
        }

        $file = '';

        $handle = fopen(base_path('external/composer.info'), "r");

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (!str_contains($line, '<warning>')) {
                    $file .= $line;
                }
            }

            fclose($handle);
        }

        return $file;
    }

    protected function queueHasErrors($errorMessage, &$queueLogs, $precheck = true)
    {
        $queueLogs = $errorMessage;

        if ($precheck) {
            $queueType = 'Precheck';
        } else {
            $queueType = 'Process';
        }

        $this->addResponse($queueType . ' has errors! Check logs.', 1, ['queue' => $this->queue]);

        $this->basepackages->progress->resetProgress();

        return false;
    }

    protected function downloadModulesFromRepo($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'pass';

        try {
            if ($this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] === '' &&
                $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name'] !== ''
            ) {
                $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] =
                    $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name'];
            }

            $this->zipFile['location'] = base_path($this->downloadLocation .
                            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/'
            );
            $this->zipFile['file'] = base_path($this->downloadLocation .
                            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/' .
                            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '.zip'
            );
            $this->zipFile['name'] = $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'];

            //Check if file was downloaded after release was published and also check if the zip file is readable
            $fileModificationTime = $this->localContent->lastModified($this->downloadLocation .
                        $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                        $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/' .
                        $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                        $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '.zip');

            if (\Carbon\Carbon::parse($this->modulesToInstallOrUpdate['repo_details']['latestRelease']['published_at'])->timestamp < $fileModificationTime &&
                $this->zip->open($this->zipFile['file']) === true
            ) {
                return true;
            }
        } catch (FilesystemException | UnableToRetrieveMetadata | \throwable $e) {
            //Important, do nothing. If the zip file is not there, it will throw an error because filesystem could not check the metadata.
        }

        // remove old data so there is no conflict
        $files =
            $this->basepackages->utils->scanDir(
                $this->downloadLocation .
                $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name']
            );

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

        $this->localContent->createDirectory(
            $this->downloadLocation .
            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name']
        );

        if ((isset($this->modulesToInstallOrUpdate['repo_details']['details']['internal']) &&
            $this->modulesToInstallOrUpdate['repo_details']['details']['internal'] == true) ||
            (isset($this->modulesToInstallOrUpdate['repo_details']['details']['private']) &&
            $this->modulesToInstallOrUpdate['repo_details']['details']['private'] == true)
        ) {
            if (!$this->initApi([
                    'api_id' => $this->modulesToInstallOrUpdate['api_id']
                    ],
                    base_path($this->downloadLocation .
                        $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                        $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/' .
                        $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                        $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '.zip'
                    ),
                    $args['progressMethod']
                )
            ) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                return $this->queueHasErrors(
                    'Not able to initialize API',
                    $preCheckQueueLogs,
                    true
                );
            }

            if ($this->apiClientConfig['id'] !== $this->modulesToInstallOrUpdate['api_id']) {
                $this->apiClientConfig = null;

                if (!$this->initApi([
                        'api_id' => $this->modulesToInstallOrUpdate['api_id']
                        ],
                        base_path($this->downloadLocation .
                            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/' .
                            $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                            $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '.zip'
                        ),
                        $args['progressMethod']
                    )
                ) {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                    $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                    return $this->queueHasErrors(
                        'Not able to initialize API',
                        $preCheckQueueLogs,
                        true
                    );
                }
            }

            $args =
                [
                    $this->apiClientConfig['org_user'],
                    $this->modulesToInstallOrUpdate['repo_details']['details']['name']
                ];
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'RepositoryApi';
                $method = 'repoGetArchive';
                $args = array_merge($args, [$this->modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name'] . '.zip']);
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'ReposApi';
                $method = 'reposDownloadZipballArchive';
                $args = array_merge($args, ['main']);
            }

            try {
                $this->apiClient->useMethod($collection, $method, $args)->getResponse();
                //As we have provided sink information, this should be downloaded and stored at the sink location.
            } catch (\throwable $e) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

                $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

                return $this->queueHasErrors(
                    $e->getMessage() . '. Try removing and readding the module from repo.',
                    $preCheckQueueLogs,
                    true
                );
            }
        } else {
            $this->method = $args['progressMethod'];

            if (isset($this->modulesToInstallOrUpdate['repo_details']['latestRelease']['zipball_url'])) {
                return $this->downloadData(
                    $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['zipball_url'],
                    base_path($this->downloadLocation .
                              $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                              $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '/' .
                              $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                              $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] . '.zip')
                );
            }
        }
    }

    protected function extractModulesDownloadedFromRepo()
    {
        $files = $this->basepackages->utils->scanDir($this->zipFile['location']);

        if (count($files['files']) > 0) {
            foreach ($files['files'] as $file) {
                if (str_contains($file, '.zip')) {
                    continue;
                }

                $this->localContent->delete($file);
            }
        }

        if (count($files['dirs']) > 0) {
            foreach ($files['dirs'] as $dir) {
                $this->localContent->deleteDirectory($dir);
            }
        }

        if ($this->zip->open($this->zipFile['file']) !== true) {
            $this->addResponse('Error reading downloaded zip file for module : ' . $this->zipFile['name'], 1);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        if (!$this->zip->extractTo($this->zipFile['location']) === true) {
            $this->addResponse('Error unzipping downloaded file for module : ' . $this->zipFile['name'], 1);

            $this->basepackages->progress->resetProgress();

            return false;
        }

        $files = $this->basepackages->utils->scanDir($this->zipFile['location'], false);

        if ($files && isset($files['dirs'][0])) {
            try {
                $name = str_replace('.zip', '', $this->zipFile['name']);

                $this->localContent->move($files['dirs'][0], $this->downloadLocation . $name . '/' . $name);

                $this->zip->close();
            } catch (FilesystemException | UnableToMoveFile $e) {
                $this->addResponse('Error renaming extracted directory for : ' . $this->zipFile['name'], 1);

                $this->basepackages->progress->resetProgress();

                return false;
            }

            return true;
        }

        return false;
    }

    protected function runRsync($args)
    {
        $taskName = $args[0];
        $module = $args[1];
        $precheck = $args[2];

        if ($precheck) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';
            $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];
        } else {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';
            $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];
        }

        if ($module['module_type'] === 'packages' &&
            $module['name'] === 'Core'
        ) {
            $includeFiles =
                [
                    'public',
                    'public/index.php',
                    'public/.htaccess_example',
                    'public/core/***',
                    'external',
                    'external/patches',
                    'external/patches/***',
                    'apps',
                    'apps/Core/***',
                    'system',
                    'system/Base/***',
                    'system/Cli/***',
                    'system/Bootstrap.php'
                ];
        } else {
            $includeFiles =
                [
                    '*'
                ];
        }

        $this->modulesToInstallOrUpdate = $this->modules->manager->getModuleInfo(
            [
                'module_type'   => $module['module_type'],
                'module_id'     => $module['id']
            ]
        );

        $this->zipFile['name'] = $this->modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                                 ($this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] !== '' ? $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] : $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name']);

        try {
            if ($module['module_type'] === 'packages' &&
                $module['name'] === 'Core'
            ) {
                $destDir = '';
            } else {
                if ($module['module_type'] === 'apptype') {
                    $destDir = 'apps/' . ucfirst($module['name']) . '/';
                } else if ($module['module_type'] === 'components') {
                    $destDir = 'apps/' . ucfirst($module['app_type']) . '/Components/';

                    $routeArr = explode('/', $module['route']);

                    foreach ($routeArr as &$path) {
                        $path = ucfirst($path);
                    }

                    $destDir .= implode('/', $routeArr) . '/';
                } else if ($module['module_type'] === 'middlewares') {
                    $destDir = 'apps/' . ucfirst($module['app_type']) . '/Middlewares/' . ucfirst($module['name']) . '/';
                } else if ($module['module_type'] === 'packages') {
                    $destDir = 'apps/' . ucfirst($module['app_type']) . '/Packages/';

                    $pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['name']), -1, PREG_SPLIT_NO_EMPTY);

                    $destDir .= implode('/', $pathArr) . '/';
                } else if ($module['module_type'] === 'views') {
                    //for view check if the main view is installed before adding to queue.
                    if (isset($module['is_public']) &&
                        $module['is_public'] == true
                    ) {
                        $destDir = 'public/' . strtolower($module['app_type']) . '/' . strtolower($module['name']) . '/';

                        $this->zipFile['name'] = $this->modulesToInstallOrUpdate['repo_details']['details']['name'] .
                                                 '-public-' .
                                                 $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'];
                    } else if (isset($module['is_subview']) &&
                        $module['is_subview'] == true
                    ) {
                        $destDir = 'apps/' . ucfirst($module['app_type']) . '/' . ucfirst($module['module_type']) . '/' . ucfirst($module['base_view_name']) . '/html/';

                        $pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['name']), -1, PREG_SPLIT_NO_EMPTY);

                        if (count($pathArr) > 1) {
                            foreach ($pathArr as &$path) {
                                $path = strtolower($path);
                            }
                        } else {
                            $pathArr[0] = strtolower($pathArr[0]);
                        }

                        $destDir .= implode('/', $pathArr) . '/';
                    } else {
                        $destDir = 'apps/' . ucfirst($module['app_type']) . '/Views/' . ucfirst($module['name']) . '/';
                    }
                }

                if (!$precheck) {
                    try {
                        if (!$this->localContent->directoryExists($destDir)) {
                            $this->localContent->createDirectory($destDir);
                        }
                    } catch (FilesystemException | UnableToCheckExistence | UnableToCreateDirectory | \throwable $e) {
                        throw $e;
                    }
                }
            }

            $srcDir = 'var/tmp/installer/' . $this->zipFile['name'] . '/' . $this->zipFile['name'];

            $rsyncSettings =
                [
                    Rsync::CONF_CWD        => base_path($srcDir),
                    Rsync::CONF_OPTIONS    =>
                        [
                            Rsync::OPT_DRY_RUN           => $precheck,
                            Rsync::OPT_VERBOSE           => true,
                            Rsync::OPT_ARCHIVE           => true,
                            Rsync::OPT_HUMAN_READABLE    => true,
                            Rsync::OPT_CHECKSUM          => true,
                            Rsync::OPT_INCLUDE           => $includeFiles,
                            Rsync::OPT_EXCLUDE           => ['*']
                        ]
                ];

            if ($this->queue['settings']['files']['deleteDestinationFiles']) {
                $rsyncSettings[Rsync::CONF_OPTIONS][Rsync::OPT_DELETE_AFTER] = true;
            }

            $rsync = new Rsync($rsyncSettings);

            $rsync->sync('.', base_path($destDir));

            if ($rsync->getExitCode() == 0) {
                $outputArr = explode(PHP_EOL, $rsync->getStdout());
                $modifiedFiles = [];
                $deleteFiles = [];

                if ($module['module_type'] === 'packages' &&
                    $module['name'] === 'Core'
                ) {
                    array_walk($outputArr, function($output) use (&$modifiedFiles, &$deleteFiles) {
                        if (str_starts_with($output, 'apps') ||
                            str_starts_with($output, 'system') ||
                            str_starts_with($output, 'public') ||
                            str_starts_with($output, 'external')
                        ) {
                            if (!str_ends_with($output, '/') &&
                                !str_ends_with($output, '.git')
                            ) {
                                array_push($modifiedFiles, $output);
                            }
                        }

                        if ($this->queue['settings']['files']['deleteDestinationFiles']) {
                            if (str_starts_with($output, 'deleting')) {
                                if (!str_ends_with($output, '/') &&
                                    !str_ends_with($output, '.git') &&
                                    !str_ends_with($output, 'keys')
                                ) {
                                    $output = str_replace('deleting ', '', $output);

                                    array_push($deleteFiles, $output);
                                }
                            }
                        }
                    });
                } else {
                    array_walk($outputArr, function($output) use (&$modifiedFiles, &$deleteFiles) {
                        if (!str_starts_with($output, 'sending') &&
                            !str_starts_with($output, 'created') &&
                            !str_ends_with($output, '/') &&
                            !str_starts_with($output, 'sent ') &&
                            !str_starts_with($output, 'total ') &&
                            $output !== ''
                        ) {
                            array_push($modifiedFiles, $output);
                        }

                        if ($this->queue['settings']['files']['deleteDestinationFiles']) {
                            if (str_starts_with($output, 'deleting')) {
                                $output = str_replace('deleting ', '', $output);

                                array_push($deleteFiles, $output);
                            }
                        }
                    });
                }

                $rsyncResults = [];
                if (count($deleteFiles) > 0) {
                    $rsyncResults['deleteFiles'] = $deleteFiles;
                }

                if ($precheck) {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'pass';

                    if (count($modifiedFiles) > 0) {
                        $rsyncResults['modifyFiles'] = $modifiedFiles;
                    }

                    if (count($rsyncResults) === 0) {
                        $preCheckQueueLogs = '-';
                    } else {
                        $preCheckQueueLogs = $this->helper->encode($rsyncResults);
                    }
                } else {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';

                    if (count($modifiedFiles) > 0) {
                        $rsyncResults['modifiedFiles'] = $modifiedFiles;
                    }

                    if (count($rsyncResults) === 0) {
                        $resultQueueLogs = '-';
                    } else {
                        $resultQueueLogs = $this->helper->encode($rsyncResults);
                    }
                }

                return true;
            }
        } catch (\throwable $e) {
            if (str_contains($e->getMessage(), 'No such file or directory')) {
                $rsyncError = 'Incorrect directory : ' . $rsync->getCWD();
            } else {
                $rsyncError = $e->getMessage();
            }
        }

        if (!isset($rsyncError)) {
            $rsyncError = $rsync->getStderr();
        }

        if ($precheck) {
            return $this->queueHasErrors(
                $rsyncError,
                $preCheckQueueLogs
            );
        } else {
            return $this->queueHasErrors(
                $rsyncError,
                $resultQueueLogs,
                false
            );
        }
    }

    protected function runModuleInstallScripts($args)
    {
        $taskName = $args[0];
        $module = $args[1];
        $uninstall = false;
        if (isset($args[2])) {
            $uninstall = $args[2];
        }

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($module['name'] === 'Core') {
            if ($uninstall) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    'Core cannot be uninstalled!',
                    $resultQueueLogs,
                    false
                );
            }

            try {
                (new CoreInstall)->init()->install();
            } catch (\throwable $e) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    $e->getMessage(),
                    $resultQueueLogs,
                    false
                );
            }
        } else {
            try {
                $this->modulesToInstallOrUpdate = $this->modules->manager->getModuleInfo(
                    [
                        'module_type'   => $module['module_type'],
                        'module_id'     => $module['id']
                    ]
                );

                $classArr = explode('\\', $this->modulesToInstallOrUpdate['class']);

                $classArr = array_slice($classArr, 0, -1);

                $class = implode('\\', $classArr) . '\\Install\\Install';

                $path = lcfirst(str_replace('\\', '/', $class) . '.php');

                try {
                    if ($this->localContent->fileExists($path)) {
                        if ($uninstall) {
                            $class = new $class;

                            if (method_exists($class, 'uninstall')) {
                                $class->init()->uninstall();
                            }
                        } else {
                            (new $class)->init()->install();
                        }
                    }
                } catch (FilesystemException | UnableToCheckExistence | \throwable $e) {
                    return true;
                }
            } catch (\throwable $e) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    $e->getMessage(),
                    $resultQueueLogs,
                    false
                );
            }
        }

        return true;
    }

    protected function updateVersion($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($taskName === 'update' ||
            $module['name'] === 'Core'
        ) {
            $versionArr = explode(' -> ', $module['version']);

            if (count($versionArr) !== 2) {
                return $this->queueHasErrors(
                    'Incorrect number of versions for module. Please contact developer.',
                    $resultQueueLogs,
                    false
                );
            }
        }

        try {
            if (str_contains($module['module_type'], 'apptype')) {
                $moduleArr = $this->apps->types->getById((int) $module['id']);
            } else {
                // $moduleMethod = 'get' . ucfirst(substr($module['module_type'], 0, -1)) . 'ById';
                $moduleArr = $this->modules->{$module['module_type']}->getById((int) $module['id']);
            }

            if (!$moduleArr) {
                return $this->queueHasErrors(
                    'Module not found with the ID provided. Please contact developer.',
                    $resultQueueLogs,
                    false
                );
            }

            if ($taskName === 'update' &&
                $moduleArr['version'] !== $versionArr[0] &&
                $moduleArr['update_version'] !== $versionArr[1]
            ) {
                return $this->queueHasErrors(
                    'Module information received by installer is incorrect. Please contact developer.',
                    $resultQueueLogs,
                    false
                );
            }

            if ($taskName === 'update' ||
                $module['name'] === 'Core'
            ) {
                $moduleArr['version'] = $moduleArr['update_version'];
            }

            $moduleArr['installed'] = 1;
            $moduleArr['updated_on'] = date('c');
            $moduleArr['update_available'] = 0;
            $moduleArr['update_version'] = '';

            if ($this->access->auth->account() && isset($this->access->auth->account()['id'])) {
                $moduleArr['updated_by'] = $this->access->auth->account()['id'];
            }

            if (str_contains($module['module_type'], 'apptype')) {
                $this->apps->types->update($moduleArr);
            } else {
                if (isset($moduleArr['repo_details']['latestRelease']['moduleJson']['dependencies'])) {
                    $moduleArr['dependencies'] = $moduleArr['repo_details']['latestRelease']['moduleJson']['dependencies'];
                }

                $this->modules->{$module['module_type']}->update($moduleArr);
            }

            if ($module['module_type'] === 'packages' &&
                $module['name'] === 'Core'
            ) {
                $this->core->core['version'] = $moduleArr['version'];

                $this->core->update($this->core->core);
            }

            return true;
        } catch (\throwable $e) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

            return $this->queueHasErrors(
                $e->getMessage(),
                $resultQueueLogs,
                false
            );
        }
    }

    public function cleanup(array $what, $modulePath = null)
    {
        if (in_array('composer', $what)) {
            $files = $this->basepackages->utils->scanDir('external', false);

            foreach ($files['files'] as $file) {
                if ($file === 'external/composer.json' ||
                    $file === 'external/composer.install' ||
                    $file === 'external/composer.info' ||
                    $file === 'external/composer.lock'
                ) {
                    continue;
                }

                if (str_contains($file, '.json') ||
                    str_contains($file, '.install') ||
                    str_contains($file, '.info') ||
                    str_contains($file, '.lock')
                ) {
                    try {
                        $this->localContent->delete($file);
                    } catch (UnableToDeleteFile | FilesystemException | \throwable $e) {
                        throw $e;
                    }
                }
            }
        }

        if (in_array('downloads', $what)) {
            try {
                if ($this->localContent->directoryExists($this->downloadLocation)) {
                    $this->localContent->deleteDirectory($this->downloadLocation);
                }
            } catch (FilesystemException | UnableToCheckExistence | UnableToDeleteDirectory | \throwable $e) {
                throw $e;
            }
        }

        if (in_array('modulePath', $what) && $modulePath) {
            $files = $this->basepackages->utils->scanDir($modulePath);

            if (count($files['files']) > 0) {
                try {
                    foreach ($files['files'] as $file) {
                        $this->localContent->delete($file);
                    }
                } catch (FilesystemException | UnableToDeleteFile | \throwable $e) {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                    return $this->queueHasErrors(
                        $e->getMessage(),
                        $resultQueueLogs,
                        false
                    );
                }
            }

            if (count($files['dirs']) > 0) {
                try {
                    foreach ($files['dirs'] as $dir) {
                        $this->localContent->deleteDirectory($dir);
                    }

                    //cleanup path by checking if any of the path directory is empty. If empty, we delete the directory.
                    $pathArr = explode('/', $modulePath);

                    foreach ($pathArr as $path) {
                        $path = null;//We dont need path as we will be popping it in the end.

                        $checkPath = join('/', $pathArr);

                        $folders = $this->localContent->listContents($checkPath)->toArray();

                        if (count($folders) === 0) {
                            $this->localContent->deleteDirectory($checkPath);
                        } else if (count($folders) === 1) {
                            $filePath = $folders[0]->path();

                            if (str_contains($filePath, '.gitkeep')) {
                                $this->localContent->delete($filePath);
                                $this->localContent->deleteDirectory($checkPath);
                            }
                        }

                        array_pop($pathArr);
                    }
                } catch (FilesystemException | UnableToDeleteFile | UnableToDeleteDirectory | \throwable $e) {
                    $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                    return $this->queueHasErrors(
                        $e->getMessage(),
                        $resultQueueLogs,
                        false
                    );
                }
            } else {
                try {
                    $this->localContent->deleteDirectory($modulePath);
                } catch (FilesystemException | UnableToDeleteFile | UnableToDeleteDirectory | \throwable $e) {
                    $this->addResponse($e->getMessage(), 1);

                    return false;
                }
            }

            return true;
        }
    }

    protected function createBackup()
    {
        if ($this->queue['settings']['backupSettings']['notes'] === '') {
            $this->queue['settings']['backupSettings']['notes'] = 'Backup taken while processing module installer queue with ID: ' . $this->queue['id'];
        }

        $backupInit = $this->basepackages->backuprestore->init()->backup($this->queue['settings']['backupSettings'], true);

        if (!$backupInit) {
            $this->basepackages->progress->resetProgress();

            $this->addResponse('Error initializing backup! Contact developer', 1);

            return false;
        }

        $progress = $this->basepackages->progress->getProgress();

        if (is_string($progress)) {
            $progress = $this->helper->decode($progress, true);
        }

        if ($progress['runners']['running']['method'] === 'createBackup') {
            if (isset($progress['runners']['running']['childs']) &&
                is_array($progress['runners']['running']['childs']) &&
                count($progress['runners']['running']['childs']) > 0
            ) {
                foreach ($progress['runners']['running']['childs'] as $child) {
                    $method = $child['method'];

                    $this->basepackages->progress->updateProgress('createBackup', null, false, $method);

                    if ($method === 'finishBackup') {
                        $call = $this->basepackages->backuprestore->$method($this->queue['settings']['backupSettings']);
                    } else {
                        $call = $this->basepackages->backuprestore->$method();
                    }

                    if ($call === false) {
                        $this->basepackages->progress->resetProgress();

                        $this->addResponse($this->basepackages->backuprestore->packagesData->responseMessage, 1);

                        return false;
                    }

                    if ($call !== false) {
                        $call = true;
                    }

                    $this->basepackages->progress->updateProgress('createBackup', $call, false, $method);
                }
            }

            return true;
        }

        $this->basepackages->progress->resetProgress();

        $this->addResponse('Progress Error! Contact developer.', 1);

        return false;
    }

    protected function registerRunPrecheckProgressMethods()
    {
        $this->runPrecheckProgressMethods = [];

        if (isset($this->queue['tasks']['analysed']['first']) &&
            count($this->queue['tasks']['analysed']['first']) > 0
        ) {
            foreach ($this->queue['tasks']['analysed']['first'] as $moduleType => $modulesTypes) {
                if ((is_array($modulesTypes) && count($modulesTypes) === 0) ||
                    !is_array($modulesTypes)
                ) {
                    continue;
                }

                //External First
                foreach ($modulesTypes as $module) {
                    if ($moduleType === 'externals') {
                        if (isset($module['hasPatch']) && $module['hasPatch'] === true) {
                            $this->addProgressMethods($this->runPrecheckProgressMethods, $module, 'first', 'first_external', true);
                        }

                        array_push($this->runPrecheckProgressMethods,
                            [
                                'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Perform precheck for external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                'args'      => ['first', $module, true],
                            ]
                        );
                    } else {
                        continue;
                    }
                }
            }
        }

        foreach ($this->queue['tasks']['analysed'] as $taskName => $modulesTypes) {
            if (($taskName === 'first' || $taskName === 'install' || $taskName === 'update') &&
                count($modulesTypes) > 0
            ) {
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    //For Views
                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'externals') {
                            continue;
                        }

                        if ($module['module_type'] === 'views' && $module['is_subview'] == false) {
                            $this->addProgressMethods($this->runPrecheckProgressMethods, $module, $taskName, 'for_main_view', true);

                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'runRsync-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Running rsync --dry-run for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module, true],
                                ]
                            );
                        }
                    }

                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'externals') {
                            continue;
                        }

                        if ($module['module_type'] === 'views' && $module['is_subview'] == false) {
                            continue;
                        }

                        $this->addProgressMethods($this->runPrecheckProgressMethods, $module, $taskName, 'everything_else', true);

                        array_push($this->runPrecheckProgressMethods,
                            [
                                'method'    => 'runRsync-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Running rsync --dry-run for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                'args'      => [$taskName, $module, true],
                            ]
                        );
                    }
                }
            }
        }

        foreach ($this->queue['tasks']['analysed'] as $taskName => $modulesTypes) {
            if (($taskName === 'uninstall' || $taskName === 'remove') &&
                count($modulesTypes) > 0
            ) {
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    foreach ($modules as $module) {
                        array_push($this->runPrecheckProgressMethods,
                            [
                                'method'    => 'precheckQueueData-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Perform precheck for module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                'args'      => [$taskName, $module],
                            ]
                        );

                        if ($moduleType === 'externals') {
                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Perform precheck for external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                    'args'      => [$taskName, $module, true],
                                ]
                            );
                        }
                    }
                }
            }
        }

        $this->basepackages->progress->registerMethods($this->runPrecheckProgressMethods);
    }

    protected function addProgressMethods(&$methods, $module, $taskName, $forTask, $precheck)
    {
        if ($precheck) {
            if ($forTask === 'first_external') {
                $module = $module['root_module'];
            }

            array_push($methods,
                [
                    'method'    => 'precheckQueueData-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                    'text'      => 'Perform precheck for module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                    'args'      => [$taskName, $module],
                ]
            );
            array_push($methods,
                [
                    'method'    => 'downloadModulesFromRepo-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                    'text'      => 'Download module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') files from repository...',
                    'args'      => [$taskName, $module],
                    'remoteWeb' => true
                ]
            );
            array_push($methods,
                [
                    'method'    => 'extractModulesDownloadedFromRepo-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                    'text'      => 'Extracting downloaded module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                    'args'      => [$taskName, $module]
                ]
            );
        } else {
            array_push($methods,
                [
                    'method'    => 'runRsync-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                    'text'      => 'Running rsync for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                    'args'      => [$taskName, $module, $precheck],
                ]
            );
            if ($module['module_type'] !== 'views' &&
                $module['module_type'] !== 'apptype' &&
                $module['module_type'] !== 'externals'
            ) {
                array_push($methods,
                    [
                        'method'    => 'runModuleInstallScripts-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                        'text'      => 'Running module install scripts for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                        'args'      => [$taskName, $module],
                    ]
                );
            }
            if (!isset($module['is_public'])) {
                array_push($methods,
                    [
                        'method'    => 'updateVersion-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                        'text'      => 'Updating version for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                        'args'      => [$taskName, $module],
                    ]
                );
            }
            array_push($methods,
                [
                    'method'    => 'deleteSourceFiles-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                    'text'      => 'Deleting Source files for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                    'args'      => [$taskName, $module],
                ]
            );
        }
    }

    protected function registerRunProcessProgressMethods()
    {
        $this->runProcessProgressMethods = [];

        if ((bool) $this->queue['settings']['backupSettings']['backup'] === true) {
            array_push($this->runProcessProgressMethods,
                [
                    'method'    => 'createBackup',
                    'text'      => 'Creating filesystem and database backup...',
                    'childs'    => $this->basepackages->backuprestore->getBackupProgressMethods()
                ]
            );
        }

        if (isset($this->queue['tasks']['analysed']['first']) &&
            count($this->queue['tasks']['analysed']['first']) > 0
        ) {
            foreach ($this->queue['tasks']['analysed']['first'] as $moduleType => $modulesTypes) {
                if ((is_array($modulesTypes) && count($modulesTypes) === 0) ||
                    !is_array($modulesTypes)
                ) {
                    continue;
                }

                //First we process external or first (core or other dependencies)
                foreach ($modulesTypes as $module) {
                    if ($moduleType === 'externals') {
                        array_push($this->runProcessProgressMethods,
                            [
                                'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Process external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                'args'      => ['first', $module, false],
                            ]
                        );
                    } else {
                        continue;
                    }
                }
            }
        }

        foreach ($this->queue['tasks']['analysed'] as $taskName => $modulesTypes) {
            if (($taskName === 'first' || $taskName === 'install' || $taskName === 'update') &&
                count($modulesTypes) > 0
            ) {
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    //For Views
                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'externals') {
                            continue;
                        }

                        if ($module['module_type'] === 'views' && $module['is_subview'] == false) {
                            $this->addProgressMethods($this->runProcessProgressMethods, $module, $taskName, 'for_main_view', false);
                        }
                    }

                    //Then we process all other modules.
                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'externals') {
                            continue;
                        }

                        if ($module['module_type'] === 'views' && $module['is_subview'] == false) {
                            continue;
                        }

                        $this->addProgressMethods($this->runProcessProgressMethods, $module, $taskName, 'everything_else', false);
                    }
                }
            }
        }

        foreach ($this->queue['tasks']['analysed'] as $taskName => $modulesTypes) {
            if (($taskName === 'uninstall' || $taskName === 'remove') &&
                count($modulesTypes) > 0
            ) {
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    foreach ($modules as $module) {
                        array_push($this->runProcessProgressMethods,
                            [
                                'method'    => 'uninstallModule-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Uninstalling module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                'args'      => [$taskName, $module],
                            ]
                        );

                        if ($module['module_type'] !== 'views' &&
                            $module['module_type'] !== 'apptype' &&
                            $module['module_type'] !== 'externals'
                        ) {
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'runModuleInstallScripts-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Running module install scripts for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module, true],
                                ]
                            );
                        }

                        if ($moduleType === 'externals') {
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Process external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                    'args'      => [$taskName, $module, false],
                                ]
                            );
                        }
                    }
                }
            }
        }

        foreach ($this->queue['tasks']['analysed'] as $taskName => $modulesTypes) {
            if ($taskName === 'remove' && count($modulesTypes) > 0) {
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    if ($module['module_type'] === 'views') {
                        continue;
                    }

                    foreach ($modules as $module) {
                        array_push($this->runProcessProgressMethods,
                            [
                                'method'    => 'removeModule-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                'text'      => 'Removing module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                'args'      => [$taskName, $module],
                            ]
                        );
                    }
                }

                //Subview
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    if ($module['module_type'] !== 'views') {
                        continue;
                    }

                    if ($module['module_type'] === 'views') {
                        if ($module['is_subview'] == true) {
                            foreach ($modules as $module) {
                                array_push($this->runProcessProgressMethods,
                                    [
                                        'method'    => 'removeModule-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                        'text'      => 'Removing module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                        'args'      => [$taskName, $module],
                                    ]
                                );
                            }
                        } else {
                            continue;
                        }
                    }
                }

                //Main View
                foreach ($modulesTypes as $moduleType => $modules) {
                    if ((is_array($modules) && count($modules) === 0) ||
                        !is_array($modules)
                    ) {
                        continue;
                    }

                    if ($module['module_type'] !== 'views') {
                        continue;
                    }

                    if ($module['module_type'] === 'views') {
                        if ($module['is_subview'] != true) {
                            foreach ($modules as $module) {
                                array_push($this->runProcessProgressMethods,
                                    [
                                        'method'    => 'removeModule-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                        'text'      => 'Removing module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                        'args'      => [$taskName, $module],
                                    ]
                                );
                            }
                        } else {
                            continue;
                        }
                    }
                }
            }
        }

        $this->basepackages->progress->registerMethods($this->runProcessProgressMethods);
    }

    protected function uninstallModule($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($taskName === 'uninstall' && $module['name'] === 'Core') {
            return $this->queueHasErrors(
                'Core cannot be uninstalled!',
                $resultQueueLogs,
                false
            );
        }

        try {
            if (str_contains($module['module_type'], 'apptype')) {
                $moduleArr = $this->apps->types->getById((int) $module['id']);
            } else {
                // $moduleMethod = 'get' . ucfirst(substr($module['module_type'], 0, -1)) . 'ById';
                $moduleArr = $this->modules->{$module['module_type']}->getById((int) $module['id']);
            }

            if (!$moduleArr) {
                return $this->queueHasErrors(
                    'Module not found with the ID provided. Please contact developer.',
                    $resultQueueLogs,
                    false
                );
            }

            $moduleArr['installed'] = 0;

            if (str_contains($module['module_type'], 'apptype')) {
                $this->apps->types->update($moduleArr);
            } else {
                $this->modules->{$module['module_type']}->update($moduleArr);
            }

            return true;
        } catch (\throwable $e) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

            return $this->queueHasErrors(
                $e->getMessage(),
                $resultQueueLogs,
                false
            );
        }
    }

    protected function removeModule($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($module['name'] === 'Core') {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

            return $this->queueHasErrors(
                'Core cannot be removed!',
                $resultQueueLogs,
                false
            );
        } else {
            try {
                $moduleToRemove = $this->modules->manager->getModuleInfo(
                    [
                        'module_type'   => $module['module_type'],
                        'module_id'     => $module['id']
                    ]
                );

                if (!$moduleToRemove) {
                    return true;
                }

                if ($module['module_type'] !== 'bundles') {
                    if ($module['module_type'] !== 'views' &&
                        $module['module_type'] !== 'apptype' &&
                        $module['module_type'] !== 'externals'
                    ) {
                        $classArr = explode('\\', $moduleToRemove['class']);

                        $classArr = array_slice($classArr, 0, -1);

                        $class = implode('\\', $classArr) . '\\Install\\Install';

                        $path = lcfirst(str_replace('\\', '/', $class) . '.php');

                        try {
                            if ($this->localContent->fileExists($path)) {
                                $class = new $class;

                                if (method_exists($class, 'uninstall')) {
                                    $class->init()->uninstall(true);
                                }
                            }
                        } catch (FilesystemException | UnableToCheckExistence | \throwable $e) {
                            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                            return $this->queueHasErrors(
                                $e->getMessage(),
                                $resultQueueLogs,
                                false
                            );
                        }
                    }

                    if ($module['module_type'] === 'externals') {
                        //Execute Composer
                        //
                    } else {
                        $cleanup = $this->cleanup(['modulePath'], $this->getModuleFilesLocation($moduleToRemove));

                        if ($cleanup !== true) {
                            return $cleanup;
                        }

                        if ($module['module_type'] === 'views' &&
                            $moduleToRemove['is_subview'] == false
                        ) {
                            $cleanup = $this->cleanup(['modulePath'], $this->getModuleFilesLocation($moduleToRemove, true));

                            if ($cleanup !== true) {
                                return $cleanup;
                            }
                        }
                    }
                }

                //Remove the module
                if ($module['module_type'] === 'apptype') {
                    $bundles = $this->modules->bundles->getBundlesForAppType(strtolower($module['app_type']));

                    if ($bundles && count($bundles) > 0) {
                        foreach ($bundles as $bundle) {
                            $this->modules->bundles->remove($bundle['id']);
                        }
                    }

                    $this->apps->types->remove($module['id']);
                } else {
                    $this->modules->{$module['module_type']}->remove($module['id']);
                }
            } catch (\throwable $e) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    $e->getMessage(),
                    $resultQueueLogs,
                    false
                );
            }
        }

        return true;
    }

    protected function getModuleFilesLocation($module, $viewPublic = false)
    {
        if (!isset($module['module_type']) &&
            ($module['app_type'] === strtolower($module['name']))
        ) {
            return 'apps/' . ucfirst($module['app_type']) . '/';
        } else if ($module['module_type'] === 'components') {
            $moduleLocation = 'apps/' . ucfirst($module['app_type']) . '/Components/';

            $routeArr = explode('/', $module['route']);

            foreach ($routeArr as &$path) {
                $path = ucfirst($path);
            }

            $routePath = implode('/', $routeArr) . '/';
        } else if ($module['module_type'] === 'packages') {
            $moduleLocation = 'apps/' . ucfirst($module['app_type']) . '/Packages/';

            $pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['name']), -1, PREG_SPLIT_NO_EMPTY);

            $routePath = implode('/', $pathArr) . '/';
        } else if ($module['module_type'] === 'middlewares') {
            $moduleLocation = 'apps/' . ucfirst($module['app_type']) . '/Middlewares/';

            $routePath = $module['name'] . '/';
        } else if ($module['module_type'] === 'views') {
            $moduleLocation = 'apps/' . ucfirst($module['app_type']) . '/Views/';

            if ($viewPublic) {
                $moduleLocation = 'public/' . $module['app_type'] . '/' . strtolower($module['name']) . '/';

                return $moduleLocation;
            }

            if ($module['is_subview'] == 0) {
                $routePath = $module['name'] . '/';
            } else {
                if (is_string($module['dependencies'])) {
                    $module['dependencies'] = $this->helper->decode($module['dependencies'], true);
                }

                if (!isset($module['dependencies']['views']) ||
                    (isset($module['dependencies']['views']) && count($module['dependencies']['views']) === 0)
                ) {
                    throw new \Exception('Base view dependencies for sub view missing in module dependencies.');
                }

                foreach ($module['dependencies']['views'] as $view) {
                    $view = $this->modules->views->getViewByRepo($view['repo']);

                    if ($view && $view['is_subview'] == false) {
                        $baseView = $view;

                        break;
                    }
                }

                if (!isset($baseView)) {
                    throw new \Exception('Base view dependencies for sub view not found on the system.');
                }

                $pathArr = preg_split('/(?=[A-Z])/', ucfirst($module['name']), -1, PREG_SPLIT_NO_EMPTY);

                if (count($pathArr) > 1) {
                    foreach ($pathArr as &$path) {
                        $path = strtolower($path);
                    }
                } else {
                    $pathArr[0] = strtolower($pathArr[0]);
                }

                $module['route'] = implode('/', $pathArr);

                $routePath = $baseView['name'] . '/html/' . $module['route'] . '/';
            }
        }

        return $moduleLocation . $routePath;
    }

    protected function initApi($data, $sink = null, $method = null)
    {
        if ($this->apiClient && $this->apiClientConfig) {
            return true;
        }

        if (!isset($data['api_id'])) {
            $this->addResponse('API information not provided', 1, []);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] == '0') {
            $this->addResponse('This is local module and not remote module, cannot sync.', 1, []);

            return false;
        }

        if ($sink & $method) {
            $this->apiClient = $this->basepackages->apiClientServices->setHttpOptions(['timeout' => 3600])->setMonitorProgress($sink, $method)->useApi($data['api_id']);
        } else {
            $this->apiClient = $this->basepackages->apiClientServices->useApi($data['api_id']);
        }

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

    protected function downloadData($url, $sink)
    {
        $download = $this->remoteWebContent->request(
            'GET',
            $url,
            $this->getHttpOptions($sink)
        );

        if ($download->getStatusCode() === 200) {
            return true;
        }

        return false;
    }

    protected function getHttpOptions($sink)//Public because remoteWebContent needs to access it
    {
        self::$trackCounter = 0;

        return [
            'progress' => function(
                $downloadTotal,
                $downloadedBytes,
                $uploadTotal,
                $uploadedBytes
            ) {
                if ($downloadTotal === 0 && $uploadTotal === 0) {
                    return;
                }

                $counters =
                        [
                            'downloadTotal'     => $downloadTotal,
                            'downloadedBytes'   => $downloadedBytes,
                            'uploadTotal'       => $uploadTotal,
                            'uploadedBytes'     => $uploadedBytes
                        ];

                if ($downloadedBytes === 0) {
                    return;
                }

                //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                //So this way, we only update progress when there is actually an update.
                if ($downloadedBytes === \System\Base\Providers\ModulesServiceProvider\Installer::$trackCounter) {
                    return;
                }

                \System\Base\Providers\ModulesServiceProvider\Installer::$trackCounter = $downloadedBytes;

                $downloadComplete = null;
                if ($downloadedBytes === $downloadTotal) {
                    $downloadComplete = true;
                }
                $this->basepackages->progress->updateProgress($this->method, $downloadComplete, false, null, $counters);
            },
            'verify'            => false,
            'connect_timeout'   => 60,
            'sink'              => $sink
        ];
    }

    protected function getComposerJsonFile()
    {
        // if (file_exists(base_path('external/composer.lock'))) {
        //     unlink(base_path('external/composer.lock'));
        // }

        try {
            return $this->helper->decode($this->localContent->read('external/composer.json'), true);
        } catch (\throwable $exception) {
            return false;
        }
    }

    protected function deleteSourceFiles($args)
    {
        if ((bool) $this->queue['settings']['files']['deleteSourceFiles'] === false) {
            return true;
        }

        $taskName = $args[0];
        $module = $args[1];

        try {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';
            $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

            $modulesToInstallOrUpdate = $this->modules->manager->getModuleInfo(
                [
                    'module_type'   => $module['module_type'],
                    'module_id'     => $module['id']
                ]
            );

            $scanDirs = [
                'var/tmp/installer/' . $modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' .
                ($modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] !== '' ? $modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] : $modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name'])
            ];

            if (isset($module['is_public']) && $module['is_public'] == true) {
                array_push($scanDirs,
                    'var/tmp/installer/' . $modulesToInstallOrUpdate['repo_details']['details']['name'] . '-public-' .
                    ($modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] !== '' ? $modulesToInstallOrUpdate['repo_details']['latestRelease']['name'] : $modulesToInstallOrUpdate['repo_details']['latestRelease']['tag_name']));
            }

            foreach ($scanDirs as $scanDir) {
                $files = $this->basepackages->utils->scanDir($scanDir);

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

                $this->localContent->deleteDirectory($scanDir);
            }

            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        } catch (UnableToListContents | UnableToDeleteDirectory | UnableToDeleteFile | \throwable $e) {
            return $this->queueHasErrors(
                $e->getMessage(),
                $resultQueueLogs
            );
        }

        return true;
    }

    protected function emailReport()
    {
        //If Email is not configured, we cannot send report.
        if (!$this->basepackages->email->setup()) {
            return true;
        }

        $addresses = explode(',', $this->queue['settings']['emailReport']);

        return $this->addEmailToQueue($addresses);
    }

    protected function addEmailToQueue(array $addresses)
    {
        $emailData['app_id'] = $this->apps->getAppInfo()['id'];
        $emailData['domain_id'] = $this->domains->getDomain()['id'];
        $emailData['status'] = 1;
        $emailData['priority'] = 1;
        $emailData['confidential'] = 0;
        $emailData['to_addresses'] = $addresses;
        $emailData['subject'] = 'Queue Report for Queue ID: ' . $this->queue['id'];
        //Move this to template in future.
        $emailData['body'] = printArrayList($this->queue);

        return $this->basepackages->emailqueue->addQueue($emailData);
    }
}