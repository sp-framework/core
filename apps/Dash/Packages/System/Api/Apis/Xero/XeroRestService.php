<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero;

use Apps\Dash\Packages\System\Api\Base\BaseRESTService;

class XeroRESTService extends BaseRESTService
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public static function getConfigDefinitions()
    {
        return [
            'debug'             => [
                'valid'             => ['bool', 'array'],
                'fn'                => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayFunctions::applyDebug',
                'default'           => false
            ],
            'authorization'     => [
                'valid'             => ['string'],
                'default'           => self::$config['user_access_token'],
                'required'          => true
            ],
            'tenantId'     => [
                'valid'             => ['string'],
                'default'           => self::$config['tenantId'],
            ],
            'compressResponse'  => [
                'valid'             => ['bool'],
                'default'           => false
            ],
            'httpHandler'       => [
                'valid'             => ['callable'],
                'default'           => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayFunctions::defaultHttpHandler'
            ],
            'httpOptions'       => [
                'valid'             => ['array'],
                'default'           => [
                    'http_errors'       => false,
                    'timeout'           => 300
                ]
            ]
        ];
    }

    protected function getUrl($name)
    {
        return static::$endPoints['primary']['production'];
    }

    protected function buildRequestHeaders($body)
    {
        $headers = $this->getXeroHeaders();

        $headers['Accept'] = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['Content-Length'] = strlen($body);

        return $headers;
    }
}