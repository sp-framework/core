<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Services\EbayDeveloperAnalyticsApiBaseService;

class EbayDeveloperAnalyticsApiService extends EbayDeveloperAnalyticsApiBaseService
{
    protected static $operations =
        [
        'GetRateLimits' => [
          'method' => 'GET',
          'resource' => 'rate_limit/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetRateLimitsRestResponse',
          'params' => [
            'api_context' => [
              'valid' => [
                'string',
              ],
            ],
            'api_name' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetUserRateLimits' => [
          'method' => 'GET',
          'resource' => 'user_rate_limit/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetUserRateLimitsRestResponse',
          'params' => [
            'api_context' => [
              'valid' => [
                'string',
              ],
            ],
            'api_name' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getRateLimits(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetRateLimitsRestRequest $request)
    {
        return $this->getRateLimitsAsync($request)->wait();
    }

    public function getRateLimitsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetRateLimitsRestRequest $request)
    {
        return $this->callOperationAsync('GetRateLimits', $request);
    }

    public function getUserRateLimits(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetUserRateLimitsRestRequest $request)
    {
        return $this->getUserRateLimitsAsync($request)->wait();
    }

    public function getUserRateLimitsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations\GetUserRateLimitsRestRequest $request)
    {
        return $this->callOperationAsync('GetUserRateLimits', $request);
    }
}