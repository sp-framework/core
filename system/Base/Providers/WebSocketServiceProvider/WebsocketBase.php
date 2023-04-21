<?php

namespace System\Base\Providers\WebSocketServiceProvider;

use System\Base\BasePackage;

class WebsocketBase extends BasePackage
{
    public $logger;

    public function setCliLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }
}