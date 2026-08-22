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

use Exception;
use System\Base\Installer\Packages\Setup as SetupPackage;
use Throwable;

/**
 * Executes full framework installation workflow or database configuration updates.
 */
class InstallationRunner
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
     * Response service instance.
     *
     * @var mixed
     */
    protected mixed $response;

    /**
     * Progress service instance.
     *
     * @var mixed
     */
    protected mixed $progress;

    /**
     * Flysystem local file storage adapter.
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
     * Password checker instance.
     *
     * @var PasswordChecker
     */
    protected PasswordChecker $passwordChecker;

    /**
     * InstallationRunner constructor.
     *
     * @param mixed                $container       DI container.
     * @param mixed                $view            View service.
     * @param mixed                $response        Response service.
     * @param mixed                $progress        Progress tracker.
     * @param mixed                $localContent    Local file adapter.
     * @param mixed                $helper          Helpers service.
     * @param PasswordChecker|null $passwordChecker Password checker instance.
     */
    public function __construct(
        mixed $container,
        mixed $view,
        mixed $response,
        mixed $progress,
        mixed $localContent,
        mixed $helper,
        ?PasswordChecker $passwordChecker = null
    ) {
        $this->container = $container;
        $this->view = $view;
        $this->response = $response;
        $this->progress = $progress;
        $this->localContent = $localContent;
        $this->helper = $helper;
        $this->passwordChecker = $passwordChecker ?? new PasswordChecker();
    }

    /**
     * Runs installation or database update execution.
     *
     * @param array<string, mixed>      $postData     POST payload array.
     * @param bool                      $onlyUpdateDb Whether only DB is being updated.
     * @param array<string, mixed>|null $config       Configs array.
     * @param SetupPackage|null         $setupPackage Setup package instance.
     *
     * @throws Throwable If dev mode installation exception occurs.
     *
     * @return mixed Response send output or null.
     */
    public function runInstallation(
        array $postData,
        bool $onlyUpdateDb = false,
        ?array $config = null,
        ?SetupPackage $setupPackage = null
    ): mixed {
        if (isset($postData['dev']) && (string) $postData['dev'] !== 'true') {
            $passStrength = $this->passwordChecker->checkPwStrength((string) ($postData['pass'] ?? ''));

            if ($passStrength !== false && $passStrength <= 2) {
                if ($this->view) {
                    $this->view->responseCode = 1;
                    $this->view->responseMessage = 'User Password strength is weak!';
                }

                if ($this->progress) {
                    $this->progress->resetProgress();
                }

                return $this->sendResponse();
            }
        }

        if (!$onlyUpdateDb) {
            $validateData = $setupPackage ? $setupPackage->validateData() : true;

            if ($validateData !== true) {
                if ($this->progress) {
                    $this->progress->preCheckComplete(false);
                }

                if ($this->view) {
                    $this->view->responseCode = 1;
                    $this->view->responseMessage = $validateData;
                }

                return $this->sendResponse();
            }

            $createNewDb = true;
            $createNewUser = true;

            if (!isset($postData['create-db']) || (isset($postData['create-db']) && (string) $postData['create-db'] === 'false')) {
                if ($this->progress) {
                    $this->progress->unregisterMethods(['createNewDb']);
                }
                $createNewDb = false;
            }

            if (!isset($postData['create-user']) || (isset($postData['create-user']) && (string) $postData['create-user'] === 'false')) {
                if ($this->progress) {
                    $this->progress->unregisterMethods(['createNewUser']);
                }
                $createNewUser = false;
            }

            if ($createNewDb || $createNewUser) {
                if (isset($postData['create-username'], $postData['create-password'])) {
                    if ($this->progress) {
                        $this->progress->preCheckComplete();
                    }

                    try {
                        if ($createNewDb && $setupPackage) {
                            $setupPackage->createNewDb();
                        }
                        if ($createNewUser && $setupPackage) {
                            $setupPackage->createNewUser();
                        }

                        unset($postData['create-username'], $postData['create-password']);
                        $setupPackage = new SetupPackage($this->container, $postData, false, $onlyUpdateDb);
                    } catch (Exception $e) {
                        if ($this->progress) {
                            $this->progress->preCheckComplete(false);
                            $this->progress->resetProgress();
                        }

                        if ($this->view) {
                            $this->view->responseCode = 1;
                            $this->view->responseMessage = $e->getMessage();
                        }

                        return $this->sendResponse();
                    }
                } else {
                    if ($this->progress) {
                        $this->progress->resetProgress();
                    }

                    if ($this->view) {
                        $this->view->responseCode = 1;
                        $this->view->responseMessage = 'Database username and password with create permission not provided.';
                    }

                    return $this->sendResponse();
                }
            }
        }

        $corePkgPath = 'system/Base/Installer/Packages/Setup/Register/Modules/Packages/Providers/Core/package.json';
        $coreJson = null;
        if ($this->localContent && method_exists($this->localContent, 'read')) {
            $coreJson = $this->helper->decode($this->localContent->read($corePkgPath), true);
        }

        if ($onlyUpdateDb) {
            if ($config && is_array($coreJson)) {
                $coreJson['settings'] = array_replace($coreJson['settings'] ?? [], $config);
            }

            if ($setupPackage) {
                $setupPackage->writeConfigs($coreJson, true, true);
            }

            if ($this->view) {
                $this->view->responseCode = 0;
                $this->view->responseMessage = 'Configuration Updated.';
            }

            return $this->sendResponse();
        }

        try {
            if ($setupPackage && !$setupPackage->checkDbEmpty()) {
                if ($this->view) {
                    $this->view->responseCode = 1;
                    $dbname = $postData['dbname'] ?? 'sp';
                    $this->view->responseMessage = 'Database <strong>' . $dbname . '</strong> not empty! Use drop existing tables checkbox to drop existing tables.';
                }

                if ($this->progress) {
                    $this->progress->resetProgress();
                }

                return $this->sendResponse();
            }

            if ($this->progress) {
                $this->progress->preCheckComplete();
            }

            if ($setupPackage) {
                $setupPackage->buildSchema();
                $setupPackage->registerRepos();
                $setupPackage->registerDomain();

                $baseConfig = $setupPackage->writeBaseConfigs($coreJson);

                $setupPackage->registerCore($baseConfig ?? []);
                $setupPackage->registerCoreAppType();
                $setupPackage->registerCoreApp();

                $setupPackage->registerModule('components');
                $setupPackage->updateCoreAppComponents();
                $setupPackage->registerModule('packages');
                $setupPackage->registerModule('middlewares');
                $setupPackage->registerModule('views');
                $setupPackage->registerModule('externals');

                $setupPackage->registerCoreRole();
                $setupPackage->registerAdditionalRoles();

                $workFactor = $baseConfig['settings']['security']['passwordWorkFactor'] ?? 12;
                $setupPackage->registerCoreAccount((int) $workFactor);
                $setupPackage->registerCoreProfile();
                $setupPackage->registerExcludeAutoGeneratedFilters();
                $setupPackage->processGeoData();
                $setupPackage->registerWorkers();
                $setupPackage->registerSchedules();
                $setupPackage->registerTasks();
                $setupPackage->performIndexing();

                $setupPackage->cleanOldAPIKeys();
                $setupPackage->cleanVar();
                $setupPackage->cleanOldBackups();
                $setupPackage->cleanOldCookies();

                $setupPackage->writeConfigs(null, true);
            }

            if ($this->view) {
                $this->view->responseCode = 0;
                $this->view->responseMessage = 'Framework installed.';
            }

            return $this->sendResponse();
        } catch (Exception $e) {
            if ($this->progress) {
                $this->progress->resetProgress();
            }

            if ($setupPackage) {
                $setupPackage->revertBaseConfig();
            }

            if (isset($postData['dev']) && (string) $postData['dev'] === 'true') {
                throw $e;
            }

            if ($this->view) {
                $this->view->responseCode = 1;
                $this->view->responseMessage = 'Framework installation error. Contact developers.';
            }

            return $this->sendResponse();
        }
    }

    /**
     * Sends view parameters as JSON response.
     *
     * @return mixed
     */
    protected function sendResponse(): mixed
    {
        if ($this->response && method_exists($this->response, 'isSent') && $this->response->isSent() !== true) {
            $params = ($this->view && method_exists($this->view, 'getParamsToView')) ? $this->view->getParamsToView() : [];
            $this->response->setJsonContent($params);

            return $this->response->send();
        }

        return null;
    }
}
