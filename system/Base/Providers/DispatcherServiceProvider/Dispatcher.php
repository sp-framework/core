<?php

namespace System\Base\Providers\DispatcherServiceProvider;

use Phalcon\Config\Adapter\Grouped;
use Phalcon\Di\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher as PhalconDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as PhalconDispatcherException;

class Dispatcher
{
    private $container;

    protected $dispatcher;

    public function __construct(DiInterface $container)
    {
        $this->container = $container;

        $this->dispatcher = new PhalconDispatcher();

        $this->dispatcher->setControllerSuffix('Component');

        $this->dispatcher->setDefaultAction('view');

        // $applicationInfo =
        //     $this->container->getShared('modules')->applications->getApplicationInfo();

        // if ($applicationInfo) {
        //     $applicationDefaults = json_decode($applicationInfo['settings'], true);

        //     if (isset($applicationDefaults['errorComponent'])) {
        //         $this->dispatcher->setEventsManager($this->register404());
        //     }
        // }
    }

    public function init()
    {
        return $this->dispatcher;
    }

    protected function register404()
    {
        $eventsManager = new Manager();

        $eventsManager->attach(
            'dispatch:beforeException',
            function (
                Event $event,
                $dispatcher,
                \Exception $exception
            ) {
                switch ($exception->getCode()) {
                    case PhalconDispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => 'Errors',
                                'action'     => 'controllerNotFound',
                            ]
                        );

                        return false;

                    case PhalconDispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => 'Errors',
                                'action'     => 'actionNotFound',
                            ]
                        );

                        return false;
                }
            }
        );

        return $eventsManager;

    }
}