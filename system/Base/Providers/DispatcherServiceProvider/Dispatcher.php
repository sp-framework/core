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
    protected $dispatcher;

    protected $events;

    protected $applicationsInfo;

    public function __construct($applicationsInfo, $config, $events)
    {
        $this->applicationsInfo = $applicationsInfo;

        $this->events = $events;

        $this->dispatcher = new PhalconDispatcher();

        $this->dispatcher->setControllerSuffix('Component');

        $this->dispatcher->setDefaultAction('view');

        if ($this->applicationsInfo && !$config->debug) {
            $applicationDefaults = json_decode($applicationsInfo['settings'], true);

            if (isset($applicationDefaults['errorComponent'])) {
                $this->dispatcher->setEventsManager(
                    $this->register404(
                        $applicationDefaults['errorComponent']
                    )
                );
            }
        } else {
            $this->dispatcher->setEventsManager($this->events);//Register Other events
        }
    }

    public function init()
    {
        return $this->dispatcher;
    }

    protected function register404($errorComponent)
    {
        $this->events->attach(
            'dispatch:beforeException',
            function (
                Event $event,
                $dispatcher,
                \Exception $exception
            ) use ($errorComponent) {

                switch ($exception->getCode()) {
                    case PhalconDispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => $errorComponent,
                                'action'     => 'controllerNotFound',
                            ]
                        );

                        return false;

                    case PhalconDispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => $errorComponent,
                                'action'     => 'actionNotFound',
                            ]
                        );

                        return false;
                }
            }
        );

        return $this->events;
    }
}