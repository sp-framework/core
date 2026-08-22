<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Components\Setup
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Components\Setup;

use System\Base\Installer\Packages\Setup as SetupPackage;
use Throwable;

/**
 * Handles HTML view rendering, composer synchronization, requirements check, and template payload loading.
 */
class ViewHandler
{
    /**
     * Dependency injection container.
     *
     * @var mixed
     */
    protected mixed $container;

    /**
     * View rendering instance.
     *
     * @var mixed
     */
    protected mixed $view;

    /**
     * Flysystem local content adapter.
     *
     * @var mixed
     */
    protected mixed $localContent;

    /**
     * Helpers manager instance.
     *
     * @var mixed
     */
    protected mixed $helper;

    /**
     * Request service instance.
     *
     * @var mixed
     */
    protected mixed $request;

    /**
     * Response service instance.
     *
     * @var mixed
     */
    protected mixed $response;

    /**
     * Session manager instance.
     *
     * @var mixed
     */
    protected mixed $session;

    /**
     * Cookies manager instance.
     *
     * @var mixed
     */
    protected mixed $cookies;

    /**
     * Security service instance.
     *
     * @var mixed
     */
    protected mixed $security;

    /**
     * Progress service instance.
     *
     * @var mixed
     */
    protected mixed $progress;

    /**
     * ViewHandler constructor.
     *
     * @param mixed $container    DI container.
     * @param mixed $view         View service.
     * @param mixed $localContent Local storage adapter.
     * @param mixed $helper       Helpers service.
     * @param mixed $request      Request instance.
     * @param mixed $response     Response instance.
     * @param mixed $session      Session instance.
     * @param mixed $cookies      Cookies instance.
     * @param mixed $security     Security instance.
     * @param mixed $progress     Progress tracker instance.
     */
    public function __construct(
        mixed $container,
        mixed $view,
        mixed $localContent,
        mixed $helper,
        mixed $request,
        mixed $response,
        mixed $session,
        mixed $cookies,
        mixed $security,
        mixed $progress = null
    ) {
        $this->container = $container;
        $this->view = $view;
        $this->localContent = $localContent;
        $this->helper = $helper;
        $this->request = $request;
        $this->response = $response;
        $this->session = $session;
        $this->cookies = $cookies;
        $this->security = $security;
        $this->progress = $progress;
    }

    /**
     * Renders installer setup view template or processes external composer installer actions.
     *
     * @param array<string, mixed> $postData     Request POST payload.
     * @param bool                 $precheckFail Whether requirements failed.
     * @param bool                 $onlyUpdateDb Whether only DB is being updated.
     * @param string|null          $message      Optional info message.
     *
     * @return void
     */
    public function renderView(
        array $postData,
        bool $precheckFail = false,
        bool $onlyUpdateDb = false,
        ?string $message = null
    ): void {
        if ($precheckFail) {
            $isPost = $this->request && method_exists($this->request, 'isPost') && $this->request->isPost();

            if ($isPost) {
                if (isset($postData['composer'])) {
                    $callResult = ($this->progress && method_exists($this->progress, 'getCallResult'))
                        ? $this->progress->getCallResult('executeComposer')
                        : null;
                    $progress = [];

                    if ($callResult === false) {
                        $progress['composer_error'] = true;
                    } elseif ($this->progress && isset($postData['session'])) {
                        $progress = (array) $this->progress->getProgress((string) $postData['session'], true);
                    }

                    $installLogPath = base_path('external/composer.install');
                    if (file_exists($installLogPath)) {
                        $composerInstall = (string) file_get_contents($installLogPath);
                        $progress['composer'] = $composerInstall;

                        if (str_contains($composerInstall, 'curl error') || str_contains($composerInstall, 'requirements could not be resolved')) {
                            $progress['composer_error'] = true;
                        }

                        if ($this->view) {
                            $this->view->responseCode = $callResult ? 0 : 1;
                            $this->view->responseMessage = $callResult
                                ? 'External packages installation success!'
                                : 'External packages installation error!';
                        }
                    }

                    if ($this->view) {
                        $this->view->responseData = $progress;
                    }
                } else {
                    $setupPackage = new SetupPackage($this->container, $postData, $precheckFail);
                    $setupPackage->executeComposer();

                    do {
                        $callResult = ($this->progress && method_exists($this->progress, 'getCallResult'))
                            ? $this->progress->getCallResult('executeComposer')
                            : true;

                        if ($callResult === false) {
                            if ($this->view) {
                                $this->view->responseCode = 3;
                                $this->view->responseMessage = 'External packages installation error!';
                            }

                            if ($this->response && method_exists($this->response, 'isSent') && $this->response->isSent() !== true) {
                                $params = ($this->view && method_exists($this->view, 'getParamsToView')) ? $this->view->getParamsToView() : [];
                                $this->response->setJsonContent($params);
                                $this->response->send();
                                return;
                            }
                        } else {
                            if ($this->view) {
                                $this->view->responseCode = 0;
                                $this->view->responseMessage = 'External packages installation success!';
                            }
                        }

                        if ($callResult === null) {
                            sleep(1);
                        }
                    } while ($callResult === null);
                }

                if ($this->response && method_exists($this->response, 'setContentType')) {
                    $this->response->setContentType('application/json', 'UTF-8');
                    $this->response->setHeader('Cache-Control', 'no-store');
                }

                if ($this->response && method_exists($this->response, 'isSent') && $this->response->isSent() !== true) {
                    $params = ($this->view && method_exists($this->view, 'getParamsToView')) ? $this->view->getParamsToView() : [];
                    $this->response->setJsonContent($params);
                    $this->response->send();
                    return;
                }
            } else {
                if ($this->progress) {
                    if ($this->progress->checkProgressFile()) {
                        $this->progress->deleteProgressFile();
                    }

                    $this->progress->registerMethods([
                        ['method' => 'executeComposer', 'text' => 'Downloading & installing external packages...']
                    ]);
                    $this->progress->preCheckComplete();
                }
            }
        }

        $host = ($this->request && method_exists($this->request, 'getHttpHost')) ? $this->request->getHttpHost() : 'localhost';
        $sessionId = ($this->session && method_exists($this->session, 'getId')) ? $this->session->getId() : '';

        if ($this->cookies && method_exists($this->cookies, 'useEncryption')) {
            $this->cookies->useEncryption(false);
            $this->cookies->set('Installer', $sessionId, time() + 600, '/', false, $host, true, ['samesite' => 'Strict']);
            $this->cookies->send();
            $this->cookies->useEncryption(true);
        }

        if (!$precheckFail && $this->view && $this->localContent && $this->helper) {
            $countriesFile = '/system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/AllCountries.json';
            if ($this->localContent->fileExists($countriesFile)) {
                $this->view->countries = $this->helper->decode($this->localContent->read($countriesFile), true);
            }

            $timezonesFile = '/system/Base/Providers/BasepackagesServiceProvider/Packages/DataExtractors/Geo/TimeZones.json';
            if ($this->localContent->fileExists($timezonesFile)) {
                $this->view->timezones = $this->helper->decode($this->localContent->read($timezonesFile), true);
            }

            $coreJsonPath = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json';
            if ($this->localContent->fileExists($coreJsonPath)) {
                $this->view->coreJson = $this->helper->decode($this->localContent->read($coreJsonPath), true);
            }
        }

        $viewService = (is_object($this->container) && method_exists($this->container, 'getShared'))
            ? $this->container->getShared('view')
            : null;

        if ($viewService && method_exists($viewService, 'render')) {
            echo $viewService->render('setup', [
                'precheckFail' => $precheckFail,
                'onlyUpdateDb' => $onlyUpdateDb,
                'message'      => $message,
                'request'      => $this->request,
                'security'     => $this->security,
                'session'      => $this->session,
            ]);
        }
    }

    /**
     * Synchronizes external/composer.json with core dependencies.
     *
     * @return void
     */
    public function populateComposerJsonFile(): void
    {
        if ($this->view) {
            $this->view->responseCode = 0;
        }

        $lockPath = base_path('external/composer.lock');
        if (file_exists($lockPath)) {
            unlink($lockPath);
        }

        $composerPath = base_path('external/composer.json');
        $coreJsonPath = base_path('system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json');

        if (!file_exists($composerPath) || !file_exists($coreJsonPath)) {
            return;
        }

        try {
            $composerJsonFile = $this->helper->decode((string) file_get_contents($composerPath), true);
            $coreJsonFile = $this->helper->decode((string) file_get_contents($coreJsonPath), true);

            if (isset($coreJsonFile['dependencies']['composer']['require'])) {
                foreach ($coreJsonFile['dependencies']['composer']['require'] as $composer => $version) {
                    if (!isset($composerJsonFile['require'][$composer])) {
                        $composerJsonFile['require'][$composer] = $version;
                    }
                }
            }

            if (isset($coreJsonFile['dependencies']['composer']['config'])) {
                $composerJsonFile['config'] = $coreJsonFile['dependencies']['composer']['config'];
            }

            if (isset($coreJsonFile['dependencies']['composer']['extra'])) {
                $composerJsonFile['extra'] = $coreJsonFile['dependencies']['composer']['extra'];
            }

            file_put_contents($composerPath, (string) $this->helper->encode($composerJsonFile, JSON_PRETTY_PRINT));
        } catch (Throwable $exception) {
            if ($this->view) {
                $this->view->responseCode = 1;
                $this->view->responseMessage = 'Error reading composer or Core JSON file. Please download Core again from repository.';
            }
        }
    }
}
