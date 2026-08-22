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

/**
 * Seeds initial pool of background worker threads into basepackages_workers_workers.
 */
class Workers
{
    /**
     * Registers pool of 100 worker slots.
     *
     * @param mixed $db PDO database connection adapter.
     * @param mixed $ff FlatFile database manager.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff): void
    {
        $workersArr = $this->workers();

        foreach ($workersArr as $worker) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_workers', $worker);
            }

            if ($ff) {
                $workerStore = $ff->store('basepackages_workers_workers');

                $workerStore->updateOrInsert($worker);
            }
        }
    }

    /**
     * Generates pool of 100 worker rows.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function workers(): array
    {
        $workersArr = [];

        for ($x = 1; $x <= 100; $x++) {
            $workersArr[] = [
                'name'    => 'Worker ' . $x,
                'status'  => 0,
                'enabled' => 1
            ];
        }

        return $workersArr;
    }
}