<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Components
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Components;

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Response\Cookies;
use Phalcon\Mvc\View\Simple;
use System\Base\Installer\Components\Setup\AjaxHandler;
use System\Base\Installer\Components\Setup\InstallationRunner;
use System\Base\Installer\Components\Setup\PasswordChecker;
use System\Base\Installer\Components\Setup\ProgressManager;
use System\Base\Installer\Components\Setup\ViewHandler;
use System\Base\Installer\Packages\Setup as SetupPackage;
use System\Base\Providers\BasepackagesServiceProvider\Basepackages;
use System\Base\Providers\CacheServiceProvider\OpCache;
use System\Base\Providers\ContentServiceProvider\Local\Content as LocalContent;
use System\Base\Providers\ContentServiceProvider\RemoteWeb\Content as RemoteWebContent;
use System\Base\Providers\SecurityServiceProvider\Crypt;
use System\Base\Providers\SecurityServiceProvider\Random;
use System\Base\Providers\SecurityServiceProvider\Security;
use System\Base\Providers\SupportServiceProvider\Helper;
use System\Base\Providers\ValidationServiceProvider\Validation;
use System\Base\Providers\WebSocketServiceProvider\Wss;
use Throwable;

/**
 * Setup web controller component handling UI rendering, setup step dispatching,
 * AJAX validation, and progress reporting for platform installation.
 *
 * Sub-components (ViewHandler, AjaxHandler, ProgressManager, InstallationRunner,
 * PasswordChecker) are lazily instantiated on demand to optimize memory consumption
 * and speed up execution.
 */
class Setup
{
    /**
     * Dependency injection container.
     *
     * @var mixed
     */
    private mixed $container;

    /**
     * Setup package coordinator instance.
     *
     * @var SetupPackage|null
     */
    private ?SetupPackage $setupPackage = null;

    /**
     * View rendering instance.
     *
     * @var mixed
     */
    private mixed $view;

    /**
     * Flysystem local content adapter.
     *
     * @var mixed
     */
    private mixed $localContent = null;

    /**
     * Decoded core package.json content array.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $coreJson = null;

    /**
     * System configuration settings array.
     *
     * @var array<string, mixed>|null
     */
    protected ?array $config = null;

    /**
     * HTTP request service.
     *
     * @var mixed
     */
    protected mixed $request;

    /**
     * HTTP response service.
     *
     * @var mixed
     */
    protected mixed $response;

    /**
     * POST payload array.
     *
     * @var array<string, mixed>
     */
    protected array $postData = [];

    /**
     * Security service instance.
     *
     * @var mixed
     */
    protected mixed $security;

    /**
     * Random generator service instance.
     *
     * @var mixed
     */
    protected mixed $random;

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
     * Basepackages manager instance.
     *
     * @var mixed
     */
    protected mixed $basepackages;

    /**
     * Helpers manager instance.
     *
     * @var mixed
     */
    protected mixed $helper;

    /**
     * Progress tracker instance.
     *
     * @var mixed
     */
    protected mixed $progress = null;

    /**
     * Lazily loaded ViewHandler.
     *
     * @var ViewHandler|null
     */
    protected ?ViewHandler $viewHandler = null;

    /**
     * Lazily loaded AjaxHandler.
     *
     * @var AjaxHandler|null
     */
    protected ?AjaxHandler $ajaxHandler = null;

    /**
     * Lazily loaded ProgressManager.
     *
     * @var ProgressManager|null
     */
    protected ?ProgressManager $progressManager = null;

    /**
     * Lazily loaded InstallationRunner.
     *
     * @var InstallationRunner|null
     */
    protected ?InstallationRunner $installationRunner = null;

    /**
     * Lazily loaded PasswordChecker.
     *
     * @var PasswordChecker|null
     */
    protected ?PasswordChecker $passwordChecker = null;

    /**
     * Setup constructor.
     *
     * @param mixed $session      Active session service.
     * @param mixed $configsObj   Initial configs object.
     * @param bool  $onlyUpdateDb Whether only DB configuration is updated.
     *
     * @throws Throwable If non-class resolution exception occurs.
     */
    public function __construct(mixed $session, mixed $configsObj, bool $onlyUpdateDb = false, mixed $container = null)
    {
        try {
            if ($container === null) {
                $container = new FactoryDefault();

            $container->setShared(
                'view',
                function () {
                    $view = new Simple();

                    $view->setViewsDir(base_path('system/Base/Installer/View/'));

                    return $view;
                }
            );

            $container->setShared(
                'basepackages',
                function () {
                    return new Basepackages();
                }
            );

            $container->setShared(
                'validation',
                function () {
                    return (new Validation())->init();
                }
            );

            $container->setShared(
                'security',
                function () {
                    return (new Security())->init();
                }
            );

            $container->setShared(
                'crypt',
                function () {
                    return (new Crypt())->init();
                }
            );

            $container->setShared(
                'random',
                function () {
                    return (new Random())->init();
                }
            );

            $container->setShared(
                'cookies',
                function () {
                    return new Cookies();
                }
            );

            if (extension_loaded('Zend OPcache')) {
                $container->setShared(
                    'opCache',
                    function () {
                        return (new OpCache())->init();
                    }
                );
            } else {
                $container->setShared(
                    'opCache',
                    function () {
                        return false;
                    }
                );
            }

            $container->setShared(
                'helper',
                function () {
                    return (new Helper())->init();
                }
            );

            $container->setShared(
                'wss',
                function () use ($configsObj, $container) {
                    return (new Wss($configsObj, $container->getShared('helper')))->init();
                }
            );

            $container->setShared(
                'localContent',
                function () {
                    return (new LocalContent())->init();
                }
            );

            $container->setShared(
                'remoteWebContent',
                function () {
                    return (new RemoteWebContent())->init();
                }
            );

            $container->setShared('session', $session);
            }

            $this->container = $container;

            $this->response = $this->container->getShared('response');
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setHeader('Cache-Control', 'no-store');

            $this->request = $this->container->getShared('request');
            $this->postData = $this->request->getPost();

            $this->security = $this->container->getShared('security');
            $this->random = $this->container->getShared('random');
            $this->session = $this->container->getShared('session');
            $this->cookies = $this->container->getShared('cookies');

            $this->view = $this->container->getShared('view');

            $this->basepackages = $this->container->getShared('basepackages');

            $this->helper = $this->container->getShared('helper');

            if ($onlyUpdateDb === false) {
                $this->progress = $this->basepackages->progress->init($this->container, 'setup');
            }

            if (is_array($configsObj)) {
                $this->config = $configsObj;
            } elseif (is_object($configsObj)) {
                if (method_exists($configsObj, 'toArray')) {
                    try {
                        $this->config = (array) $configsObj->toArray();
                    } catch (\TypeError $e) {
                        $this->config = (array) $configsObj;
                    }
                } else {
                    $this->config = (array) $configsObj;
                }
            } else {
                $this->config = [];
            }

            $this->localContent = $this->container->getShared('localContent');
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'Class')) {
                if ($this->request->isGet()) {
                    $this->getViewHandler()->populateComposerJsonFile();
                }

                $this->getViewHandler()->renderView($this->postData, true);

                return;
            }

            throw $e;
        }
    }

    /**
     * Lazily gets ViewHandler instance.
     *
     * @return ViewHandler
     */
    public function getViewHandler(): ViewHandler
    {
        if ($this->viewHandler === null) {
            $this->viewHandler = new ViewHandler(
                $this->container,
                $this->view,
                $this->localContent,
                $this->helper,
                $this->request,
                $this->response,
                $this->session,
                $this->cookies,
                $this->security,
                $this->progress
            );
        }

        return $this->viewHandler;
    }

    /**
     * Lazily gets AjaxHandler instance.
     *
     * @return AjaxHandler
     */
    public function getAjaxHandler(): AjaxHandler
    {
        if ($this->ajaxHandler === null) {
            $this->ajaxHandler = new AjaxHandler(
                $this->view,
                $this->response,
                $this->random,
                $this->progress,
                $this->getPasswordChecker()
            );
        }

        return $this->ajaxHandler;
    }

    /**
     * Lazily gets ProgressManager instance.
     *
     * @return ProgressManager
     */
    public function getProgressManager(): ProgressManager
    {
        if ($this->progressManager === null) {
            $this->progressManager = new ProgressManager($this->progress);
        }

        return $this->progressManager;
    }

    /**
     * Lazily gets InstallationRunner instance.
     *
     * @return InstallationRunner
     */
    public function getInstallationRunner(): InstallationRunner
    {
        if ($this->installationRunner === null) {
            $this->installationRunner = new InstallationRunner(
                $this->container,
                $this->view,
                $this->response,
                $this->progress,
                $this->localContent,
                $this->helper,
                $this->getPasswordChecker()
            );
        }

        return $this->installationRunner;
    }

    /**
     * Lazily gets PasswordChecker instance.
     *
     * @return PasswordChecker
     */
    public function getPasswordChecker(): PasswordChecker
    {
        if ($this->passwordChecker === null) {
            $this->passwordChecker = new PasswordChecker();
        }

        return $this->passwordChecker;
    }

    /**
     * Executes the setup workflow or renders the view depending on request context.
     *
     * @param bool        $onlyUpdateDb Whether only database update should occur.
     * @param string|null $message      Optional view message string.
     *
     * @throws Throwable If dev mode installation exception occurs.
     *
     * @return mixed
     */
    public function run(bool $onlyUpdateDb = false, ?string $message = null): mixed
    {
        try {
            if (!isset($this->postData['session'])) {
                $this->setupPackage = new SetupPackage($this->container, $this->postData, false, $onlyUpdateDb);

                if (!$onlyUpdateDb) {
                    if ($this->progress->checkProgressFile()) {
                        $this->progress->deleteProgressFile();
                    }

                    $this->getProgressManager()->registerProgressMethods();
                }
            }
        } catch (Throwable $e) {
            if (str_contains($e->getMessage(), 'Class')) {
                if ($this->request->isGet()) {
                    $this->getViewHandler()->populateComposerJsonFile();
                }

                $this->getViewHandler()->renderView($this->postData, true);

                return null;
            }

            if (!$onlyUpdateDb && $this->progress) {
                $this->progress->preCheckComplete(false);

                $this->progress->resetProgress();
            }

            $this->view->responseCode = 1;

            $errMessage = $e->getMessage();

            if (str_contains($errMessage, 'SQLSTATE[HY000] [2002] No such file or directory')) {
                $errMessage = 'Database not available on entered Host : ' . $e->getMessage();
            } elseif (str_contains($errMessage, 'SQLSTATE[HY000] [2002] Connection refused')) {
                $errMessage = 'Database not available on entered Port : ' . $e->getMessage();
            } elseif (str_contains($errMessage, 'SQLSTATE[HY000] [1044]')) {
                $errMessage = 'Database not available on server : ' . $e->getMessage();
            } elseif (str_contains($errMessage, 'SQLSTATE[HY000] [1045]')) {
                $errMessage = 'Authentication Error : ' . $e->getMessage();
            }

            $this->view->responseMessage = $errMessage;

            if ($this->response->isSent() !== true) {
                $params = ($this->view && method_exists($this->view, 'getParamsToView')) ? $this->view->getParamsToView() : [];

                $this->response->setJsonContent($params);

                return $this->response->send();
            }
        }

        $isPost = $this->request && method_exists($this->request, 'isPost') && $this->request->isPost();

        if ($isPost && !isset($this->postData['session'])) {
            return $this->getInstallationRunner()->runInstallation(
                $this->postData,
                $onlyUpdateDb,
                $this->config,
                $this->setupPackage
            );
        } elseif ($isPost && isset($this->postData['session']) && !isset($this->postData['composer'])) {
            return $this->getAjaxHandler()->handle($this->postData);
        } elseif (isset($this->postData['composer'])) {
            $this->getViewHandler()->renderView($this->postData, true);
        } else {
            $this->getViewHandler()->renderView($this->postData, false, $onlyUpdateDb, $message);
        }

        return null;
    }

    /**
     * Checks password strength via PasswordChecker.
     *
     * @param string $pass Password string.
     *
     * @return int|false Score from 0 to 4, or false on error.
     */
    public function checkPwStrength(string $pass): int|false
    {
        return $this->getPasswordChecker()->checkPwStrength($pass);
    }
}