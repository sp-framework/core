<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Providers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Providers;

use Phalcon\Db\Enum;

/**
 * Seeds default Core framework registration record and updates DB connection pools.
 */
class Core
{
    /**
     * Registers default Core framework row.
     *
     * @param array<string, mixed> $baseConfig Base framework metadata array.
     * @param mixed                $db         PDO database connection adapter.
     * @param mixed                $ff         FlatFile database manager.
     *
     * @return void
     */
    public function register(array $baseConfig, mixed $db, mixed $ff): void
    {
        $core = [
            'name'         => $baseConfig['name'] ?? 'Core',
            'display_name' => $baseConfig['display_name'] ?? 'Core',
            'description'  => $baseConfig['description'] ?? 'Core Provider',
            'version'      => $baseConfig['version'] ?? '1.0.0',
            'repo'         => $baseConfig['repo'] ?? '',
            'settings'     => isset($baseConfig['settings']) ? json_encode($baseConfig['settings']) : null,
        ];

        if ($db) {
            $db->insertAsDict('service_provider_core', $core);
        }

        if ($ff) {
            $coreStore = $ff->store('service_provider_core');

            $coreStore->updateOrInsert($core);
        }
    }

    /**
     * Updates database connection pool configuration in the Core record.
     *
     * @param array<string, mixed> $dbs    Database connections array.
     * @param mixed                $helper Helpers service instance.
     * @param mixed                $db     PDO database connection adapter.
     * @param mixed                $ff     FlatFile database manager.
     *
     * @return void
     */
    public function onlyUpdateDb(array $dbs, mixed $helper, mixed $db, mixed $ff): void
    {
        if ($ff) {
            $coreStore = $ff->store('service_provider_core');
            $core = $coreStore?->findById('1');

            if ($core && is_array($core)) {
                if (isset($core['settings']) && is_string($core['settings'])) {
                    $core['settings'] = $helper->decode($core['settings'], true);
                }

                if (!isset($core['settings']['dbs']) || !is_array($core['settings']['dbs'])) {
                    $core['settings']['dbs'] = [];
                }

                $core['settings']['dbs'] = array_merge($core['settings']['dbs'], $dbs);
                $coreStore->updateOrInsert($core);
            }
        }

        if ($db) {
            $rows = $db->fetchAll(
                'SELECT * FROM service_provider_core WHERE id = :id',
                Enum::FETCH_ASSOC,
                ['id' => '1']
            );

            if (isset($rows[0]) && is_array($rows[0])) {
                $settings = $rows[0]['settings'] ?? [];

                if (is_string($settings)) {
                    $settings = $helper->decode($settings, true);
                }

                if (!isset($settings['dbs']) || !is_array($settings['dbs'])) {
                    $settings['dbs'] = [];
                }

                $settings['dbs'] = array_merge($settings['dbs'], $dbs);

                $db->updateAsDict(
                    'service_provider_core',
                    ['settings' => $helper->encode($settings)],
                    'id = 1'
                );
            }
        }
    }
}