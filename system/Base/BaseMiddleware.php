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

    protected function logException($exception)
    {
        if ($this->config->logs->exceptions) {
            $this->logger->logExceptions->critical(json_trace($exception));
        }
    }
}