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
    public function init($localContent = null)
    {
        if ($localContent) {
            $this->localContent = $localContent;
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

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
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

    public function getProgress($session = null)
    {
        $progressFile = $this->readProgressFile($session);

        if (!$progressFile) {
            return false;
        }

        return Json::encode(
            [
                'total'             => $progressFile['total'],
                'completed'         => $progressFile['completed'],
                'preCheckComplete'  => $progressFile['preCheckComplete'],
                'percentComplete'   => number_format(($progressFile['completed'] * 100) / $progressFile['total']),
                'runners'           => $progressFile['runners']
            ]
        );
    }

    public function updateProgress($method, $deleteFile = true)
    {
        $progressFile = $this->readProgressFile();

        if (isset($progressFile['processes']) && count($progressFile['processes']) > 0) {
            $runners = [];
            foreach ($progressFile['processes'] as $progressFileKey => $progressFileMethod) {
                if ($progressFileMethod['method'] === $method) {
                    $runners['last'] = current($progressFile['processes']);
                    $runners['running'] = next($progressFile['processes']);
                    $runners['next'] = next($progressFile['processes']);
                    unset($progressFile['processes'][$progressFileKey]);
                    break;
                }
            }

            if (count($progressFile['processes']) === 0 && $deleteFile) {
                $this->deleteProgressFile();

                return true;
            }

            $this->writeProgressFile($progressFile['processes'], false, false, true, $runners);

            return true;
        }

        return false;
    }

    public function preCheckComplete($complete = true)
    {
        $progressFile = $this->readProgressFile();

        $progressFile['preCheckComplete'] = $complete;

        $this->writeProgressFile($progressFile['processes'], false, false, false, null, $progressFile);
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

        try {
            return Json::decode($this->localContent->read('/var/progress/' . $session . '.json'), true);
        } catch (\ErrorException | FilesystemException | UnableToReadFile $exception) {
            return false;
        }
    }

    protected function writeProgressFile($methods, $register = false, $unregister = false, $update = false, $runners = null, $progressFile = null)
    {
        if ($progressFile) {
            $file = $progressFile;
        } else {
            if ($register || $unregister) {
                $file['total'] = count($methods);
                $file['completed'] = 0;
                $file['preCheckComplete'] = false;
                $file['runners']['last'] = [];
                $file['runners']['running'] = current($methods);
                $file['runners']['next'] = next($methods);
            }

            if ($update) {
                $progressFile = $this->readProgressFile();
                $file['total'] = $progressFile['total'];
                $file['completed'] = $progressFile['total'] - count($methods);
                $file['preCheckComplete'] = true;
                if ($runners) {
                    $file['runners'] = $runners;
                }
            }

            $file['processes'] = $methods;
        }

        try {
            $this->localContent->write('var/progress/' . $this->session->getId() . '.json' , Json::encode($file));
        } catch (\ErrorException | FilesystemException | UnableToWriteFile $exception) {
            throw $exception;
        }
    }

    protected function deleteProgressFile()
    {
        try {
            $this->localContent->delete('var/progress/' . $this->session->getId() . '.json');
        } catch (\ErrorException | FilesystemException | UnableToDeleteFile $exception) {
            throw $exception;
        }
    }
}