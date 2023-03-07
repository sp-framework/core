<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetAppTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\RefreshUserTokenRestRequest;
use Apps\Dash\Packages\System\Api\Base\ConfigurationResolver;
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;
use Apps\Dash\Packages\System\Api\Base\UriResolver;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class OAuthService
{
    const API_VERSION = 'v1';

    protected static $endPoints = [
        'sandbox'    => 'https://api.sandbox.ebay.com/identity',
        'production' => 'https://api.ebay.com/identity'
    ];

    protected static $operations = [
        'getUserToken' => [
            'method' => 'POST',
            'resource' => 'oauth2/token',
            'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetUserTokenRestResponse',
            'params' => [
            ]
        ],
        'refreshUserToken' => [
            'method' => 'POST',
            'resource' => 'oauth2/token',
            'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\RefreshUserTokenRestResponse',
            'params' => [
            ]
        ],
        'getAppToken' => [
            'method' => 'POST',
            'resource' => 'oauth2/token',
            'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\OAuth\Types\GetAppTokenRestResponse',
            'params' => [
            ]
        ]
    ];

    protected $resolver;

    protected $uriResolver;

    protected static $config;

    protected static $sandbox;

    protected static $debug;

    protected static $credentials;

    public function __construct(array $config)
    {
        self::$credentials = $config['credentials'];

        self::$sandbox = $config['sandbox'];

        self::$debug = $config['debug'];

        $this->resolver = new ConfigurationResolver(static::getConfigDefinitions());

        $this->uriResolver = new UriResolver();

        self::$config = $this->resolver->resolve($config);
    }

    public static function getConfigDefinitions()
    {
        return [
            'apiVersion'    => [
                'valid'     => ['string'],
                'default'   => self::API_VERSION,
                'required'  => true
            ],
            'credentials'   => [
                'valid'     => ['array'],
                'default'   => self::$credentials
            ],
            'debug'         => [
                'valid'     => ['bool', 'array'],
                'fn'        => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayFunctions::applyDebug',
                'default'   => self::$debug
            ],
            'httpHandler'   => [
                'valid'     => ['callable'],
                'default'   => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayFunctions::defaultHttpHandler'
            ],
            'httpOptions'   => [
                'valid'     => ['array'],
                'default'   => [
                    'http_errors' => false
                ]
            ],
            'ruName'        => [
                'valid'     => ['string'],
                'default'   => self::$credentials['ruName'],
                'required'  => true
            ],
            'sandbox'       => [
                'valid'     => ['bool'],
                'default'   => self::$sandbox
            ]
        ];
    }

    public function redirectUrlForUser(array $params)
    {
        if (!array_key_exists('state', $params)) {
            throw new \InvalidArgumentException('state parameter required');
        }

        if (!array_key_exists('scope', $params)) {
            throw new \InvalidArgumentException('scope parameter required');
        }

        $url = self::$config['sandbox']
            ? 'https://auth.sandbox.ebay.com/oauth2/authorize?'
            : 'https://auth.ebay.com/oauth2/authorize?';

        $urlParams = [
            'client_id'     => self::$config['credentials']['appId'],
            'redirect_uri'  => self::$config['ruName'],
            'response_type' => 'code',
            'state'         => $params['state'],
            'scope'         => implode(' ', $params['scope']),
            'prompt'        => 'login'
        ];

        return $url.http_build_query($urlParams, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * @param GetUserTokenRestRequest $request
     * @return GetUserTokenRestResponse
     */
    public function getUserToken(GetUserTokenRestRequest $request)
    {
        return $this->getUserTokenAsync($request)->wait();
    }

    /**
     * @param GetUserTokenRestRequest $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getUserTokenAsync(GetUserTokenRestRequest $request)
    {
        if (!$request) {
            $request = new GetUserTokenRestRequest();
        }
        if (!isset($request->grant_type)) {
            $request->grant_type = 'authorization_code';
        }
        if (!isset($request->redirect_uri)) {
            $request->redirect_uri = self::$config['ruName'];
        }

        return $this->callOperationAsync('getUserToken', $request);
    }

    /**
     * @param RefreshUserTokenRestRequest $request
     * @return RefreshUserTokenRestResponse
     */
    public function refreshUserToken(RefreshUserTokenRestRequest $request)
    {
        return $this->refreshUserTokenAsync($request)->wait();
    }

    /**
     * @param RefreshUserTokenRestRequest $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function refreshUserTokenAsync(RefreshUserTokenRestRequest $request)
    {
        if (!$request) {
            $request = new RefreshUserTokenRestRequest();
        }
        if (!isset($request->grant_type)) {
            $request->grant_type = 'refresh_token';
        }

        return $this->callOperationAsync('refreshUserToken', $request);
    }

    /**
     * @param GetAppTokenRestRequest $request
     * @return GetAppTokenRestResponse
     */
    public function getAppToken(GetAppTokenRestRequest $request = null)
    {
        return $this->getAppTokenAsync($request)->wait();
    }

    /**
     * @param GetAppTokenRestRequest $request
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getAppTokenAsync(GetAppTokenRestRequest $request = null)
    {
        if (!$request) {
            $request = new GetAppTokenRestRequest();
        }
        if (!isset($request->grant_type)) {
            $request->grant_type = 'client_credentials';
        }
        if (!isset($request->redirect_uri)) {
            $request->redirect_uri = self::$config['ruName'];
        }
        if (!isset($request->scope)) {
            $request->scope = 'https://api.ebay.com/oauth/api_scope';
        }

        return $this->callOperationAsync('getAppToken', $request);
    }

    protected function callOperationAsync($name, BaseType $request = null)
    {
        $operation = static::$operations[$name];
        $paramValues = [];
        $requestValues = [];

        if ($request) {
            $requestArray = $request->toArray();
            $paramValues = array_intersect_key($requestArray, $operation['params']);
            $requestValues = array_diff_key($requestArray, $operation['params']);
        }

        $url = $this->uriResolver->resolve(
            $this->getUrl($name),
            self::$config['apiVersion'],
            $operation['resource'],
            $operation['params'],
            $paramValues
        );

        $method = $operation['method'];
        $body = $this->buildRequestBody($requestValues);
        $headers = $this->buildRequestHeaders($body);
        $responseClass = $operation['responseClass'];
        $debug = self::$config['debug'];
        $httpHandler = self::$config['httpHandler'];
        $httpOptions = self::$config['httpOptions'];

        if ($debug !== false) {
            $this->debugRequest($url, $headers, $body);
        }

        $request = new Request($method, $url, $headers, $body);

        return $httpHandler($request, $httpOptions)->then(
            function (ResponseInterface $res) use ($debug, $responseClass) {
                $json = $res->getBody()->getContents();

                if ($debug !== false) {
                    $this->debugResponse($json);
                }

                return new $responseClass(
                    $json !== '' ? json_decode($json, true) : [],
                    $res->getStatusCode(),
                    $res->getHeaders()
                );
            }
        );
    }

    /**
     * Helper function to return the URL as determined by the sandbox configuration option.
     *
     * @return string Either the production or sandbox URL.
     */
    protected function getUrl()
    {
        return self::$config['sandbox'] ? static::$endPoints['sandbox'] : static::$endPoints['production'];
    }

    /**
     * Builds the request body string.
     *
     * @param array $request Associative array that is the request body.
     *
     * @return string The request body in URL-encoded format.
     */
    protected function buildRequestBody(array $request)
    {
        $params = array_reduce(array_keys($request), function ($carry, $key) use($request) {
            $value = $request[$key];
            $carry[$key] = is_array($value) ? implode(' ', $value) : $value;
            return $carry;
        }, []);

        return empty($request) ? '' : http_build_query($params, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Helper function that builds the HTTP request headers.
     *
     * @param string $body The request body.
     *
     * @return array An associative array of HTTP headers.
     */
    protected function buildRequestHeaders($body)
    {
        $credentials = self::$config['credentials'];
        $appId = $credentials['appId'];
        $certId = $credentials['certId'];

        $headers = [];

        $headers['Accept'] = 'application/json';
        $headers['Authorization'] = 'Basic '.base64_encode($appId.':'.$certId);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Content-Length'] = strlen($body);

        return $headers;
    }

    /**
     * Sends a debug string of the request details.
     *
     * @param string $url API endpoint.
     * @param array $headers Associative array of HTTP headers.
     * @param string $body The JSON body of the request.
      */
    protected function debugRequest($url, array $headers, $body)
    {
        $str = $url.PHP_EOL;

        $str .= array_reduce(array_keys($headers), function ($str, $key) use ($headers) {
            $str .= $key.': '.$headers[$key].PHP_EOL;
            return $str;
        }, '');

        $str .= $body;

        $this->debug($str);
    }

    /**
     * Sends a debug string of the response details.
     *
     * @param string $body The JSON body of the response.
      */
    protected function debugResponse($body)
    {
        $this->debug($body);
    }

    /**
     * Sends a debug string via the attach debugger.
     *
     * @param string $str The debug information.
     */
    protected function debug($str)
    {
        $debugger = self::$config['debug'];
        $debugger($str);
    }
}