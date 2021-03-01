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

    /**
     * @var array $endPoints The API endpoints.
     */
    protected static $endPoints = [
        'sandbox'    => 'https://api.sandbox.ebay.com/identity',
        'production' => 'https://api.ebay.com/identity'
    ];

    /**
     * @property array $operations Associative array of operations provided by the service.
     */
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

    /**
     * @var \Apps\Dash\Packages\System\Api\Apis\Ebay\ConfigurationResolver Resolves configuration options.
     */
    protected $resolver;

    /**
     * @var \Apps\Dash\Packages\System\Api\Apis\Ebay\UriResolver Resolves uri parameters.
     */
    protected $uriResolver;

    /**
     * @var array Associative array storing the current configuration option values.
     */
    protected static $config;

    protected static $sandbox;

    protected static $debug;

    protected static $credentials;

    /**
     * @param array $config Configuration option values.
     */
    public function __construct(array $config)
    {
        self::$credentials = $config['credentials'];

        self::$sandbox = $config['sandbox'];

        self::$debug = $config['debug'];

        $this->resolver = new ConfigurationResolver(static::getConfigDefinitions());

        $this->uriResolver = new UriResolver();

        self::$config = $this->resolver->resolve($config);
    }

    /**
     * Returns definitions for each configuration option that is supported.
     *
     * @return array An associative array of configuration definitions.
     */
    public static function getConfigDefinitions()
    {
        return [
            'apiVersion'    => [
                'valid'     => ['string'],
                'default'   => self::API_VERSION,
                'required'  => true
            ],
            'profile'       => [
                'valid'     => ['string'],
                'fn'        => 'Apps\Dash\Packages\System\Api\Base\Functions::applyProfile',
            ],
            'credentials'   => [
                'valid'     => ['array'],
                'default'   => self::$credentials
            ],
            'debug'         => [
                'valid'     => ['bool', 'array'],
                'fn'        => 'Apps\Dash\Packages\System\Api\Base\Functions::applyDebug',
                'default'   => self::$debug
            ],
            'httpHandler'   => [
                'valid'     => ['callable'],
                'default'   => 'Apps\Dash\Packages\System\Api\Base\Functions::defaultHttpHandler'
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

    /**
     * Method to get the service's configuration.
     *
     * @param string|null $option The name of the option whos value will be returned.
     *
     * @return mixed Returns an associative array of configuration options if no parameters are passed,
     * otherwise returns the value for the specified configuration option.
     */
    public function getConfig($option = null)
    {
        return $option === null
            ? self::$config
            : (isset(self::$config[$option])
                ? self::$config[$option]
                : null);
    }

    /**
     * Set multiple configuration options.
     *
     * @param array $configuration Associative array of configuration options and their values.
     */
    public function setConfig(array $configuration)
    {
        self::$config = Functions::arrayMergeDeep(
            self::$config,
            $this->resolver->resolveOptions($configuration)
        );
    }

    /**
     * Helper method to return the value of the credentials configuration option.
     *
     * @return \Apps\Dash\Packages\System\Api\Apis\Ebay\Credentials\CredentialsInterface
     */
    public function getCredentials()
    {
        return $this->getConfig('credentials');
    }

    /**
     * @param array $params An associative array with state and scope as the keys.
     *
     * @return string The redirect URL.
     * @throws \InvalidArgumentException.
     */
    public function redirectUrlForUser(array $params)
    {
        if (!array_key_exists('state', $params)) {
            throw new \InvalidArgumentException('state parameter required');
        }

        if (!array_key_exists('scope', $params)) {
            throw new \InvalidArgumentException('scope parameter required');
        }

        $url = $this->getConfig('sandbox')
            ? 'https://auth.sandbox.ebay.com/oauth2/authorize?'
            : 'https://auth.ebay.com/oauth2/authorize?';

        $urlParams = [
            'client_id'     => $this->getConfig('credentials')['appId'],
            'redirect_uri'  => $this->getConfig('ruName'),
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
            $request->redirect_uri = $this->getConfig('ruName');
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
            $request->redirect_uri = $this->getConfig('ruName');
        }
        if (!isset($request->scope)) {
            $request->scope = 'https://api.ebay.com/oauth/api_scope';
        }

        return $this->callOperationAsync('getAppToken', $request);
    }

    /**
     * Sends an asynchronous API request.
     *
     * @param string $name The name of the operation.
     * @param \Apps\Dash\Packages\System\Api\Apis\Ebay\Types\BaseType $request Request object containing the request information.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface A promise that will be resolved with an object created from the JSON response.
     */
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
            $this->getConfig('apiVersion'),
            $operation['resource'],
            $operation['params'],
            $paramValues
        );

        $method = $operation['method'];
        $body = $this->buildRequestBody($requestValues);
        $headers = $this->buildRequestHeaders($body);
        $responseClass = $operation['responseClass'];
        $debug = $this->getConfig('debug');
        $httpHandler = $this->getConfig('httpHandler');
        $httpOptions = $this->getConfig('httpOptions');

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
        return $this->getConfig('sandbox') ? static::$endPoints['sandbox'] : static::$endPoints['production'];
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
        $credentials = $this->getConfig('credentials');
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
        $debugger = $this->getConfig('debug');
        $debugger($str);
    }
}
