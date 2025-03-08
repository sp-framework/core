<?php

namespace System\Base\Installer\Packages\Setup\Register\Modules;

use Phalcon\Db\Enum;

class External
{
    public function register($db, $ff, $composerJsonFile, $helper)
    {
        if (isset($composerJsonFile['require']) && is_array($composerJsonFile['require']) && count($composerJsonFile) > 0) {
            $installedComposerPackages = $this->executeComposer();

            $coreId = $this->getCoreId($db, $ff);

            try {
                $installedComposerPackages = $helper->decode($installedComposerPackages, true);
            } catch (\throwable $e) {
                //Do nothing.
            }

            if (isset($installedComposerPackages['installed']) && count($installedComposerPackages['installed']) > 0) {
                array_walk($installedComposerPackages['installed'], function($installedComposerPackage, $index) use (&$installedComposerPackages) {
                    $installedComposerPackages[$installedComposerPackage['name']] = $installedComposerPackage;
                    unset($installedComposerPackages[$index]);
                });

                unset($installedComposerPackages['installed']);
            }

            foreach ($composerJsonFile['require'] as $externalPackageName => $externalPackageversion) {
                $externalPackageNameArr = explode('/', $externalPackageName);
                if (count($externalPackageNameArr) === 2) {
                    $externalPackage =
                        [
                            'developer'             => $externalPackageNameArr[0],
                            'name'                  => $externalPackageNameArr[1],
                            'display_name'          => $externalPackageName,
                            'description'           =>
                                (isset($installedComposerPackages[$externalPackageName]['description'])) ? $installedComposerPackages[$externalPackageName]['description'] : '',
                            'module_type'           => 'externals',
                            'app_type'              => 'core',
                            'version'               =>
                                (isset($installedComposerPackages[$externalPackageName]['version'])) ? $installedComposerPackages[$externalPackageName]['version'] : '',
                            'patches'               =>
                                (isset($composerJsonFile['extra']['patches'][$externalPackageName])) ?
                                    $helper->encode($composerJsonFile['extra']['patches'][$externalPackageName]) :
                                    $helper->encode([]),
                            'abandoned'             =>
                                (isset($installedComposerPackages[$externalPackageName]['abandoned'])) ? (($installedComposerPackages[$externalPackageName]['abandoned'] == true) ? 1 : 0) : 0,
                            'installed'             => 1,
                            'required_by'           => $helper->encode(['packages' => [$coreId]]),
                            'updated_by'            => 0
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

    protected function executeComposer()
    {
        try {
            putenv('COMPOSER_HOME=' . base_path('external/'));

            $stream = fopen(base_path('external/composer.info'), 'w');
            $input = new \Symfony\Component\Console\Input\StringInput('show -f json -d ' . base_path('external/'));
            $output = new \Symfony\Component\Console\Output\StreamOutput($stream);

            $application = new \Composer\Console\Application();
            $application->setAutoExit(false); // prevent `$application->run` method from exiting the script

            $app = $application->run($input, $output);
        } catch (\throwable $e) {
            throw $e;
        }

        if ($app !== 0) {
            return false;
        }

        $file = '';

        $handle = fopen(base_path('external/composer.info'), "r");

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (!str_contains($line, '<warning>')) {
                    $file .= $line;
                }
            }

            fclose($handle);
        }

        return $file;
    }

    protected function getCoreId($db, $ff)
    {
        if ($ff) {
            $modulesStore = $ff->store('modules_packages');

            $core = $modulesStore->findOneBy(['name', '=', 'Core']);

            if ($core) {
                return $core['id'];
            }
        }

        if ($db) {
            $core =
                $db->fetchAll(
                    "SELECT * FROM modules_packages WHERE name LIKE :name",
                    Enum::FETCH_ASSOC,
                    [
                        "name" => "Core",
                    ]
                );

            if ($core) {
                return $core[0]['id'];
            }
        }

        return 0;
    }
}