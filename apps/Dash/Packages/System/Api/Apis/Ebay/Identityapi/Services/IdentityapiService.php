<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Services\IdentityapiBaseService;

class IdentityapiService extends IdentityapiBaseService
{
    const API_VERSION = 'v1';

    protected static $operations =
        [
        'GetUser' => [
          'method' => 'GET',
          'resource' => 'user/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestResponse',
          'params' => [
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getUser(\Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestRequest $request)
    {
        return $this->getUserAsync($request)->wait();
    }

    public function getUserAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Identityapi\Operations\GetUserRestRequest $request)
    {
        return $this->callOperationAsync('GetUser', $request);
    }
}