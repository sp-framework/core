<?php

namespace System\Base;

use Phalcon\Mvc\Controller;
use System\Base\Interfaces\MiddlewareInterface;

abstract class BaseMiddleware extends Controller implements MiddlewareInterface
{
    protected $app;

    public function onConstruct()
    {
        $this->app = $this->apps->getAppInfo();
    }
}