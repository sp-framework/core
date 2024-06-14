<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices;

use GuzzleHttp\TransferStats;
use Mattiasgeniar\Percentage\Percentage;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\ApiClientServices;
use System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\ApisHeaderSelector;

class Apis extends BasePackage
{
    protected static $debug = false;

    protected $apiConfig;

    protected $config;

    protected $api;

    protected $serviceClass;

    protected $response;

    protected $headers = null;

    public static $trackCounter = 0;

    protected $httpOptions = [
        'debug'           => false,
        'http_errors'     => true,
        'timeout'         => 10,
        'verify'          => false
    ];

    public function init($apiConfig = null, $apiClientServices = null, $httpOptions = null, $monitorProgress = null)
    {
        if (isset($apiConfig['checkOnly']) && $apiConfig['checkOnly'] === true) {//used for checking via ServicesComponent if the API exists
            return $this;
        }

        $apiConfig['location'] = ucfirst($apiConfig['location']);
        $apiConfig['category'] = ucfirst($apiConfig['category']);
        $apiConfig['provider'] = ucfirst($apiConfig['provider']);

        $this->apiConfig = $apiConfig;

        $this->apiClientServices = $apiClientServices;

        $this->setConfiguration();

        if ($httpOptions && is_array($httpOptions)) {
            $this->httpOptions = array_merge($this->httpOptions, $httpOptions);
        }

        if ($monitorProgress && is_array($monitorProgress)) {
            $this->initMonitorProgress($monitorProgress);
        }

        if ($this->apiConfig['location'] === 'Basepackages') {
            $this->serviceClass =
                    "System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\ApiClientServices\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Api\\";
        } else {
            $this->serviceClass =
                    "Apps\\{$this->apiConfig['location']}\\Packages\\System\\ApiClientServices\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Api\\";
        }

        return $this;
    }

    protected function initRemoteWebContent($method, $apiConfig)
    {
        $this->httpOptions['on_stats'] = function (\GuzzleHttp\TransferStats $stats) use ($method, $apiConfig) {
            $errorCode = null;

            if (!$stats->hasResponse()) {
                $errorCode = $stats->getHandlerErrorData();
            }

            $api = (new ApiClientServices)->init();

            $api->apiStats->updateApiCallStats($method, $apiConfig['id'], $stats->getHandlerStats(), $errorCode);
        };

        if (strtolower($apiConfig['provider']) === 'github') {
            $this->httpOptions['headers']['Authorization'] = 'Bearer ' . $apiConfig['authorization'];
        }

        $this->remoteWebContent = (new \System\Base\Providers\ContentServiceProvider\RemoteWeb\Content)->init($this->httpOptions);
    }

    protected function setConfiguration()
    {
        if ($this->apiConfig['location'] === 'Basepackages') {
            $configurationClass = "System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\ApiClientServices\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Configuration";
        } else {
            $configurationClass = "Apps\\{$this->apiConfig['location']}\\Packages\\System\\ApiClientServices\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Configuration";
        }

        $this->config = new $configurationClass;

        $this->config->setHost($this->helper->reduceSlashes($this->apiConfig['api_url']));

        if (isset($this->apiConfig['debug']) && $this->apiConfig['debug'] === true) {
            $this->config->setDebug(true);
            $this->config->setDebugFile(base_path("var/log/api_{$this->apiConfig['category']}_{$this->apiConfig['provider']}.log"));
            $this->httpOptions['debug'] = true;
        }

        $this->config->setUsername(null);
        $this->config->setPassword(null);
        $this->config->setApiKey('access_token', null);
        if ($this->apiConfig['auth_type'] === 'auth') {
            $this->config->setUsername($this->apiConfig['username']);
            $this->config->setPassword($this->apiConfig['password']);
        } else if ($this->apiConfig['auth_type'] === 'access_token') {
            $this->config->setApiKey('access_token', $this->apiConfig['access_token']);
        } else if ($this->apiConfig['auth_type'] === 'autho') {
            if (strtolower($this->apiConfig['provider']) === 'gitea') {
                $this->config->setApiKey('Authorization', $this->apiConfig['authorization']);
                $this->config->setApiKeyPrefix('Authorization', 'token');
            }
            //Set Authorization for github via $this->httpOptions as Openapi tool does not generate method to include authentication.
        }
    }

    public function useMethod($collection, $method, $methodArgs = [])
    {
        $this->initRemoteWebContent($collection . ':' . $method, $this->apiConfig);

        try {
            $class = $this->serviceClass . $collection;

            $collectionClass = new $class($this->remoteWebContent, $this->config, $this->headers);

            $this->response = call_user_func_array([$collectionClass, $method], $methodArgs);

            return $this;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getResponse($toArray = false, $toJson = false)
    {
        if ($this->response) {
            if ($toArray) {
                return $this->toArray();
            } else if ($toJson) {
                return $this->response->__toString();
            }

            return $this->response;
        }

        return false;
    }

    public function toArray()
    {
        $responseArr = [];

        if ($this->response && is_array($this->response)) {
            foreach ($this->response as $key => $response) {
                $responseArr[$key] = $this->helper->decode($response->__toString(), true);
            }
        } else {
            $responseArr = $this->helper->decode($this->response->__toString(), true);
        }

        return $responseArr;
    }

    public function getApiClientServices()
    {
        return $this->apiClientServices;
    }

    public function getApiConfig()
    {
        return $this->apiConfig;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setHeaders(array $accept = [], string $contentType = '', bool $isMultipart = false)
    {
        $this->headers = (new ApisHeaderSelector)->selectHeaders($accept, $contentType, $isMultipart);
    }

    protected function initMonitorProgress($monitorProgress)
    {
        self::$trackCounter = 0;

        $this->httpOptions['sink'] = $monitorProgress['sink'];

        $this->httpOptions['progress'] =
            function (
                $downloadTotal,
                $downloadedBytes,
                $uploadTotal,
                $uploadedBytes
            ) use ($monitorProgress) {
                if ($downloadTotal === 0 && $uploadTotal === 0) {
                    return;
                }

                $counters =
                        [
                            'downloadTotal'     => $downloadTotal,
                            'downloadedBytes'   => $downloadedBytes,
                            'uploadTotal'       => $uploadTotal,
                            'uploadedBytes'     => $uploadedBytes
                        ];

                if ($downloadTotal > 0) {
                    if ($downloadedBytes === 0) {
                        return;
                    }

                    //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                    //So this way, we only update progress when there is actually an update.
                    if ($downloadedBytes === \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis::$trackCounter) {
                        return;
                    }

                    \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis::$trackCounter = $downloadedBytes;

                    $downloadComplete = null;
                    if ($downloadedBytes === $downloadTotal) {
                        $downloadComplete = true;
                    }
                    $this->basepackages->progress->updateProgress($monitorProgress['method'], $downloadComplete, false, null, $counters);
                } else if ($uploadTotal > 0) {
                    if ($uploadedBytes === 0) {
                        return;
                    }

                    //Trackcounter is needed as guzzelhttp runs this in a while loop causing too many updates with same download count.
                    //So this way, we only update progress when there is actually an update.
                    if ($uploadedBytes === \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis::$trackCounter) {
                        return;
                    }

                    \System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis::$trackCounter = $uploadedBytes;

                    $uploadComplete = null;
                    if ($uploadedBytes === $uploadTotal) {
                        $uploadComplete = true;
                    }
                    $this->basepackages->progress->updateProgress($monitorProgress['method'], $uploadComplete, false, null, $counters);
                }
            };
    }
}