<?php

namespace System\Base\Providers\ContentServiceProvider\RemoteWeb;

use GuzzleHttp\Client;

class Content
{
    protected $client;

    protected $options = [];

    public function __construct()
    {
    }

    public function init($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->client = new Client($this->options);

        return $this->client;
    }

    protected function setOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }
}