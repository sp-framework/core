<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Db\Enum;

class Calls
{
    public function register($db, $ff, $basepackages, $container, $corePackageId, $databasetype)
    {
         $callsArr = $basepackages->utils->init($container)->scanDir(
            'system/Base/Providers/BasepackagesServiceProvider/Packages/Workers/Calls/'
         );

        if (count($callsArr['files']) > 0) {
            foreach ($callsArr['files'] as $key => $call) {
                $call = ucfirst($call);
                $call = str_replace('/', '\\', $call);
                $call = str_replace('.php', '', $call);

                $callClass = new $call;
                $callReflection = new \ReflectionClass($call);

                if ($databasetype !== 'db') {
                    $callStorage = $ff->store('basepackages_workers_calls');

                    $dbCall = $callStorage->findBy(['name', '=', $callReflection->getShortName()]);
                } else {
                    $dbCall =
                        $db->fetchAll(
                            "SELECT * FROM basepackages_workers_calls WHERE name = :name",
                            Enum::FETCH_ASSOC,
                            [
                                "name" => $callReflection->getShortName(),
                            ]
                        );
                }

                if ($dbCall && count($dbCall) > 0) {
                    $dbCall = $dbCall[0];
                }

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
                    $dbCall['package_id'] = $corePackageId;

                    if ($db) {
                        $db->insertAsDict('basepackages_workers_calls', $dbCall);
                    }

                    if ($ff) {
                        $callStore = $ff->store('basepackages_workers_calls');

                        $callStore->updateOrInsert($dbCall);
                    }
                }
            }
        }
    }
}