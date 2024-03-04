<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Http\Response\Cookies as PhalconCookies;

class Cookies
{
    protected $response;

    protected $secTools;

    protected $cookies;

    public function __construct($response, $secTools)
    {
        $this->response = $response;

        $this->secTools = $secTools;
    }

    public function init()
    {
        $this->cookies = new PhalconCookies(true, $this->secTools->getCookiesSig());

        $this->response->setCookies($this->cookies);

        return $this->cookies;
    }
}