<?php

namespace System\Base\Providers\ApiServiceProvider;

use DateInterval;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use System\Base\Providers\ApiServiceProvider\Repositories\AccessTokenRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\AuthCodeRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\ClientRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\RefreshTokenRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\ScopeRepository;
use System\Base\Providers\ApiServiceProvider\Repositories\UserRepository;

class Api
{
    protected $apps;

    protected $request;

    protected $response;

    public $isApi;

    public $apiNeedsAuth;

    protected $server;

    protected $accessTokenRepository;

    protected $authCodeRepository;

    protected $clientRepository;

    protected $refreshTokenRepository;

    protected $scopeRepository;

    protected $userRepository;

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
        $this->request = $request;

        if (isset($this->isApi)) {
            return $this->isApi;
        }

        $this->isApi = false;

        $url = $this->request->getURI();

        $urlParts = explode("/", $url);

        if (isset($urlParts[1]) &&
            $urlParts[1] === 'api' &&
            $this->request->getBestAccept() === 'application/json'
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

    public function setup($apps)
    {
        $this->apps = $apps;

        $app = $this->apps->getAppInfo();

        if ($app['api_grant_type']) {
            if (!isset($this->{$app['api_grant_type']})) {
                if (method_exists($this, $grant = "init" . ucfirst("{$app['api_grant_type']}"))) {
                    $this->initApiServer();

                    $this->{$grant}();
                }
            }
        }
    }

    protected function initApiServer()
    {
        $this->accessTokenRepository = new AccessTokenRepository();
        $this->authCodeRepository = new AuthCodeRepository();
        $this->clientRepository = new ClientRepository();
        $this->refreshTokenRepository = new RefreshTokenRepository();
        $this->scopeRepository = new ScopeRepository();
        $this->userRepository = new UserRepository();
        // $deviceCodeRepository = new DeviceCodeRepository();

        $keys = $this->apps->getAPIKeys();

        $this->server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            new CryptKey($keys['private_location'], $keys['pki_passphrase']),
            $keys['enc']
        );
    }

    protected function initPassword()//Password Grant
    {
        $grant = new PasswordGrant(
            $this->userRepository,
            $this->refreshTokenRepository
        );

        $grant->setRefreshTokenTTL(new DateInterval('P1M'));// refresh tokens will expire after 1 month

        // Enable the password grant on the server with a token TTL of 1 hour
        $this->server->enableGrantType(
            $grant,
            new DateInterval('PT1H')// access tokens will expire after 1 hour
        );
    }

    protected function initClient_credentials()//Client Credentials Grant
    {
        // Enable the client credentials grant on the server
        $this->server->enableGrantType(
            new ClientCredentialsGrant(),
            new DateInterval('PT1H') // access tokens will expire after 1 hour
        );
    }

    // protected function initDcg()//Implemented in Oauth version 9.x
    // {
    //     // Enable the device code grant on the server with a token TTL of 1 hour
    //     $this->server->enableGrantType(
    //         new DeviceCodeGrant(
    //             $deviceCodeRepository,
    //             $refreshTokenRepository,
    //             new DateInterval('PT10M'),
    //             'http://foo/bar'
    //         ),
    //         new DateInterval('PT1H')
    //     );
    // }

    protected function initCode()//Authorization Code Grant
    {
        $grant = new AuthCodeGrant(
             $this->authCodeRepository,
             $this->refreshTokenRepository,
             new \DateInterval('PT10M') // authorization codes will expire after 10 minutes
         );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

        // Enable the authentication code grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );
    }

    protected function initRefresh_token()//Refresh Token Grant
    {
        $grant = new RefreshTokenGrant($this->refreshTokenRepository);
        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // new refresh tokens will expire after 1 month

        // Enable the refresh token grant on the server
        $this->server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // new access tokens will expire after an hour
        );
    }

    public function registerClient($data)
    {
        $serverResponse = new Response();

        try {
            // Try to respond to the access token request
            $token = $this->server->respondToAccessTokenRequest(ServerRequest::fromGlobals(), $serverResponse);

            var_dump($token);
        } catch (OAuthServerException $exception) {
            var_dump($exception);die();
            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($serverResponse);
        } catch (\Exception $exception) {
            var_dump($exception);die();
            // Catch unexpected exceptions
            $body = $serverResponse->getBody();
            $body->write($exception->getMessage());

            return $serverResponse->withStatus(500)->withBody($body);
        }
    }
}