<?php

namespace System\Base\Providers\DispatcherServiceProvider;

use Phalcon\Config\Adapter\Grouped;
use Phalcon\Di\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use Phalcon\Helper\Arr;
use Phalcon\Mvc\Dispatcher as PhalconDispatcher;
use Phalcon\Mvc\Dispatcher\Exception as PhalconDispatcherException;

class Dispatcher
{
    protected $dispatcher;

    protected $events;

    protected $applicationsInfo;

    protected $components;

    protected $router;

    public function __construct($applicationsInfo, $config, $events, $components, $router)
    {
        $this->applicationsInfo = $applicationsInfo;

        $this->events = $events;

        $this->components = $components;

        $this->router = $router;

        $this->dispatcher = new PhalconDispatcher();

        $this->dispatcher->setControllerSuffix('Component');

        $this->dispatcher->setDefaultAction('view');

        if ($this->applicationsInfo) {
            if (isset($this->applicationsInfo['errors_component']) &&
                $this->applicationsInfo['errors_component'] != 0
            ) {
                $errorClassArr = explode('\\', $this->components->getById($this->applicationsInfo['errors_component'])['class']);
                unset($errorClassArr[Arr::lastKey($errorClassArr)]);
                $errorComponent = ucfirst($this->components->getById($this->applicationsInfo['errors_component'])['route']);
                $namespace = implode('\\', $errorClassArr);
            } else {
                $errorComponent = 'Errors';
                $namespace = 'System\Base\Providers\ErrorServiceProvider';
            }
            $this->dispatcher->setEventsManager($this->register404($errorComponent, $namespace));
        } else {
            $this->dispatcher->setEventsManager($this->events);//Register Other events
        }
    }

    public function init()
    {
        return $this->dispatcher;
    }

    protected function register404($errorComponent, $namespace)
    {
        $this->events->attach(
            'dispatch:beforeException',
            function (
                Event $event,
                $dispatcher,
                \Exception $exception
            ) use ($errorComponent, $namespace) {
                switch ($exception->getCode()) {
                    case PhalconDispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => $errorComponent,
                                'action'     => 'controllerNotFound',
                                'namespace'  => $namespace
                            ]
                        );

                        return false;

                    case PhalconDispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            [
                                'controller' => $errorComponent,
                                'action'     => 'actionNotFound',
                                'namespace'  => $namespace
                            ]
                        );

                        return false;
                }
            }
        );

        return $this->events;
    }
}