<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup;

use Composer\Console\Application as ComposerApp;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StreamOutput;
use Throwable;

/**
 * Executes Composer commands non-interactively to install external dependencies.
 */
class ComposerRunner
{
    /**
     * Executes composer install for external vendor dependencies.
     *
     * @throws Throwable If composer execution fails.
     *
     * @return bool True on success, false on non-zero exit code.
     */
    public function executeComposer(): bool
    {
        try {
            putenv('COMPOSER_HOME=' . base_path('external/'));

            $stream = fopen(base_path('external/composer.install'), 'w');
            $input = new StringInput('install -d ' . base_path('external/'));
            $output = new StreamOutput($stream);

            $application = new ComposerApp();
            $application->setAutoExit(false);

            $app = $application->run($input, $output);
        } catch (Throwable $e) {
            throw $e;
        }

        return ($app === 0);
    }
}
