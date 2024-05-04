<?php
/**
 * OidcApi
 * PHP version 7.4
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * GitHub v3 REST API
 *
 * GitHub's v3 REST API.
 *
 * The version of the OpenAPI document: 1.1.4
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ApiException;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Configuration;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\HeaderSelector;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ObjectSerializer;

/**
 * OidcApi Class Doc Comment
 *
 * @category Class
 * @package  System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class OidcApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'oidcGetOidcCustomSubTemplateForOrg' => [
            'application/json',
        ],
        'oidcUpdateOidcCustomSubTemplateForOrg' => [
            'application/json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ClientInterface $client = null,
        Configuration $config = null,
        HeaderSelector $selector = null,
        $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Configuration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation oidcGetOidcCustomSubTemplateForOrg
     *
     * Get the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub
     */
    public function oidcGetOidcCustomSubTemplateForOrg($org, string $contentType = self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'][0])
    {
        list($response) = $this->oidcGetOidcCustomSubTemplateForOrgWithHttpInfo($org, $contentType);
        return $response;
    }

    /**
     * Operation oidcGetOidcCustomSubTemplateForOrgWithHttpInfo
     *
     * Get the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub, HTTP status code, HTTP response headers (array of strings)
     */
    public function oidcGetOidcCustomSubTemplateForOrgWithHttpInfo($org, string $contentType = self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'][0])
    {
        $request = $this->oidcGetOidcCustomSubTemplateForOrgRequest($org, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 200:
                    if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation oidcGetOidcCustomSubTemplateForOrgAsync
     *
     * Get the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function oidcGetOidcCustomSubTemplateForOrgAsync($org, string $contentType = self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'][0])
    {
        return $this->oidcGetOidcCustomSubTemplateForOrgAsyncWithHttpInfo($org, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation oidcGetOidcCustomSubTemplateForOrgAsyncWithHttpInfo
     *
     * Get the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function oidcGetOidcCustomSubTemplateForOrgAsyncWithHttpInfo($org, string $contentType = self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'][0])
    {
        $returnType = '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub';
        $request = $this->oidcGetOidcCustomSubTemplateForOrgRequest($org, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'oidcGetOidcCustomSubTemplateForOrg'
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function oidcGetOidcCustomSubTemplateForOrgRequest($org, string $contentType = self::contentTypes['oidcGetOidcCustomSubTemplateForOrg'][0])
    {

        // verify the required parameter 'org' is set
        if ($org === null || (is_array($org) && count($org) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $org when calling oidcGetOidcCustomSubTemplateForOrg'
            );
        }


        $resourcePath = '/orgs/{org}/actions/oidc/customization/sub';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($org !== null) {
            $resourcePath = str_replace(
                '{' . 'org' . '}',
                ObjectSerializer::toPathValue($org),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation oidcUpdateOidcCustomSubTemplateForOrg
     *
     * Set the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub $oidc_custom_sub oidc_custom_sub (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return object|\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError|\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError
     */
    public function oidcUpdateOidcCustomSubTemplateForOrg($org, $oidc_custom_sub, string $contentType = self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'][0])
    {
        list($response) = $this->oidcUpdateOidcCustomSubTemplateForOrgWithHttpInfo($org, $oidc_custom_sub, $contentType);
        return $response;
    }

    /**
     * Operation oidcUpdateOidcCustomSubTemplateForOrgWithHttpInfo
     *
     * Set the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub $oidc_custom_sub (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of object|\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError|\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError, HTTP status code, HTTP response headers (array of strings)
     */
    public function oidcUpdateOidcCustomSubTemplateForOrgWithHttpInfo($org, $oidc_custom_sub, string $contentType = self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'][0])
    {
        $request = $this->oidcUpdateOidcCustomSubTemplateForOrgRequest($org, $oidc_custom_sub, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            switch($statusCode) {
                case 201:
                    if ('object' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('object' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, 'object', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 403:
                    if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            $returnType = 'object';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        'object',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\BasicError',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation oidcUpdateOidcCustomSubTemplateForOrgAsync
     *
     * Set the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub $oidc_custom_sub (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function oidcUpdateOidcCustomSubTemplateForOrgAsync($org, $oidc_custom_sub, string $contentType = self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'][0])
    {
        return $this->oidcUpdateOidcCustomSubTemplateForOrgAsyncWithHttpInfo($org, $oidc_custom_sub, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation oidcUpdateOidcCustomSubTemplateForOrgAsyncWithHttpInfo
     *
     * Set the customization template for an OIDC subject claim for an organization
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub $oidc_custom_sub (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function oidcUpdateOidcCustomSubTemplateForOrgAsyncWithHttpInfo($org, $oidc_custom_sub, string $contentType = self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'][0])
    {
        $returnType = 'object';
        $request = $this->oidcUpdateOidcCustomSubTemplateForOrgRequest($org, $oidc_custom_sub, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'oidcUpdateOidcCustomSubTemplateForOrg'
     *
     * @param  string $org The organization name. The name is not case sensitive. (required)
     * @param  \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Repos\Github\Model\OidcCustomSub $oidc_custom_sub (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function oidcUpdateOidcCustomSubTemplateForOrgRequest($org, $oidc_custom_sub, string $contentType = self::contentTypes['oidcUpdateOidcCustomSubTemplateForOrg'][0])
    {

        // verify the required parameter 'org' is set
        if ($org === null || (is_array($org) && count($org) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $org when calling oidcUpdateOidcCustomSubTemplateForOrg'
            );
        }

        // verify the required parameter 'oidc_custom_sub' is set
        if ($oidc_custom_sub === null || (is_array($oidc_custom_sub) && count($oidc_custom_sub) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $oidc_custom_sub when calling oidcUpdateOidcCustomSubTemplateForOrg'
            );
        }


        $resourcePath = '/orgs/{org}/actions/oidc/customization/sub';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($org !== null) {
            $resourcePath = str_replace(
                '{' . 'org' . '}',
                ObjectSerializer::toPathValue($org),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($oidc_custom_sub)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($oidc_custom_sub));
            } else {
                $httpBody = $oidc_custom_sub;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
