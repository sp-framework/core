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
    public function init($container = null)
    {
        if ($container) {
            $this->container = $container;
        }

        $this->checkProgressPath();

        return $this;
    }

    public function checkProgressFile($session = null)
    {
        if ($this->readProgressFile($session)) {
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
                'percentComplete'   => number_format(($progressFile['completed'] * 100) / $progressFile['total']),
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

    public function updateProgress($method, $callResult = null, $deleteFile = true)
    {
        $progressFile = $this->readProgressFile();

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            $runners = [];

            foreach ($progressFile['processes'] as $progressFileKey => $progressFileMethod) {
                if ($progressFileMethod['method'] === $method) {
                    if ($callResult !== null) {
                        unset($progressFile['processes'][$progressFileKey]);
                    }

                    $runners['running'] = current($progressFile['processes']);
                    $runners['next'] = next($progressFile['processes']);

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

            $this->writeProgressFile($progressFile['processes'], false, false, true, $runners, null, $method, $callResult);

            if ($callResult === true) {
                $this->sendNotification($callResult);
            }

            return true;
        }

        return false;
    }

    protected function sendNotification($callResult)
    {
        if (isset($this->apps)) {
            $account =
                $this->basepackages->accounts->getAccountById(
                    $this->auth->account()['id'], false, false, false, false, false, true
                );

            if ($account && isset($account['notifications_tunnel'])) {
                $notificationTunnel = $account['notifications_tunnel'];
            }
        } else {
            $notificationTunnel = 0;
        }

        if (isset($notificationTunnel)) {
            $progressFile = $this->readProgressFile();

            $this->wss->send(
                [
                    'type'              => 'progress',
                    'to'                => $notificationTunnel,
                    'response'          => [
                        'responseCode'      => 0,
                        'responseMessage'   => 'Ok',
                        'responseData'      =>
                            [
                                'total'             => $progressFile['total'],
                                'completed'         => $progressFile['completed'],
                                'preCheckComplete'  => $progressFile['preCheckComplete'],
                                'percentComplete'   => number_format(($progressFile['completed'] * 100) / $progressFile['total']),
                                'runners'           => $progressFile['runners'] ?? false,
                                'callResult'        => $callResult
                            ]
                    ]
                ]
            );
        }
    }

    public function preCheckComplete($complete = true)
    {
        $progressFile = $this->readProgressFile();

        $progressFile['preCheckComplete'] = $complete;

        $this->writeProgressFile($progressFile['processes'], false, false, false, null, $progressFile);
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
        if (!$session) {
            $session = $this->session->getId();
        }

        if ($this->opCache) {
            return $this->opCache->getCache($session, 'progress');
        } else {
            try {
                return Json::decode($this->localContent->read('/var/progress/' . $session . '.json'), true);
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
        $callResult = null
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
                                if ($callResult !== null) {
                                    $allProcess['callResult'] = $callResult;
                                }

                                if (!isset($allProcess['callExecTime'])) {
                                    $allProcess['callExecTime'] = gettimeofday(true);
                                } else {
                                    $allProcess['callExecTime'] = gettimeofday(true) - $allProcess['callExecTime'];
                                }
                            }
                        }
                    }

                    $file['allProcesses'] = $progressFile['allProcesses'];
                }
                $file['total'] = $progressFile['total'];
                $file['completed'] = $progressFile['total'] - count($methods);
                $file['preCheckComplete'] = true;
                if ($runners) {
                    $file['runners'] = $runners;
                }
            }

            $file['processes'] = $methods;
        }

        if ($this->opCache) {
            if ($progressFile) {
                $this->opCache->resetCache($this->session->getId(), $file, 'progress');
            } else {
                $this->opCache->setCache($this->session->getId(), $file, 'progress');
            }
        } else {
            try {
                $this->localContent->write('var/progress/' . $this->session->getId() . '.json' , Json::encode($file));
            } catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
                throw $exception;
            }
        }
    }

    public function deleteProgressFile()
    {
        if ($this->opCache) {
            $this->opCache->removeCache($this->session->getId(), 'progress');
        } else {
            try {
                $this->localContent->delete('var/progress/' . $this->session->getId() . '.json');
            } catch (\ErrorException | FilesystemException | UnableToDeleteFile $exception) {
                throw $exception;
            }
        }
    }
}