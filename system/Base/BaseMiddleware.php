<?php

namespace System\Base;

use Phalcon\Mvc\Controller;

abstract class BaseMiddleware extends Controller
{
    protected $application;

    public function onConstruct()
    {
        $this->application = $this->modules->applications->getApplicationInfo();
    }
}