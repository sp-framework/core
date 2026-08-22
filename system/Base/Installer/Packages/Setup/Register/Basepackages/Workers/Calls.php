<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\Workers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Db\Enum;
use ReflectionClass;

/**
 * Discovers and seeds background worker callable definitions into basepackages_workers_calls.
 */
class Calls
{
    /**
     * Scans and registers worker callable classes.
     *
     * @param mixed  $db            PDO database connection adapter.
     * @param mixed  $ff            FlatFile database manager.
     * @param mixed  $basepackages  Basepackages manager instance.
     * @param mixed  $container     DI container.
     * @param mixed  $corePackageId Core package ID.
     * @param string $databasetype  Database driver type.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, mixed $basepackages, mixed $container, mixed $corePackageId, string $databasetype): void
    {
        $callsArr = $basepackages->utils->init($container)->scanDir(
            'system/Base/Providers/BasepackagesServiceProvider/Packages/Workers/Calls/'
        );

        if (isset($callsArr['files']) && is_array($callsArr['files']) && count($callsArr['files']) > 0) {
            foreach ($callsArr['files'] as $call) {
                $call = ucfirst((string) $call);
                $call = str_replace('/', '\\', $call);
                $call = str_replace('.php', '', $call);

                if (!class_exists($call)) {
                    continue;
                }

                $callClass = new $call();
                $callReflection = new ReflectionClass($call);

                if ($databasetype !== 'db' && $ff) {
                    $callStorage = $ff->store('basepackages_workers_calls');
                    $dbCall = $callStorage?->findBy(['name', '=', $callReflection->getShortName()]);
                } elseif ($db) {
                    $dbCall = $db->fetchAll(
                        'SELECT * FROM basepackages_workers_calls WHERE name = :name',
                        Enum::FETCH_ASSOC,
                        ['name' => $callReflection->getShortName()]
                    );
                } else {
                    $dbCall = null;
                }

                if ($dbCall && is_array($dbCall) && count($dbCall) > 0) {
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
                        $callStorage = $ff->store('basepackages_workers_calls');

                        $callStorage->updateOrInsert($dbCall);
                    }
                }
            }
        }
    }
}