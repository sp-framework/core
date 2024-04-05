<?php

namespace System\Base\Providers\ApiServiceProvider;

class Api
{
    public $isApi;

    public function __construct()
    {
        //
    }

    public function init()
    {
        return $this;
    }

    public function isApi($request)
    {
        if (isset($this->isApi)) {
            return $this->isApi;
        }

        $this->isApi = false;

        $url = $request->getURI();

        $urlParts = explode("/", $url);

        if (isset($urlParts[1]) &&
            $urlParts[1] === 'api' &&
            $request->getBestAccept() === 'application/json'
        ) {
            $this->isApi = true;
        }

        return $this->isApi;
    }
}