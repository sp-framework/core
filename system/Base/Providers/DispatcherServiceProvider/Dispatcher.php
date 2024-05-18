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

    protected $appsInfo;

    protected $components;

    protected $helper;

    public function __construct($appsInfo, $events, $components, $helper)
    {
        $this->appsInfo = $appsInfo;

        $this->events = $events;

        $this->components = $components;

        $this->helper = $helper;

        $this->dispatcher = new PhalconDispatcher();

        $this->dispatcher->setControllerSuffix('Component');

        $this->dispatcher->setDefaultAction('view');

        if ($this->appsInfo) {
            $component = $this->components->getComponentById($this->appsInfo['errors_component']);

            if (isset($this->appsInfo['errors_component']) &&
                $this->appsInfo['errors_component'] != 0
            ) {
                $errorClassArr = explode('\\', $component['class']);
                unset($errorClassArr[$this->helper->lastKey($errorClassArr)]);
                $errorComponent = ucfirst($component['route']);
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