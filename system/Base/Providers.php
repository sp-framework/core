<?php

/**
 * SP Framework
 *
 * Dependency Injection Service Providers Configuration.
 * Defines the registration map of service providers across execution modes:
 * - 'mvc': Standard Model-View-Controller full-stack web application context.
 * - 'cli': Command Line Interface / console task execution context.
 * - 'api': Micro RESTful API / micro-service execution context.
 *
 * @package   System\Base
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

use System\Base\Providers\AccessServiceProvider;
use System\Base\Providers\AnnotationsServiceProvider;
use System\Base\Providers\AppsServiceProvider;
use System\Base\Providers\BasepackagesServiceProvider;
use System\Base\Providers\CacheServiceProvider;
use System\Base\Providers\ConfigServiceProvider;
use System\Base\Providers\ContentServiceProvider;
use System\Base\Providers\CoreServiceProvider;
use System\Base\Providers\DatabaseServiceProvider;
use System\Base\Providers\DispatcherServiceProvider;
use System\Base\Providers\DomainsServiceProvider;
use System\Base\Providers\ErrorServiceProvider;
use System\Base\Providers\EventsServiceProvider;
use System\Base\Providers\FlashServiceProvider;
use System\Base\Providers\HttpServiceProvider;
use System\Base\Providers\LoggerServiceProvider;
use System\Base\Providers\ModulesServiceProvider;
use System\Base\Providers\RouterServiceProvider;
use System\Base\Providers\SecurityServiceProvider;
use System\Base\Providers\SessionServiceProvider;
use System\Base\Providers\SupportServiceProvider;
use System\Base\Providers\TerminalServiceProvider;
use System\Base\Providers\ValidationServiceProvider;
use System\Base\Providers\ViewServiceProvider;
use System\Base\Providers\WebSocketServiceProvider;

return [
    /**
     * MVC Web Application Service Providers.
     * Full web application lifecycle stack including views, sessions, routing, dispatching, and security.
     */
    'mvc' => [
        ConfigServiceProvider::class,
        SupportServiceProvider::class,
        EventsServiceProvider::class,
        AnnotationsServiceProvider::class,
        SecurityServiceProvider::class,
        DatabaseServiceProvider::class,
        HttpServiceProvider::class,
        CacheServiceProvider::class,
        BasepackagesServiceProvider::class,
        CoreServiceProvider::class,
        AppsServiceProvider::class,
        DomainsServiceProvider::class,
        ModulesServiceProvider::class,
        LoggerServiceProvider::class,
        ContentServiceProvider::class,
        RouterServiceProvider::class,
        DispatcherServiceProvider::class,
        ViewServiceProvider::class,
        FlashServiceProvider::class,
        AccessServiceProvider::class,
        ErrorServiceProvider::class,
        ValidationServiceProvider::class,
        WebSocketServiceProvider::class,
    ],

    /**
     * CLI Console Application Service Providers.
     * Command-line task runner stack including terminal services, database, background workers, and caching.
     */
    'cli' => [
        ConfigServiceProvider::class,
        SupportServiceProvider::class,
        EventsServiceProvider::class,
        HttpServiceProvider::class,
        SecurityServiceProvider::class,
        SessionServiceProvider::class,
        DatabaseServiceProvider::class,
        CacheServiceProvider::class,
        BasepackagesServiceProvider::class,
        CoreServiceProvider::class,
        AppsServiceProvider::class,
        DomainsServiceProvider::class,
        ModulesServiceProvider::class,
        LoggerServiceProvider::class,
        ContentServiceProvider::class,
        WebSocketServiceProvider::class,
        ValidationServiceProvider::class,
        AccessServiceProvider::class,
        TerminalServiceProvider::class,
    ],

    /**
     * RESTful / Micro API Service Providers.
     * Optimized, lightweight stack for stateless JSON / REST API endpoints and external API clients.
     */
    'api' => [
        ConfigServiceProvider::class,
        DatabaseServiceProvider::class,
        HttpServiceProvider::class,
        ContentServiceProvider::class,
        BasepackagesServiceProvider::class,
        RouterServiceProvider::class,
        DomainsServiceProvider::class,
        AppsServiceProvider::class,
        CoreServiceProvider::class,
        CacheServiceProvider::class,
        SupportServiceProvider::class,
        SecurityServiceProvider::class,
        LoggerServiceProvider::class,
        AccessServiceProvider::class,
        ModulesServiceProvider::class,
        ValidationServiceProvider::class,
    ],
];
