<?php

namespace System\Base\Providers;

use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\DispatcherInterface;

class MicroMiddlewaresServiceProvider extends Injectable
{
    public function beforeExecuteRoute(
        Event $event,
        $micro,
    ) {
        //Auth here
    }

    public function afterExecuteRoute(
        Event $event,
        $micro,
    ) {
        //
    }
}