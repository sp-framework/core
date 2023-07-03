<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Progress extends BasePackage
{
    protected $notificationsTunnel;

    protected $progressFileName;

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

        if ($this->readProgressFile()) {
            return true;
        }

        return false;
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

    public function unregisterMethods(array $methods, $using = 'method')//Either Method or Text as identifier in case of duplicate methods.
    {
        $progressFile = $this->readProgressFile();

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            foreach ($methods as $method) {
                foreach ($progressFile['processes'] as $progressFileKey => $progressFileMethod) {
                    if ($using === 'method') {
                        if ($progressFileMethod['method'] === $method) {
                            unset($progressFile['processes'][$progressFileKey]);
                        }
                    } else if ($using === 'text') {
                        if ($progressFileMethod['text'] === $method) {
                            unset($progressFile['processes'][$progressFileKey]);
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

        if (!$progressFile || ($progressFile && !isset($progressFile['runners']))) {
            return false;
        }

        $progress =
            [
                'total'             => $progressFile['total'],
                'completed'         => $progressFile['completed'],
                'preCheckComplete'  => $progressFile['preCheckComplete'],
                'percentComplete'   => $this->getPercentComplete($progressFile),
                'runners'           => $progressFile['runners']
            ];

        if ($returnArray) {
            return $progress;
        }

        return Json::encode($progress);
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

    public function updateProgress($method, $callResult = null, $deleteFile = true, $child = null, array $remoteWebCounters = null)
    {
        if (!$this->progressFileName) {
            $this->progressFileName = $this->session->getId();
        }

        $progressFile = $this->readProgressFile();

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
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
                                $runners['running'] = current($progressFileMethod['childs']);
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

                        if (isset($currentProcess['remoteWeb']) && $currentProcess['remoteWeb'] === true && $remoteWebCounters) {
                            $currentProcess = array_merge($currentProcess, ['remoteWebCounters' => $remoteWebCounters]);
                        }

                        $runners['running'] = $currentProcess;
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

            $this->writeProgressFile($progressFile['processes'], false, false, true, $runners, null, $method, $callResult, $child, $remoteWebCounters);

            if ($callResult === true) {
                $this->sendNotification($callResult);
            }

            return true;
        }

        return false;
    }

    protected function checkNotificationTunnel()
    {
        if (!$this->notificationsTunnel && isset($this->apps)) {
            $account = $this->basepackages->accounts->getAccountById($this->auth->account()['id']);

            if ($account && isset($account['tunnels']['notifications_tunnel'])) {
                $this->notificationsTunnel = $account['tunnels']['notifications_tunnel'];
            }
        } else {
            $this->notificationsTunnel = 0;
        }
    }

    protected function sendNotification($callResult)
    {
        if ($this->notificationsTunnel !== null) {
            $progressFile = $this->readProgressFile();

            if ($progressFile) {
                $this->wss->send(
                    [
                        'type'              => 'progress',
                        'to'                => $this->notificationsTunnel,
                        'response'          => [
                            'responseCode'      => 0,
                            'responseMessage'   => 'Ok',
                            'responseData'      =>
                                [
                                    'total'             => $progressFile['total'],
                                    'completed'         => $progressFile['completed'],
                                    'preCheckComplete'  => $progressFile['preCheckComplete'],
                                    'percentComplete'   => $this->getPercentComplete($progressFile),
                                    'runners'           => $progressFile['runners'] ?? false,
                                    'callResult'        => $callResult
                                ]
                        ]
                    ]
                );
            }
        }
    }

    protected function getPercentComplete($progressFile)
    {
        $percentComplete = (float) number_format(($progressFile['completed'] * 100) / $progressFile['total']);

        if (isset($progressFile['runners']['running']['remoteWebCounters'])) {
            $methodPercent = (float) number_format(100 / $progressFile['total']);

            $webProgress = 0;

            if (isset($progressFile['runners']['running']['remoteWebCounters']['downloadTotal']) && $progressFile['runners']['running']['remoteWebCounters']['downloadTotal'] > 0) {
                $webProgress = (float) number_format(($progressFile['runners']['running']['remoteWebCounters']['downloadedBytes'] * 100) / $progressFile['runners']['running']['remoteWebCounters']['downloadTotal']);
            } else if (isset($progressFile['runners']['running']['remoteWebCounters']['uploadTotal']) && $progressFile['runners']['running']['remoteWebCounters']['uploadTotal'] > 0) {
                $webProgress = (float) number_format(($progressFile['runners']['running']['remoteWebCounters']['uploadedBytes'] * 100) / $progressFile['runners']['running']['remoteWebCounters']['uploadTotal']);
            }

            if ($webProgress > 0) {
                $totalProgress = (float) number_format($webProgress * 100 / $methodPercent);

                $percentComplete = $percentComplete + $totalProgress;
            }
        }//Need to do if runner is a child

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
            $this->deleteProgressFile();

            if ($reRegisterMethods) {
                $this->registerMethods($progressFile['allProcesses']);
            }
        }

        $this->sendNotification('reset');

        return true;
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
                return Json::decode($this->localContent->read('/var/progress/' . $this->progressFileName . '.json'), true);
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
        array $remoteWebCounters = null
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
                    $file['allProcesses'] = $methods;
                } else if ($unregister) {
                    $progressFile = $this->readProgressFile();
                    $file['allProcesses'] = $progressFile['allProcesses'];
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

                                    if (isset($allProcess['remoteWeb']) && $allProcess['remoteWeb'] === true && $remoteWebCounters) {
                                        $allProcess = array_merge($allProcess, $remoteWebCounters);
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
            }

            $file['processes'] = $methods;
        }

        $file['notifications_tunnel'] = $this->notificationsTunnel;

        if ($this->opCache) {
            if ($progressFile) {
                $this->opCache->resetCache($this->progressFileName, $file, 'progress');
            } else {
                $this->opCache->setCache($this->progressFileName, $file, 'progress');
            }
        } else {
            try {
                $this->localContent->write('var/progress/' . $this->progressFileName . '.json' , Json::encode($file));
            } catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
                throw $exception;
            }
        }
    }

    public function deleteProgressFile()
    {
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