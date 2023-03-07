<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayXMLService;
use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class EbayTradingApiBaseService extends EbayXMLService
{
    const HDR_API_VERSION = 'X-EBAY-API-COMPATIBILITY-LEVEL';

    const HDR_APP_ID = 'X-EBAY-API-APP-NAME';

    const HDR_AUTHORIZATION = 'X-EBAY-API-IAF-TOKEN';

    const HDR_CERT_ID = 'X-EBAY-API-CERT-NAME';

    const HDR_DEV_ID = 'X-EBAY-API-DEV-NAME';

    const HDR_OPERATION_NAME = 'X-EBAY-API-CALL-NAME';

    const HDR_SITE_ID = 'X-EBAY-API-SITEID';

    protected static $endPoints =
        [
        'primary' => [
          'production' => 'https://api.ebay.com/ws/api.dll',
        ],
      ];

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public static function getConfigDefinitions()
    {
        $definitions = parent::getConfigDefinitions();

        return $definitions + [
            'apiVersion' => [
                'valid' => ['string'],
                'default' => \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Services\EbayTradingApiService::API_VERSION,
                'required' => true
            ],
            'authorization' => [
                'valid' => ['string']
            ],
            'authToken' => [
                'valid' => ['string']
            ],
            'siteId' => [
                'valid' => ['int', 'string'],
                'required' => true,
                'default' => self::$config['ebay_ids'][self::$config['marketplace_id']]['site_id']
            ]
        ];
    }

    protected function getEbayHeaders($operationName)
    {
        $appId =
            $this->getConfig('user_credentials_app_id') !== '' ?
            $this->getConfig('user_credentials_app_id') :
            $this->getConfig('credentials')['appId'];

        $devId =
            $this->getConfig('user_credentials_dev_id') !== '' ?
            $this->getConfig('user_credentials_dev_id') :
            $this->getConfig('credentials')['devId'];

        $certId =
            $this->getConfig('user_credentials_cert_id') !== '' ?
            $this->getConfig('user_credentials_cert_id') :
            $this->getConfig('credentials')['certId'];

        $headers = [];

        // Add required headers first.
        $headers[self::HDR_API_VERSION] = $this->getConfig('apiVersion');
        $headers[self::HDR_OPERATION_NAME] = $operationName;
        $headers[self::HDR_SITE_ID] = $this->getConfig('siteId');

        // Add optional headers.
        if ($appId) {
            $headers[self::HDR_APP_ID] = $appId;
        }

        if ($certId) {
            $headers[self::HDR_CERT_ID] = $certId;
        }

        if ($devId) {
            $headers[self::HDR_DEV_ID] = $devId;
        }

        if ($this->getConfig('authorization')) {
            $headers[self::HDR_AUTHORIZATION] = $this->getConfig('authorization');
        }

        if ($operationName === 'UploadSiteHostedPictures') {
            $headers['Content-Type'] = 'multipart/form-data;boundary="boundary"';
        }

        return $headers;
    }

    protected function callOperationAsync($name, BaseType $request, $responseClass)
    {
        if ($this->getConfig('authorization') !== null) {
            /**
             * Don't send requester credentials if oauth authentication needed.
             */
            if (isset($request->RequesterCredentials)) {
                unset($request->RequesterCredentials);
            }
        } elseif ($this->getConfig('authToken') !== null) {
            /**
             * Don't modify a request if the token already exists.
             */
            if (!isset($request->RequesterCredentials)) {
                $request->RequesterCredentials = new \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CustomSecurityHeaderType();
            }
            if (!isset($request->RequesterCredentials->eBayAuthToken)) {
                $request->RequesterCredentials->eBayAuthToken = $this->getConfig('authToken');
            }
        }

        return parent::callOperationAsync($name, $request, $responseClass);
    }

    protected function buildRequestBody(BaseType $request)
    {
        if ($request->hasAttachment() && $request instanceof \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UploadSiteHostedPicturesRequest) {
            return $this->buildMultipartFormDataXMLPayload($request).$this->buildMultipartFormDataFilePayload($request->PictureName, $request->attachment());
        } else {
            return parent::buildRequestBody($request);
        }
    }
}