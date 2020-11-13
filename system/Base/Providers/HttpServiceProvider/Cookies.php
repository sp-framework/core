<?php

namespace System\Base\Providers\HttpServiceProvider;

use Phalcon\Helper\Json;
use Phalcon\Http\Response\Cookies as PhalconCookies;

class Cookies
{
    protected $response;

    protected $crypt;

    protected $core;

    protected $random;

    protected $cookies;

    public function __construct($response, $crypt, $random, $core)
    {
        $this->response = $response;

        $this->crypt = $crypt;

        $this->random = $random;

        $this->core = $core;
    }

    public function init()
    {
        $coreSettings = Json::decode($this->core->core[0]['settings'], true);

        if (isset($coreSettings['sigKey']) &&
            isset($coreSettings['sigText']) &&
            isset($coreSettings['cookiesSig'])
        ) {
            $sigKey = $coreSettings['sigKey'];
            $sigText = $coreSettings['sigText'];
            $cookiesSig = $coreSettings['cookiesSig'];
        } else {
            $coreSettings['sigKey'] = $sigKey = $this->random->base58();
            $coreSettings['sigText'] = $sigText = $this->random->base58(32);
            $coreSettings['cookiesSig'] = $cookiesSig = $this->crypt->encryptBase64($sigText, $sigKey);
            $coreData = $this->core->core[0];
            $coreData['settings'] = Json::encode($coreSettings);
            $this->core->update($coreData);
        }

        $this->cookies = new PhalconCookies(true, $cookiesSig);

        $this->response->setCookies($this->cookies);

        return $this->cookies;
    }
}