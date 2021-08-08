<?php

namespace Apps\Dash\Packages\System\Api\Base;

use Apps\Dash\Packages\System\Api\Base\BaseFunctions;
use Apps\Dash\Packages\System\Api\Base\ConfigurationResolver;
use Apps\Dash\Packages\System\Api\Base\Parser\JsonParser;
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;
use Apps\Dash\Packages\System\Api\Base\UriResolver;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;

class BaseRESTService
{
    /**
     * @var \DTS\eBaySDK\ConfigurationResolver Resolves configuration options.
     */
    protected $resolver;

    /**
     * @var \DTS\eBaySDK\UriResolver Resolves uri parameters.
     */
    protected $uriResolver;

    /**
     * @var array Associative array storing the current configuration option values.
     */
    protected static $config;

    /**
     * @var array Associative array for optionalHeaders
     */
    protected $optionalHeaders = [];

    protected $container;

    /**
     * @param array $config Configuration option values.
     */
    public function __construct(array $config)
    {
        self::$config = $config;

        $this->resolver = new ConfigurationResolver(static::getConfigDefinitions());

        $this->uriResolver = new UriResolver();

        self::$config = $this->resolver->resolve($config);
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
        self::$config = BaseFunctions::arrayMergeDeep(
            self::$config,
            $this->resolver->resolveOptions($configuration)
        );
    }

    public function setOptionalHeader(array $header)
    {
        $this->optionalHeaders = BaseFunctions::arrayMergeDeep(
            $this->optionalHeaders,
            $header
        );
    }

    /**
     * Sends an asynchronous API request.
     *
     * @param string $name The name of the operation.
     * @param \Apps\Dash\Packages\System\Api\Base\Types\BaseType $request Request object containing the request information.
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
            $this->getUrl(),
            $this->getConfig('apiVersion'),
            $operation['resource'],
            $operation['params'],
            $paramValues
        );

        $method = $operation['method'];

        $body = $this->buildRequestBody($requestValues);

        if (count($this->optionalHeaders) > 0) {
            $headers = array_merge($this->optionalHeaders, $this->buildRequestHeaders($body));
        } else {
            $headers = $this->buildRequestHeaders($body);
        }

        $responseClass = $operation['responseClass'];
        $debug = $this->getConfig('debug');
        $httpHandler = $this->getConfig('httpHandler');
        $httpOptions = $this->getConfig('httpOptions');

        $apiId = $this->getConfig('api_id');

        $httpOptions['on_stats'] = function (TransferStats $stats) use ($name, $apiId) {
            $errorCode = null;

            if (!$stats->hasResponse()) {
                $errorCode = $stats->getHandlerErrorData();
            }

            $api = new \Apps\Dash\Packages\System\Api\Api;

            $api->updateApiCallStats($name, $apiId, $stats->getHandlerStats(), $errorCode);
        };

        if ($debug !== false) {
            $this->debugRequest($url, $headers, $body);
        }

        $request = new Request($method, $url, $headers, $body);

        return $httpHandler($request, $httpOptions)->then(
            function (ResponseInterface $res) use ($debug, $responseClass) {
                $json = $res->getBody()->getContents();

                // var_dump(json_decode($json, true));
                if ($debug !== false) {
                    $this->debugResponse($json);
                }

                $response = new $responseClass(
                    $json !== '' ? json_decode($json, true) : [],
                    $res->getStatusCode(),
                    $res->getHeaders()
                );

                JsonParser::parseAndAssignProperties($response, $json);

                return $response;
            }
        );
    }

    /**
     * Builds the request body string.
     *
     * @param array $request Associative array that is the request body.
     *
     * @return string The request body in JSON format.
     */
    protected function buildRequestBody(array $request)
    {
        return empty($request) ? '' : json_encode($request);
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