<?php

namespace System\Base;

use Phalcon\Mvc\Controller;

abstract class BaseMiddleware extends Controller
{
    protected $app;

    public function onConstruct()
    {
        $this->app = $this->apps->getAppInfo();
    }
}