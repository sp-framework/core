<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use Mattiasgeniar\Percentage\Percentage;
use System\Base\BasePackage;
use System\Base\Exceptions\DuplicateProgressException;

class Progress extends BasePackage
{
    protected $notificationsTunnel;

    protected $progressFileName;

    protected $countersTimer;

    protected $errors = [];

    protected $details = [];

    public function init($container = null, $fileName = null)
    {
        if ($container) {
            $this->container = $container;
        }

        if ($fileName) {
            $this->progressFileName = $fileName;
        }

        $this->checkProgressPath();

        if (!$this->notificationsTunnel) {
            $this->checkNotificationTunnel();
        }

        $this->init = true;

        return $this;
    }

    public function checkProgressFile($fileName = null)
    {
        if ($fileName) {
            $this->progressFileName = $fileName;
        }

        $progressFile = $this->readProgressFile();
        if ($progressFile) {
            return $progressFile;
        }

        return false;
    }

    public function getProgressFile()
    {
        return $this->readProgressFile();
    }

    public function registerMethods(array $methods)
    {
        foreach ($methods as $key => $method) {
            if (!is_array($method)) {
                throw new \Exception('Each entry of method needs to have method and text (description)');
            }
            if (!isset($method['method']) || !isset($method['text'])) {
                throw new \Exception('Each entry of method needs to have method and text (description)');
            }
        }

        $progressFile = $this->readProgressFile();

        $this->checkProcessIsRunning($progressFile);

        if ($progressFile && isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            foreach ($methods as $method) {
                array_push($progressFile['processes'], $method);
            }

            $this->writeProgressFile($progressFile['processes'], true);

            return true;
        } else {
            $this->writeProgressFile($methods, true);

            return true;
        }

        return false;
    }

    public function unregisterMethods(array $methods, $using = 'method', array $childs = [])//Either Method or Text as identifier in case of duplicate methods.
    {
        $progressFile = $this->readProgressFile();

        if ($using !== 'method' && $using !== 'text') {
            $using = 'method';
        }

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            foreach ($methods as $method) {
                foreach ($progressFile['processes'] as $progressFileKey => $progressFileMethod) {
                    if ($progressFileMethod[$using] === $method) {
                        if (count($childs) > 0 &&
                            isset($progressFile['processes'][$progressFileKey]['childs']) &&
                            count($progressFile['processes'][$progressFileKey]['childs']) > 0
                        ) {
                            foreach ($progressFile['processes'][$progressFileKey]['childs'] as $childKey => $child) {
                                if (in_array($child[$using], $childs)) {
                                    unset($progressFile['processes'][$progressFileKey]['childs'][$childKey]);

                                    if (isset($progressFile['registeredMethods'][$progressFileKey]['childs'][$childKey])) {
                                        unset($progressFile['registeredMethods'][$progressFileKey]['childs'][$childKey]);
                                    }
                                }
                            }
                        } else {
                            unset($progressFile['processes'][$progressFileKey]);

                            if (isset($progressFile['registeredMethods'][$progressFileKey])) {
                                unset($progressFile['registeredMethods'][$progressFileKey]);
                            }
                        }
                    }
                }
            }

            $this->writeProgressFile($progressFile['processes'], false, true);

            return true;
        }

        return false;
    }

    public function getProgress($session = null, $returnArray = false)
    {
        $progressFile = $this->readProgressFile($session);

        if (!$progressFile) {
            return false;
        }

        $errors = false;

        if (is_array($this->errors) && count($this->errors) > 0) {
            $errors = $this->helper->encode($this->errors);
        }

        $progress =
            [
                'progressFile'          => $this->progressFileName,
                'pid'                   => $progressFile['pid'] ?? false,
                'total'                 => $progressFile['total'],
                'completed'             => $progressFile['completed'],
                'preCheckComplete'      => $progressFile['preCheckComplete'],
                'totalPercentComplete'  => $this->getPercentComplete($progressFile, false),
                'percentComplete'       => $this->getPercentComplete($progressFile),
                'runners'               => $progressFile['runners'] ?? false,
                'callResult'            => $callResult,
                'errors'                => $errors
            ];
        if ($returnArray) {
            return $progress;
        }

        return $this->helper->encode($progress);
    }

    public function getCallResult($method)
    {
        $progressFile = $this->readProgressFile();

        if ($progressFile && isset($progressFile['allProcesses'])) {
            foreach ($progressFile['allProcesses'] as $allProcess) {
                if ($allProcess['method'] === $method) {
                    if (isset($allProcess['callResult'])) {
                        return $allProcess['callResult'];
                    }
                }
            }
        }
    }

    public function updateProgress($method, $callResult = null, $deleteFile = true, $child = null, array $counters = null, $text = null)
    {
        if (!$this->progressFileName) {
            $this->progressFileName = $this->session->getId();
        }

        $progressFile = $this->readProgressFile();

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            if (isset($progressFile['allProcesses'][0]) &&
                $progressFile['allProcesses'][0]['method'] === $method &&
                !$callResult &&
                !$child &&
                !$counters &&
                $progressFile['completed'] === 0 &&
                $progressFile['pid'] > 0
            ) {
                $this->checkProcessIsRunning($progressFile);
            }

            $runners = [];

            foreach ($progressFile['processes'] as $progressFileKey => $progressFileMethod) {
                if ($progressFileMethod['method'] === $method) {
                    if ($child && isset($progressFileMethod['childs'])) {
                        foreach ($progressFileMethod['childs'] as $childKey => $childValue) {
                            if ($childValue['method'] === $child) {
                                if ($callResult !== null) {
                                    unset($progressFile['processes'][$progressFileKey]['childs'][$childKey]);
                                }

                                $runners['child'] = true;
                                $runners['remainingChilds'] = count($progressFile['processes'][$progressFileKey]['childs']);
                                $currentProcess = current($progressFileMethod['childs']);

                                if (isset($currentProcess['remoteWeb']) && $currentProcess['remoteWeb'] === true && $counters) {
                                    $currentProcess = array_merge($currentProcess, ['remoteWebCounters' => $counters]);
                                } else if (isset($currentProcess['steps']) && $currentProcess['steps'] === true && $counters) {
                                    $currentProcess = array_merge($currentProcess, ['stepsCounters' => $counters]);
                                }

                                $runners['running'] = $currentProcess;
                                if ($text) {
                                    $runners['running']['text'] = $text;
                                }
                                $runners['next'] = next($progressFileMethod['childs']);

                                break;
                            }
                        }

                        if (count($progressFileMethod['childs']) === 0) {
                            unset($progressFile['processes'][$progressFileKey]);
                        }
                    } else {
                        if ($callResult !== null) {
                            unset($progressFile['processes'][$progressFileKey]);
                        }

                        $currentProcess = current($progressFile['processes']);

                        if (isset($currentProcess['remoteWeb']) && $currentProcess['remoteWeb'] === true && $counters) {
                            $currentProcess = array_merge($currentProcess, ['remoteWebCounters' => $counters]);
                        } else if (isset($currentProcess['steps']) && $currentProcess['steps'] === true && $counters) {
                            $currentProcess = array_merge($currentProcess, ['stepsCounters' => $counters]);
                        }

                        $runners['running'] = $currentProcess;
                        if ($text) {
                            $runners['running']['text'] = $text;
                        }
                        $runners['next'] = next($progressFile['processes']);
                    }

                    break;
                }
            }

            if (count($progressFile['processes']) === 0 && $deleteFile) {
                $this->deleteProgressFile();

                return true;
            }

            if ($callResult !== false) {
                $callResult = true;
            }

            $this->writeProgressFile($progressFile['processes'], false, false, true, $runners, null, $method, $callResult, $child, $counters);

            if ($callResult === true) {
                $this->sendNotification($callResult, $counters);
            }

            return true;
        }

        return false;
    }

    public function checkProcessIsRunning($progressFile = null)
    {
        if (!$progressFile) {
            $progressFile = $this->checkProgressFile();
        }

        //Check if process is running
        //If a user tries to reinitiate same process from start, we should return an error.
        if ($progressFile &&
            array_key_exists('runners', $progressFile) &&
            ($progressFile['runners']['running'] !== false) &&
            $progressFile['pid'] > 0
        ) {
            if (isset($progressFile['pid']) && $progressFile['pid'] > 0) {
                exec('ps -aux | grep ' . $progressFile['pid'], $output, $result);

                if ($result === 0 &&
                    count($output) > 0
                ) {
                    if (str_contains($output[0], 'php')) {
                        $this->errors = ['PID running' => 'Progress is running with process ID: ' . $progressFile['pid']];

                        $this->sendNotification('pid_running');

                        throw new DuplicateProgressException('Progress is running with process ID: ' . $progressFile['pid']);
                    }
                }
            }
        }
    }

    protected function checkNotificationTunnel()
    {
        if (!$this->notificationsTunnel && isset($this->apps)) {
            $account = $this->basepackages->accounts->getAccountById($this->access->auth->account()['id']);

            if ($account && isset($account['tunnels']['notifications_tunnel'])) {
                $this->notificationsTunnel = $account['tunnels']['notifications_tunnel'];
            }
        } else {
            $this->notificationsTunnel = 0;
        }
    }

    protected function sendNotification($callResult, $counters = null)
    {
        if ($counters &&
            (isset($counters['downloadTotal']) && ($counters['downloadTotal'] !== $counters['downloadedBytes']) ||
             isset($counters['uploadTotal']) && ($counters['uploadTotal'] !== $counters['uploadedBytes']) ||
             isset($counters['stepsTotal']) && ($counters['stepsTotal'] !== $counters['stepsCurrent'])
            )
        ) {//only for remoteWebCounters
            if (!$this->countersTimer) {
                $this->countersTimer = time();
            } else {
                if ((time() - $this->countersTimer) < 1) {
                    return false;//To minimize chatting on ws, we add a 1 second delay.
                } else {
                    $this->countersTimer = time();
                }
            }
        }

        if ($this->notificationsTunnel !== null) {
            $progressFile = $this->readProgressFile();

            if ($progressFile) {
                $errors = false;

                if (is_array($this->errors) && count($this->errors) > 0) {
                    $errors = $this->helper->encode($this->errors);
                }

                $details = false;
                if (isset($progressFile['allProcesses']) && is_array($progressFile['allProcesses'])) {
                    $details = [];

                    foreach ($progressFile['allProcesses'] as $processKey => $process) {
                        if (!array_key_exists('callResult', $process)) {
                            continue;
                        }

                        $processCallResult = 'Running...';
                        if (isset($progressFile['runners']['running']['method']) &&
                            $progressFile['runners']['running']['method'] !== $process['method']) {
                            if ($process['callResult'] === true) {
                                $processCallResult = 'Done';
                            } else if ($process['callResult'] === false) {
                                $processCallResult = 'Error';
                            }
                            if (count($this->errors) > 0) {
                                $processCallResult = 'Error';
                            }
                        }

                        $details[$process['text']] = $processCallResult;
                    }

                    if (count($details) === 0) {
                        $details = false;
                    }
                }

                $this->wss->send(
                    [
                        'type'              => 'progress',
                        'to'                => $this->notificationsTunnel,
                        'response'          => [
                            'responseCode'      => 0,
                            'responseMessage'   => 'Ok',
                            'responseData'      =>
                                [
                                    'progressFile'          => $this->progressFileName,
                                    'pid'                   => $progressFile['pid'] ?? false,
                                    'total'                 => $progressFile['total'],
                                    'completed'             => $progressFile['completed'],
                                    'preCheckComplete'      => $progressFile['preCheckComplete'],
                                    'totalPercentComplete'  => $this->getPercentComplete($progressFile, false),
                                    'percentComplete'       => $this->getPercentComplete($progressFile),
                                    'runners'               => $progressFile['runners'] ?? false,
                                    'callResult'            => $callResult,
                                    'errors'                => $errors,
                                    'details'               => $details
                                ]
                        ]
                    ]
                );
            }
        }
    }

    protected function getPercentComplete($progressFile, $counters = true)
    {
        $percentComplete = (float) number_format(($progressFile['completed'] * 100) / $progressFile['total']);

        if ($counters) {
            if (isset($progressFile['runners']['running']['remoteWebCounters'])) {
                $webProgress = 0;

                if (isset($progressFile['runners']['running']['remoteWebCounters']['downloadTotal']) && $progressFile['runners']['running']['remoteWebCounters']['downloadTotal'] > 0) {
                    $webProgress = Percentage::calculate($progressFile['runners']['running']['remoteWebCounters']['downloadedBytes'], $progressFile['runners']['running']['remoteWebCounters']['downloadTotal']);
                } else if (isset($progressFile['runners']['running']['remoteWebCounters']['uploadTotal']) && $progressFile['runners']['running']['remoteWebCounters']['uploadTotal'] > 0) {
                    $webProgress = Percentage::calculate($progressFile['runners']['running']['remoteWebCounters']['uploadedBytes'], $progressFile['runners']['running']['remoteWebCounters']['uploadTotal']);
                }

                if ($webProgress > -1) {
                    $percentComplete = (float) number_format($webProgress);
                }
            } else if (isset($progressFile['runners']['running']['stepsCounters'])) {
                $stepsProgress = 0;

                if (isset($progressFile['runners']['running']['stepsCounters']['stepsTotal']) && $progressFile['runners']['running']['stepsCounters']['stepsTotal'] > 0) {
                    $stepsProgress = Percentage::calculate($progressFile['runners']['running']['stepsCounters']['stepsCurrent'], $progressFile['runners']['running']['stepsCounters']['stepsTotal']);
                }

                if ($stepsProgress > -1) {
                    $percentComplete = (float) number_format($stepsProgress);
                }
            }
        }

        return $percentComplete;
    }

    public function preCheckComplete($complete = true)
    {
        $progressFile = $this->readProgressFile();

        if ($progressFile) {
            $progressFile['preCheckComplete'] = $complete;

            $this->writeProgressFile($progressFile['processes'], false, false, false, null, $progressFile);
        }
    }

    public function resetProgress($reRegisterMethods = true)
    {
        $progressFile = $this->readProgressFile();

        if ($progressFile) {
            $this->deleteProgressFile(true);

            if ($reRegisterMethods) {
                $this->registerMethods($progressFile['registeredMethods']);
            }
        }

        $this->sendNotification('reset');

        return true;
    }

    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    protected function checkProgressPath()
    {
        if (!is_dir(base_path('var/progress/'))) {
            if (!mkdir(base_path('var/progress/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }

    public function cancelProgress($fileName = null)
    {
        $progressFile = $this->checkProgressFile($fileName);

        if ($progressFile) {
            if (isset($progressFile['pid']) && $progressFile['pid'] > 0) {
                exec('ps -aux | grep ' . $progressFile['pid'], $output, $result);

                if ($result === 0 &&
                    count($output) > 0
                ) {
                    if (str_contains($output[0], 'php')) {
                        exec('kill -9 ' . $progressFile['pid'], $output, $result);

                        if ($result !== 0) {
                            $this->addResponse('Error terminating process', 1, ['output' => $output]);

                            return false;
                        }

                        $this->writeProgressFile(methods: [],progressFile: $progressFile);

                        $this->addResponse('Successfully terminating process');

                        return $this->resetProgress();
                    }
                }
            }

            $progressFile['runners']['running'] = false;
            $progressFile['runners']['next'] = false;
            $this->writeProgressFile(methods: [], progressFile: $progressFile, register: true);

            $this->addResponse('Process not running', 1);

            return false;
        } else {
            $this->addResponse('Error loading progressfile!', 1);

            return false;
        }
    }

    protected function readProgressFile($session = null)
    {
        if (!$this->progressFileName) {
            if ($session) {
                $this->progressFileName = $session;
            } else {
                $this->progressFileName = $this->session->getId();
            }
        }

        if ($this->opCache) {
            return $this->opCache->getCache($this->progressFileName, 'progress');
        } else {
            try {
                return $this->helper->decode($this->localContent->read('/var/progress/' . $this->progressFileName . '.json'), true);
            } catch (\ErrorException | FilesystemException | UnableToReadFile | \InvalidArgumentException $exception) {
                return false;
            }
        }
    }

    protected function writeProgressFile(
        $methods,
        $register = false,
        $unregister = false,
        $update = false,
        $runners = null,
        $progressFile = null,
        $method = null,
        $callResult = null,
        $child = null,
        array $counters = null
    ) {
        if ($progressFile) {
            $file = $progressFile;
        } else {
            if ($register || $unregister) {
                $file['total'] = count($methods);
                $file['completed'] = 0;
                $file['preCheckComplete'] = false;
                $file['runners']['running'] = current($methods);
                $file['runners']['next'] = next($methods);
                if ($register) {
                    $file['allProcesses'] = $file['registeredMethods'] = $methods;
                } else if ($unregister) {
                    $progressFile = $this->readProgressFile();
                    $file['allProcesses'] = $progressFile['registeredMethods'];
                }
            }

            if ($update) {
                $progressFile = $this->readProgressFile();

                if (isset($progressFile['allProcesses'])) {
                    if ($method) {
                        foreach ($progressFile['allProcesses'] as &$allProcess) {
                            if ($allProcess['method'] === $method) {
                                if ($child && isset($allProcess['childs'])) {
                                    $totalChilds = count($allProcess['childs']);

                                    foreach ($allProcess['childs'] as $childKey => &$childValue) {
                                        if ($childValue['method'] === $child) {
                                            if ($callResult !== null) {
                                                $childValue['callResult'] = $callResult;
                                            }

                                            if (!isset($childValue['callExecTime'])) {
                                                $childValue['callExecTime'] = gettimeofday(true);
                                            } else {
                                                $childValue['callExecTime'] = gettimeofday(true) - $childValue['callExecTime'];
                                            }
                                        }
                                    }
                                } else {
                                    if ($callResult !== null) {
                                        $allProcess['callResult'] = $callResult;
                                    }

                                    if (!isset($allProcess['callExecTime'])) {
                                        $allProcess['callExecTime'] = gettimeofday(true);
                                    } else {
                                        $allProcess['callExecTime'] = gettimeofday(true) - $allProcess['callExecTime'];
                                    }

                                    if (isset($allProcess['remoteWeb']) && $allProcess['remoteWeb'] === true && $counters) {
                                        $allProcess = array_merge($allProcess, $counters);
                                    } else if (isset($allProcess['steps']) && $allProcess['steps'] === true && $counters) {
                                        $allProcess = array_merge($allProcess, $counters);
                                    }
                                }
                            }
                        }
                    }

                    $file['allProcesses'] = $progressFile['allProcesses'];
                }

                if ($child) {
                    $file['total'] = $totalChilds;
                    $file['completed'] = $totalChilds - $runners['remainingChilds'];
                } else {
                    $file['total'] = count($progressFile['allProcesses']);
                    $file['completed'] = count($progressFile['allProcesses']) - count($methods);
                }

                $file['preCheckComplete'] = true;

                if ($runners) {
                    $file['runners'] = $runners;
                }

                $file['registeredMethods'] = $progressFile['registeredMethods'];
            }

            $file['processes'] = $methods;
        }

        $file['notifications_tunnel'] = $this->notificationsTunnel;

        if ($register) {
            $file['pid'] = 0;
        }

        if (!array_key_exists('pid', $file)) {
            $file['pid'] = getmypid();
        }

        $file['errors'] = $this->errors;

        if ($this->opCache) {
            if ($progressFile) {
                $this->opCache->resetCache($this->progressFileName, $file, 'progress');
            } else {
                $this->opCache->setCache($this->progressFileName, $file, 'progress');
            }
        } else {
            try {
                $this->localContent->write('var/progress/' . $this->progressFileName . '.json' , $this->helper->encode($file));
            } catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
                throw $exception;
            }
        }
    }

    public function deleteProgressFile($reset = false)
    {
        if (!$reset) {
            $this->checkProcessIsRunning();
        }

        if (!$this->progressFileName) {
            $this->progressFileName = $this->session->getId();
        }

        if ($this->opCache) {
            $this->opCache->removeCache($this->progressFileName, 'progress');
        } else {
            try {
                $this->localContent->delete('var/progress/' . $this->progressFileName . '.json');
            } catch (\ErrorException | FilesystemException | UnableToDeleteFile $exception) {
                throw $exception;
            }
        }
    }
}