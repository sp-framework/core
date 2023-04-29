<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repos;

use GuzzleHttp\TransferStats;
use Phalcon\Helper\Str;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Configuration;

class Repos extends BasePackage
{
    protected static $debug = false;

    protected $apiConfig;

    protected $config;

    protected $api;

    protected $serviceClass;

    protected $httpOptions = [
        'debug'           => false,
        'http_errors'     => true,
        'timeout'         => 10,
        'verify'          => false
    ];

    public function init($apiConfig = null, $api = null, $httpOptions = null)
    {
        $apiConfig['category'] = ucfirst($apiConfig['category']);
        $apiConfig['provider'] = ucfirst($apiConfig['provider']);

        $this->apiConfig = $apiConfig;

        $this->setConfiguration();

        if ($httpOptions) {
            $this->httpOptions = array_merge($this->httpOptions, $httpOptions);
        }

        $this->api = $api;

        if ($this->apiConfig['location'] === 'system') {
            $this->serviceClass =
                    "System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Api\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Api\\";
        } else if ($this->apiConfig['location'] === 'apps') {
            $this->serviceClass =
                    "Apps\\Dash\\Packages\\System\\Api\\Apis\\{$this->apiConfig['category']}\\{$this->apiConfig['provider']}\\Api\\";
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

            $api = (new \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Api)->init();

            $api->apiStats->updateApiCallStats($method, $apiConfig['id'], $stats->getHandlerStats(), $errorCode);
        };

        $this->remoteWebContent = (new \System\Base\Providers\ContentServiceProvider\RemoteWeb\Content)->init($this->httpOptions);
    }

    protected function setConfiguration()
    {
        $this->config = new Configuration;

        $this->config->setHost(Str::reduceSlashes($this->apiConfig['api_url'] . '/api/v1'));

        if (isset($this->apiConfig['debug']) && $this->apiConfig['debug'] === true) {
            $this->config->setDebug(true);
            $this->httpOptions['debug'] = true;
        }

        if ($this->apiConfig['auth_type'] === 'auth') {
            $this->config->setUsername($this->apiConfig['username']);
            $this->config->setPassword($this->apiConfig['password']);
        } else if ($this->apiConfig['auth_type'] === 'access_token') {
            $this->config->setApiKey('access_token', $this->apiConfig['access_token']);
        } else if ($this->apiConfig['auth_type'] === 'autho') {
            $this->config->setApiKey('Authorization', $this->apiConfig['authorization']);
            $this->config->setApiKeyPrefix('Authorization', 'token');
        }
    }
}