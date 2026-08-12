<?php

/**
 * SP Framework
 *
 * @package   System\Base\Providers\ErrorServiceProvider
 * @copyright Copyright (c) 2026
 * @link      https://phpdoc.org/
 */

namespace System\Base\Providers\ErrorServiceProvider;

use System\Bootstrap;

/**
 * Class BootstrapExceptionHandler
 *
 * Handles uncaught exceptions and errors that occur during the application bootstrap
 * and request lifecycle at the root entry point level.
 *
 * Provides contextual exception handling across CLI, Micro/API, and MVC execution environments:
 * - CLI Context: Logs exception details, outputs stack traces to STDERR, and terminates with an error status code.
 * - API Context: Returns structured JSON error payloads or passes through to MicroExceptionHandler in debug mode.
 * - MVC Context: Forwards to registered error service providers, handles 404 AppNotFoundExceptions, or renders
 *   a secure, styled HTML debug trace table with XSS protection.
 *
 * @package System\Base\Providers\ErrorServiceProvider
 */
class BootstrapExceptionHandler
{
    /**
     * Dispatches the exception to the appropriate context-specific handler (CLI, API, or MVC).
     *
     * @param \Throwable      $exception The caught exception or error.
     * @param \System\Bootstrap|null $bootstrap Optional Bootstrap instance if initialized prior to the exception.
     *
     * @return mixed Context-dependent response (e.g. delegated return value, HTML output, or process termination).
     */
    public function handle(\Throwable $exception, ?Bootstrap $bootstrap = null)
    {
        if ($this->isCli()) {
            return $this->handleCli($exception, $bootstrap);
        }

        if (isset($bootstrap->isApi) && $bootstrap->isApi === true) {
            return $this->handleApi($exception, $bootstrap);
        }

        return $this->handleMvc($exception, $bootstrap);
    }

    /**
     * Alias for handle() to provide method signature compatibility across system exception handlers.
     *
     * @param \Throwable      $exception The caught exception or error.
     * @param \System\Bootstrap|null $bootstrap Optional Bootstrap instance if initialized prior to the exception.
     *
     * @return mixed Context-dependent response.
     */
    public function init(\Throwable $exception, ?Bootstrap $bootstrap = null)
    {
        return $this->handle($exception, $bootstrap);
    }

    /**
     * Checks whether the current execution context is CLI (Command Line Interface).
     *
     * @return bool True if running in CLI mode, false otherwise.
     */
    protected function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Terminates script execution with the specified exit status code.
     *
     * @param int $code Process exit status code (default: 0).
     *
     * @return void
     */
    protected function terminate(int $code = 0): void
    {
        exit($code);
    }

    /**
     * Writes an error message to the CLI error stream (STDERR) or standard output.
     *
     * @param string $message The formatted error string to output.
     *
     * @return void
     */
    protected function writeCliError(string $message): void
    {
        if (defined('STDERR') && is_resource(STDERR)) {
            fwrite(STDERR, $message);
        } else {
            echo $message;
        }
    }

    /**
     * Handles exceptions thrown during CLI execution.
     *
     * Flushes and commits any pending log entries, writes the error message and stack trace
     * to STDERR, and terminates the CLI process with exit code 1.
     *
     * @param \Throwable      $exception The uncaught exception.
     * @param \System\Bootstrap|null $bootstrap Optional Bootstrap instance.
     *
     * @return void
     */
    public function handleCli(\Throwable $exception, ?Bootstrap $bootstrap = null): void
    {
        if (isset($bootstrap->logger) && $bootstrap->logger) {
            $bootstrap->logger->commit();
        }

        $this->writeCliError('Error: ' . $exception->getMessage() . PHP_EOL);
        $this->writeCliError($exception->getTraceAsString() . PHP_EOL);

        $this->terminate(1);
    }

    /**
     * Handles exceptions thrown during API / Micro request lifecycle.
     *
     * Sets HTTP 500 response code. In debug mode with a valid response object, delegates to
     * MicroExceptionHandler. In production mode, returns a standardized JSON error message.
     *
     * @param \Throwable      $exception The uncaught exception.
     * @param \System\Bootstrap|null $bootstrap Optional Bootstrap instance.
     *
     * @throws \Throwable If debug mode is enabled and no response object is available.
     *
     * @return void
     */
    public function handleApi(\Throwable $exception, ?Bootstrap $bootstrap = null): void
    {
        http_response_code(500);

        if (!empty($bootstrap->config->debug) && isset($bootstrap->response) && $bootstrap->response) {
            (new MicroExceptionHandler())->init($exception, $bootstrap->logger ?? null, $bootstrap->response);
        } elseif (!empty($bootstrap->config->debug)) {
            throw $exception;
        } else {
            echo json_encode([
                'responseMessage' => 'Error! Please contact Administrator.',
                'responseCode'    => 1,
            ]);
        }

        if (isset($bootstrap->logger) && $bootstrap->logger) {
            $bootstrap->logger->commit();
        }

        $this->terminate(0);
    }

    /**
     * Handles exceptions thrown during full MVC request lifecycle.
     *
     * Delegates to the registered error provider if present. Returns HTTP 404 for AppNotFoundException.
     * Renders an HTML debug trace table if debug is enabled, or re-throws the exception in production.
     *
     * @param \Throwable      $exception The uncaught exception.
     * @param \System\Bootstrap|null $bootstrap Optional Bootstrap instance.
     *
     * @throws \Throwable Re-throws exception in production when debug mode is disabled.
     *
     * @return mixed
     */
    public function handleMvc(\Throwable $exception, ?Bootstrap $bootstrap = null)
    {
        if (isset($bootstrap->error) && $bootstrap->error) {
            return $bootstrap->error->handle($exception);
        }

        $class = (new \ReflectionClass($exception))->getShortName();

        if ($class === 'AppNotFoundException') {
            http_response_code(404);
            return;
        }

        $isDebug = !isset($bootstrap->config) || (!empty($bootstrap->config->debug));

        if ($isDebug) {
            $this->renderDebugHtml($exception);
        } else {
            throw $exception;
        }
    }

    /**
     * Renders a sanitized, styled HTML error table with stack trace for development/debug mode.
     *
     * @param \Throwable $exception The uncaught exception to format and display.
     *
     * @return void
     */
    public function renderDebugHtml(\Throwable $exception): void
    {
        $traces = [];

        foreach ($exception->getTrace() as $trace) {
            $file = $trace['file'] ?? null;
            $line = $trace['line'] ?? null;
            $className = $trace['class'] ?? '';
            $function = $trace['function'] ?? '';

            $call = $className ? "Class: {$className} - Function: {$function}" : "Function: {$function}";

            if ($file && $line) {
                $traces[] = "{$line} - {$file} - {$call}";
            } else {
                $traces[] = $call;
            }
        }

        echo '<style type="text/css">
            .tg {border-collapse:collapse;border-spacing:0;width:100%;}
            .tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
              font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
            .tg .tg-6kns{background-color:#fe0000;border-color:#333333;text-align:center;vertical-align:top}
            .tg .tg-orf0{font-family:"Arial Black", Gadget, sans-serif !important;text-align:left;vertical-align:top}
            .tg .tg-0lax{text-align:left;vertical-align:top}
        </style>
        <table class="tg">
            <thead>
                <tr>
                    <th class="tg-6kns" colspan="2">
                        <span style="font-weight:bold; text-align:center;">Exception Thrown: "' . htmlspecialchars(get_class($exception), ENT_QUOTES, 'UTF-8') . '"</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tg-orf0"><span>Info</span></td>
                    <td class="tg-0lax">' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                    <td class="tg-0lax"><span>File</span></td>
                    <td class="tg-0lax">' . htmlspecialchars($exception->getFile(), ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                    <td class="tg-0lax"><span>Line</span></td>
                    <td class="tg-0lax">' . (int) $exception->getLine() . '</td>
                </tr>
                <tr>
                    <td class="tg-0lax"><span>Trace</span></td>
                    <td class="tg-0lax">' . implode('<br>', array_map(fn($t) => htmlspecialchars($t, ENT_QUOTES, 'UTF-8'), array_reverse($traces))) . '</td>
                </tr>
            </tbody>
        </table>';
    }
}
