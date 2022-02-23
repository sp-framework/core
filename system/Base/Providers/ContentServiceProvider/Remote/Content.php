<?php

namespace System\Base\Providers\ContentServiceProvider\Remote;

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