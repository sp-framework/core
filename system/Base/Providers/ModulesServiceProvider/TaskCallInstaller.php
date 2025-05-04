<?php

namespace System\Base\Providers\ModulesServiceProvider;

use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;

class TaskCallInstaller extends BasePackage
{
    public function installTaskCall($packageClass)
    {
        try {
            $packageFile = 'apps/' . implode('/', array_slice(explode('\\', get_class($packageClass)), 1, -1)) . '/package.json';

            if ($this->localContent->fileExists($packageFile)) {
                $installPackageJsonFile = $this->helper->decode($this->localContent->read($packageFile), true);

                $package = $this->modules->packages->getPackageByClass($installPackageJsonFile['class']);

                if ($package) {
                    $packageFolder = 'apps/' . implode('/', array_slice(explode('\\', get_class($packageClass)), 1, -2)) . '/TaskCalls/';

                    $callsArr = $this->basepackages->utils->scanDir($packageFolder);

                    if (count($callsArr['files']) > 0) {
                        foreach ($callsArr['files'] as $key => $call) {
                            $call = ucfirst($call);
                            $call = str_replace('/', '\\', $call);
                            $call = str_replace('.php', '', $call);

                            $callClass = new $call;
                            $callReflection = new \ReflectionClass($call);

                            $dbCall = $this->basepackages->workers->calls->getByCallName($callReflection->getShortName());

                            if (!$dbCall) {
                                $dbCall = [];
                                $dbCall['name'] = $callReflection->getShortName();
                                $dbCall['display_name'] = $callReflection->getShortName();
                                $dbCall['class'] = $callReflection->getName();
                                if ($callReflection->hasProperty('funcDisplayName')) {
                                    $dbCall['display_name'] = $callReflection->getProperty('funcDisplayName')->getValue($callClass);
                                }
                                $dbCall['description'] = '';
                                if ($callReflection->hasProperty('funcDescription')) {
                                    $dbCall['description'] = $callReflection->getProperty('funcDescription')->getValue($callClass);
                                }
                                $dbCall['can_be_scheduled'] = true;
                                if ($callReflection->hasProperty('funcCanBeScheduled')) {
                                    $dbCall['can_be_scheduled'] = $callReflection->getProperty('funcCanBeScheduled')->getValue($callClass);
                                }
                                $dbCall['can_be_run_on_demand'] = true;
                                if ($callReflection->hasProperty('funcCanBeRunOnDemand')) {
                                    $dbCall['can_be_run_on_demand'] = $callReflection->getProperty('funcCanBeRunOnDemand')->getValue($callClass);
                                }
                                $dbCall['package_id'] = $package['id'];

                                $this->basepackages->workers->calls->addCall($dbCall);
                            }
                        }
                    }
                }
            }
        } catch (FilesystemException | UnableToCheckExistence | UnableToReadFile | \throwable $e) {
            throw $e;
        }

        return true;
    }

    public function uninstallTaskCall($packageClass)
    {
        //Check for running jobs
        //Uninstall Task associated with the call
        //Uninstall Call
    }
}