<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayRESTService;

class EbayDeveloperAnalyticsApiBaseService extends EbayRESTService
{
    protected static $endPoints =
        [
        'primary' => [
          'production' => 'https://api.ebay.com/developer/analytics/v1_beta',
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