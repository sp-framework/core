<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayRESTService;

class EbayIdentityApiBaseService extends EbayRESTService
{
    protected static $endPoints =
        [
        'primary' => [
          'production' => 'https://apiz.ebay.com/commerce/identity/v1',
        ],
      ];

    const HDR_AUTHORIZATION = 'Authorization';

    const HDR_MARKETPLACE_ID = 'X-EBAY-C-MARKETPLACE-ID';

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    protected function getEbayHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = 'Bearer ' . $this->getConfig('user_access_token');

        // Add optional headers.
        if ($this->getConfig('marketplaceId')) {
            $headers[self::HDR_MARKETPLACE_ID] = $this->getConfig('marketplaceId');
        }

        return $headers;
    }
}