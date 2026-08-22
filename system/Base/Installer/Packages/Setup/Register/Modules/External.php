<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Modules
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Composer\Console\Application as ComposerApp;
use Phalcon\Db\Enum;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Throwable;

/**
 * Seeds and discovers installed Composer external dependencies in modules_externals.
 *
 * @package System\Base\Installer\Packages\Setup\Register\Modules
 */
class External
{
    /**
     * Registers external vendor packages into modules_externals table and store.
     *
     * @param mixed                $db               PDO database connection adapter.
     * @param mixed                $ff               FlatFile database manager.
     * @param array<string, mixed> $composerJsonFile Decoded composer.json file contents.
     * @param mixed                $helper           Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $composerJsonFile, mixed $helper): void
    {
        if (isset($composerJsonFile['require']) && is_array($composerJsonFile['require']) && count($composerJsonFile['require']) > 0) {
            $installedComposerPackages = $this->executeComposer();

            $coreId = $this->getCoreId($db, $ff);

            try {
                if (is_string($installedComposerPackages) && method_exists($helper, 'decode')) {
                    $installedComposerPackages = $helper->decode($installedComposerPackages, true);
                }
            } catch (Throwable $e) {
                $installedComposerPackages = [];
            }

            if (is_array($installedComposerPackages) && isset($installedComposerPackages['installed']) && count($installedComposerPackages['installed']) > 0) {
                foreach ($installedComposerPackages['installed'] as $pkg) {
                    if (isset($pkg['name'])) {
                        $installedComposerPackages[$pkg['name']] = $pkg;
                    }
                }
                unset($installedComposerPackages['installed']);
            }

            foreach ($composerJsonFile['require'] as $externalPackageName => $externalPackageversion) {
                $externalPackageNameArr = explode('/', (string) $externalPackageName);
                if (count($externalPackageNameArr) === 2) {
                    $desc = $installedComposerPackages[$externalPackageName]['description'] ?? '';
                    $version = $installedComposerPackages[$externalPackageName]['version'] ?? '';
                    $abandoned = (isset($installedComposerPackages[$externalPackageName]['abandoned']) && $installedComposerPackages[$externalPackageName]['abandoned'] === true) ? 1 : 0;
                    $patches = isset($composerJsonFile['extra']['patches'][$externalPackageName])
                        ? $helper->encode($composerJsonFile['extra']['patches'][$externalPackageName])
                        : $helper->encode([]);

                    $externalPackage = [
                        'developer'    => $externalPackageNameArr[0],
                        'name'         => $externalPackageNameArr[1],
                        'display_name' => $externalPackageName,
                        'description'  => $desc,
                        'module_type'  => 'externals',
                        'app_type'     => 'core',
                        'version'      => $version,
                        'patches'      => $patches,
                        'abandoned'    => $abandoned,
                        'installed'    => 1,
                        'required_by'  => $helper->encode(['packages' => [$coreId]]),
                        'updated_by'   => 0
                    ];

                    if ($db) {
                        $db->insertAsDict('modules_externals', $externalPackage);
                    }

                    if ($ff) {
                        $externalPackageStore = $ff->store('modules_externals');

                        $externalPackageStore->updateOrInsert($externalPackage);
                    }
                }
            }
        }
    }

    /**
     * Executes composer show -f json to introspect installed vendor packages.
     *
     * @return string|false JSON string or false on failure.
     */
    protected function executeComposer(): string|false
    {
        $infoFile = base_path('external/composer.info');

        try {
            putenv('COMPOSER_HOME=' . base_path('external/'));

            $stream = fopen($infoFile, 'w');
            $input = new StringInput('show -f json -d ' . base_path('external/'));
            $output = new StreamOutput($stream);

            $application = new ComposerApp();
            $application->setAutoExit(false);

            $app = $application->run($input, $output);
        } catch (Throwable $e) {
            return false;
        }

        if ($app !== 0) {
            return false;
        }

        $file = '';
        if (file_exists($infoFile)) {
            $handle = fopen($infoFile, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (!str_contains($line, '<warning>')) {
                        $file .= $line;
                    }
                }
                fclose($handle);
            }
        }

        return $file;
    }

    /**
     * Resolves the Core package ID from DB or FlatFile.
     *
     * @param mixed $db PDO database connection adapter.
     * @param mixed $ff FlatFile database manager.
     *
     * @return int Core package ID.
     */
    protected function getCoreId(mixed $db, mixed $ff): int
    {
        if ($db && method_exists($db, 'fetchAll')) {
            $core = $db->fetchAll(
                'SELECT * FROM modules_packages WHERE name = :name',
                Enum::FETCH_ASSOC,
                ['name' => 'Core']
            );

            if (isset($core[0]['id'])) {
                return (int) $core[0]['id'];
            }
        }

        if ($ff && method_exists($ff, 'store')) {
            $packageStore = $ff->store('modules_packages');
            $core = $packageStore?->findOneBy(['name', '=', 'Core']);

            if ($core && isset($core['id'])) {
                return (int) $core['id'];
            }
        }

        return 1;
    }
}