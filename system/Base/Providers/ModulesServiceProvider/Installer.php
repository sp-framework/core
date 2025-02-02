<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToListContents;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use System\Base\BasePackage;
use System\Base\Installer\Packages\Setup\Schema;
use System\Base\Providers\CoreServiceProvider\Install\Package as CorePackage;
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

        if (!$this->queue) {
            $this->addResponse('Not able to obtain queue', 1);

            return false;
        }

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
            $this->basepackages->progress->deleteProgressFile();
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

            foreach ($this->runPrecheckProgressMethods as $method) {
                if ($this->withProgress($method['method'], $method['args'] ?? []) === false) {
                    $this->modules->queues->update($this->queue);

                    return false;
                }
            }

            $this->queue['status'] = 1;

            $this->modules->queues->update($this->queue);

            $this->addResponse('Precheck complete', 0, ['queue' => $this->queue]);
        } else if ($this->process === 'runprocess') {
            $this->basepackages->progress->preCheckComplete();

            foreach ($this->runProcessProgressMethods as $method) {
                if ($this->withProgress($method['method'], $method['args'] ?? []) === false) {
                    $this->modules->queues->update($this->queue);

                    return false;
                }
            }

            $this->queue['status'] = 2;

            $this->modules->queues->update($this->queue);

            $emailReport = false;
            if ($this->queue['settings']['emailReport'] !== '') {
                $emailReport = $this->emailReport();
            }

            $this->addResponse('Process complete', 0, ['queue' => $this->queue, 'emailReport' => $emailReport]);
        }
    }

    protected function precheckQueueData($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->modulesToInstallOrUpdate = $this->modules->manager->getModuleInfo(
            [
                'module_type'   => $module['module_type'],
                'module_id'     => $module['id'],
                'sync'          => true
            ]
        );

        if ($this->modulesToInstallOrUpdate) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'pass';

            if (is_string($this->modulesToInstallOrUpdate['repo_details'])) {
                try {
                    $this->modulesToInstallOrUpdate['repo_details'] = $this->helper->decode($this->modulesToInstallOrUpdate['repo_details'], true);

                    return true;
                } catch (\Exception $e) {
                    //Do Nothings
                }
            } else {
                return true;
            }
        }

        if ($module['module_type'] === 'bundles') {
            $this->addResponse($this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'], 1);

            $this->basepackages->progress->resetProgress();

            return false;
        } else if ($taskName === 'first' && strtolower($module['name']) === 'core') {
            $this->queue['results']['first']['packages'][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results']['first']['packages'][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'],
                $preCheckQueueLogs
            );
        } else {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'fail';

            $preCheckQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck_logs'];

            return $this->queueHasErrors(
                $this->modules->manager->packagesData->responseMessage ?? 'Could not retrieve repository information for module: ' . $module['name'],
                $preCheckQueueLogs
            );
        }

        return false;
    }

    protected function processExternalPackages($args)
    {
        $module = $args[1];
        $precheck = $args[2];

        if ($module['module_type'] === 'external') {
            $this->queue['results']['first']['external'][explode('/', $module['name'])[1]]['precheck'] = 'fail';
            $preCheckQueueLogs = &$this->queue['results']['first']['external'][explode('/', $module['name'])[1]]['precheck_logs'];

            if (isset($module['hasPatch']) && $module['hasPatch'] === true) {
                if (!isset($module['composerJsonFile']['extra']['patches'][$module['name']])) {
                    $this->cleanup(['composer']);

                    return $this->queueHasErrors(
                        'External packages should have package defined, but are missing from the composer json file for : ' . $module['name'],
                        $preCheckQueueLogs
                    );
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

                    return $this->queueHasErrors(
                        'External package requires a patch which is missing from the repository : ' . $module['root_module']['repo'],
                        $preCheckQueueLogs
                    );
                }

                $modulePatchName = str_replace(['/','-'], ['_','_'], $module['name']);
                $patches['files'] = arrayFilterKeywords($patches['files'], [$modulePatchName]);

                if (count($patches['files']) !== count($module['composerJsonFile']['extra']['patches'][$module['name']])) {
                    $this->cleanup(['composer']);

                    return $this->queueHasErrors(
                        'External package number of patches do not match what is defined in the composer json file for repository : ' . $module['root_module']['repo'],
                        $preCheckQueueLogs
                    );
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

                    return $this->queueHasErrors(
                        'External package all patches not found in the external/patches directory as per the  composer json file for repository : ' . $module['root_module']['repo'],
                        $preCheckQueueLogs
                    );
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
                $externalComposerFileName = str_replace('/', '_', $module['name']) . '_composer.json';

                $this->localContent->write('external/' . $externalComposerFileName, $this->helper->encode($module['composerJsonFile']));
            } catch (FilesystemException | UnableToWriteFile $e) {
                $this->cleanup(['composer']);

                return $this->queueHasErrors(
                    'Error writing file external package composer file : ' . $externalComposerFileName,
                    $preCheckQueueLogs
                );
            }

            try {
                putenv('COMPOSER_HOME=' . base_path('external/'));
                putenv('COMPOSER=' . $externalComposerFileName);

                $stream = fopen(base_path('external/' . $externalComposerFileName . '.install'), 'w');
                $input = new \Symfony\Component\Console\Input\StringInput('install --dry-run -d ' . base_path('external/'));
                $output = new \Symfony\Component\Console\Output\StreamOutput($stream);

                $application = new \Composer\Console\Application();
                $application->setAutoExit(false); // prevent `$application->run` method from exiting the script

                $app = $application->run($input, $output);

                $precheckLog = $this->localContent->read('external/' . $externalComposerFileName . '.install');

                $this->queue['results']['first']['external'][explode('/', $module['name'])[1]]['precheck'] = 'pass';
                $this->queue['results']['first']['external'][explode('/', $module['name'])[1]]['precheck_logs'] = $precheckLog;
            } catch (\throwable | UnableToReadFile $e) {
                $this->cleanup(['composer']);

                return $this->queueHasErrors($e->getMessage(), $preCheckQueueLogs);
            }

            if ($app !== 0) {
                $this->cleanup(['composer']);

                return $this->queueHasErrors('Precheck for composer package failed : ' . $module['name'], $preCheckQueueLogs);
            }
        } else {
            $this->cleanup(['composer']);

            return $this->queueHasErrors('Incorrect external package type: ' . $module['name'], $preCheckQueueLogs);
        }

        return true;
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

    protected function downloadModulesFromRepo($data)
    {
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

        try {//Check if file was downloaded after release was published and also check if the zip file is readable
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
        } catch (FilesystemException | UnableToRetrieveMetadata | \Exception $e) {
            // Do Nothing.
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
                    $data['progressMethod']
                )
            ) {
                $this->basepackages->progress->resetProgress();

                return false;
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
                        $data['progressMethod']
                    )
                ) {
                    $this->basepackages->progress->resetProgress();

                    return false;
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
                $this->basepackages->progress->resetProgress();

                $this->addResponse($e->getMessage(), 1);

                return false;
            }
        } else {
            $this->method = $data['progressMethod'];

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
                        $this->modulesToInstallOrUpdate['repo_details']['latestRelease']['name'];

        try {
            $rsyncSettings =
                [
                    Rsync::CONF_CWD        => base_path('var/tmp/installer/' . $this->zipFile['name'] . '/' . $this->zipFile['name']),
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

            if ($module['module_type'] === 'packages' &&
                $module['name'] === 'Core'
            ) {
                $rsync->sync(
                    '.',
                    base_path('')
                );
            } else {
                $rsync->sync(
                    '.',
                    base_path('apps/' . ucfirst($module['app_type']) . '/' . ucfirst($module['module_type']) . '/' . ucfirst($module['name']) . '/')
                );
            }

            if ($rsync->getExitCode() == 0) {
                $outputArr = explode(PHP_EOL, $rsync->getStdout());
                $modifiedFiles = [];
                $deleteFiles = [];

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
            }

            if ($precheck) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['precheck'] = 'pass';
                if ($this->queue['settings']['files']['deleteDestinationFiles']) {
                    $preCheckQueueLogs = $this->helper->encode(['modifiedFiles' => $modifiedFiles, 'deleteFiles' => $deleteFiles]);
                } else {
                    $preCheckQueueLogs = $this->helper->encode(['modifiedFiles' => $modifiedFiles]);
                }
            } else {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
                if ($this->queue['settings']['files']['deleteDestinationFiles']) {
                    $resultQueueLogs = $this->helper->encode(['modifiedFiles' => $modifiedFiles, 'deleteFiles' => $deleteFiles]);
                } else {
                    $resultQueueLogs = $this->helper->encode(['modifiedFiles' => $modifiedFiles]);
                }
            }

            return true;
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

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($module['module_type'] === 'packages' &&
            $module['name'] === 'Core'
        ) {
            try {
                (new CorePackage)->install($this);
            } catch (\throwable $e) {
                trace([$e]);
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    $e->getMessage(),
                    $resultQueueLogs,
                    false
                );
            }
        } else {
            //
        }

        return true;
    }

    protected function updateVersion($args)
    {
        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        if ($module['module_type'] === 'packages' &&
            $module['name'] === 'Core'
        ) {
            try {
                $versionArr = explode(' -> ', $module['version']);

                if (count($versionArr) !== 2) {
                    return $this->queueHasErrors(
                        'Incorrect number of versions for Core. Please contact developer.',
                        $resultQueueLogs,
                        false
                    );
                }

                $package = $this->modules->packages->getPackageById($module['id']);

                if (!$package) {
                    return $this->queueHasErrors(
                        'Package core not found with the ID provided. Please contact developer.',
                        $resultQueueLogs,
                        false
                    );
                }

                if ($package['version'] !== $versionArr[0] &&
                    $package['update_version'] !== $versionArr[1]
                ) {
                    return $this->queueHasErrors(
                        'Module information received by installer is incorrect. Please contact developer.',
                        $resultQueueLogs,
                        false
                    );
                }

                $package['version'] = $package['update_version'];
                $package['updated_on'] = date('c');
                $package['update_available'] = 0;

                if ($this->access->auth->account() && isset($this->access->auth->account()['id'])) {
                    $package['updated_by'] = $this->access->auth->account()['id'];
                }

                $this->modules->packages->update($package);

                $this->core->core['version'] = $package['version'];

                $this->core->update($this->core->core);
            } catch (\throwable $e) {
                $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                return $this->queueHasErrors(
                    $e->getMessage(),
                    $resultQueueLogs,
                    false
                );
            }
        } else {
            //
        }

        return true;
    }

    public function cleanup(array $what)
    {
        if (in_array('composer', $what)) {
            $files = $this->basepackages->utils->scanDir('external', false);

            foreach ($files['files'] as $file) {
                if ($file === 'external/composer.json' || $file === 'external/composer.install') {
                    continue;
                }

                if (str_contains($file, '.json') || str_contains($file, '.install')) {
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
    }

    protected function createBackup($args)
    {
        if ((bool) $this->queue['settings']['backupSettings']['backup'] === false) {
            return true;
        }

        if ($this->queue['settings']['backupSettings']['notes'] === '') {
            $this->queue['settings']['backupSettings']['notes'] = 'Backup taken while processing module installer queue with ID: ' . $this->queue['id'];
        }

        $taskName = $args[0];
        $module = $args[1];

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        $resultQueueLogs = &$this->queue['results'][$taskName][$module['module_type']][$module['id']]['result_logs'];

        $backupInit = $this->basepackages->backuprestore->init()->backup($this->queue['settings']['backupSettings'], true);

        if (!$backupInit) {
            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

            return $this->queueHasErrors(
                $this->basepackages->backuprestore->packagesData->responseMessage,
                $resultQueueLogs,
                false
            );
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
                        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

                        return $this->queueHasErrors(
                            $this->basepackages->backuprestore->packagesData->responseMessage,
                            $resultQueueLogs,
                            false
                        );
                    }

                    if ($call !== false) {
                        $call = true;
                    }

                    $this->basepackages->progress->updateProgress('createBackup', $call, false, $method);
                }
            }

            return true;
        }

        $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'fail';

        return $this->queueHasErrors(
            'Progress Error!',
            $resultQueueLogs,
            false
        );
    }

    protected function registerRunPrecheckProgressMethods()
    {
        $this->runPrecheckProgressMethods = [];

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

                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'external') {
                            if (isset($module['hasPatch']) && $module['hasPatch'] === true) {
                                array_push($this->runPrecheckProgressMethods,
                                    [
                                        'method'    => 'precheckQueueData-' . $module['root_module']['id'] . '-' . strtolower(str_replace(' ', '', $module['root_module']['name'])),
                                        'text'      => 'Perform precheck for module ' . $module['root_module']['name'] . ' (' . ucfirst($module['root_module']['module_type']) . ') ...',
                                        'args'      => [$taskName, $module['root_module']],
                                    ]
                                );
                                array_push($this->runPrecheckProgressMethods,
                                    [
                                        'method'    => 'downloadModulesFromRepo-' . $module['root_module']['id'] . '-' . strtolower(str_replace(' ', '', $module['root_module']['name'])),
                                        'text'      => 'Download module ' . $module['root_module']['name'] . ' (' . ucfirst($module['root_module']['module_type']) . ') files from repository...',
                                        'args'      => [$taskName, $module['root_module']],
                                        'remoteWeb' => true
                                    ]
                                );
                                array_push($this->runPrecheckProgressMethods,
                                    [
                                        'method'    => 'extractModulesDownloadedFromRepo-' . $module['root_module']['id'] . '-' . strtolower(str_replace(' ', '', $module['root_module']['name'])),
                                        'text'      => 'Extracting downloaded module ' . $module['root_module']['name'] . ' (' . ucfirst($module['root_module']['module_type']) . ')...'
                                    ]
                                );
                            }
                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Perform precheck for external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                    'args'      => [$taskName, $module, true],
                                ]
                            );
                        } else {
                            continue;
                        }
                    }

                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'external') {
                            continue;
                        } else {
                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'precheckQueueData-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Perform precheck for module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                    'args'      => [$taskName, $module],
                                ]
                            );
                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'downloadModulesFromRepo-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Download module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') files from repository...',
                                    'args'      => [$taskName, $module],
                                    'remoteWeb' => true
                                ]
                            );
                            array_push($this->runPrecheckProgressMethods,
                                [
                                    'method'    => 'extractModulesDownloadedFromRepo-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Extracting downloaded module ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...'
                                ]
                            );
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
        }

        $this->basepackages->progress->registerMethods($this->runPrecheckProgressMethods);
    }

    protected function registerRunProcessProgressMethods()
    {
        $this->runProcessProgressMethods = [];

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

                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'external') {
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'processExternalPackages-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Perform precheck for external package ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ') ...',
                                    'args'      => [$taskName, $module],
                                ]
                            );
                        } else {
                            continue;
                        }
                    }

                    foreach ($modules as $module) {
                        if ($taskName === 'first' && $moduleType === 'external') {
                            continue;
                        } else {
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'createBackup',
                                    'text'      => 'Creating filesystem and database backup...',
                                    'args'      => [$taskName, $module],
                                    'childs'    => $this->basepackages->backuprestore->getBackupProgressMethods()
                                ]
                            );
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'runRsync-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Running rsync for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module, false],
                                ]
                            );
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'runModuleInstallScripts-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Running module install scripts for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module],
                                ]
                            );
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'deleteSourceFiles-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Updating version for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module],
                                ]
                            );
                            array_push($this->runProcessProgressMethods,
                                [
                                    'method'    => 'updateVersion-' . $module['id'] . '-' . strtolower(str_replace(' ', '', $module['name'])),
                                    'text'      => 'Updating version for ' . $module['name'] . ' (' . ucfirst($module['module_type']) . ')...',
                                    'args'      => [$taskName, $module],
                                ]
                            );
                        }
                    }
                }
            }
        }

        $this->basepackages->progress->registerMethods($this->runProcessProgressMethods);
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

            $files =
                $this->basepackages->utils->scanDir(
                    'var/tmp/installer/' . $modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' . $modulesToInstallOrUpdate['repo_details']['latestRelease']['name']
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

            $this->localContent->deleteDirectory(
                'var/tmp/installer/' . $modulesToInstallOrUpdate['repo_details']['details']['name'] . '-' . $modulesToInstallOrUpdate['repo_details']['latestRelease']['name']
            );

            $this->queue['results'][$taskName][$module['module_type']][$module['id']]['result'] = 'pass';
        } catch (UnableToListContents | UnableToDeleteDirectory | UnableToDeleteFile | \throwable $e) {
            return $this->queueHasErrors(
                $e->getMessage(),
                $resultQueueLogs
            );
        }
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

        return $this->basepackages->emailqueue->addToQueue($emailData);
    }
}