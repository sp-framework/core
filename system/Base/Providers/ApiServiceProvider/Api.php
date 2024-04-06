<?php

namespace System\Base\Providers\ApiServiceProvider;

use League\OAuth2\Server\AuthorizationServer;

class Api
{
    public $isApi;

    public $apiNeedsAuth;

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

    public function apiNeedsAuth($appSettings = [])
    {
        if (count($appSettings) === 0) {
            return false;
        }

        if (isset($this->apiNeedsAuth)) {
            return $this->apiNeedsAuth;
        }

        $this->apiNeedsAuth = false;

        var_Dump($appSettings);
        if (isset($appSettings['api']['enabled']) && $appSettings['api']['enabled'] == true) {

        }


        return $this->apiNeedsAuth;
    }

    public function setup($app)
    {
        if ($app['api_grant_type']) {
            if (!isset($this->{$app['api_grant_type']})) {
                if (method_exists($this, $method = "init" . ucfirst("{$app['api_grant_type']}"))) {
                    $this->{$method}();
                }
            }
        }
    }

    protected function initPg()
    {
        $clientRepository = new ClientRepository();
        $scopeRepository = new ScopeRepository();
        $accessTokenRepository = new AccessTokenRepository();
        $userRepository = new UserRepository();
        $refreshTokenRepository = new RefreshTokenRepository();

        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );

        var_dump($server);
    }
}