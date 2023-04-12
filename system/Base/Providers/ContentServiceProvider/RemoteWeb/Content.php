<?php

namespace System\Base\Providers\ContentServiceProvider\RemoteWeb;

use GuzzleHttp\Client;

class Content
{
    public function __construct()
    {
    }

    public function init()
    {
        return new Client();
    }
}