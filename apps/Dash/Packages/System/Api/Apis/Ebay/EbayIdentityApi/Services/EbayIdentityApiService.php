<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Services\EbayIdentityApiBaseService;

class EbayIdentityApiService extends EbayIdentityApiBaseService
{
    protected static $operations =
        [
        'GetUser' => [
          'method' => 'GET',
          'resource' => 'user/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Operations\GetUserRestResponse',
          'params' => [
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getUser(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Operations\GetUserRestRequest $request)
    {
        return $this->getUserAsync($request)->wait();
    }

    public function getUserAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Operations\GetUserRestRequest $request)
    {
        return $this->callOperationAsync('GetUser', $request);
    }
}