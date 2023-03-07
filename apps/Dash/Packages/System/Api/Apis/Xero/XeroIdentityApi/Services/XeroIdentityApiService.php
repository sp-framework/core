<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Services\XeroIdentityApiBaseService;

class XeroIdentityApiService extends XeroIdentityApiBaseService
{
    protected static $operations =
        [
        'GetConnections' => [
          'method' => 'GET',
          'resource' => 'Connections',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\GetConnectionsRestResponse',
          'params' => [
            'authEventId' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'DeleteConnection' => [
          'method' => 'DELETE',
          'resource' => 'Connections/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\DeleteConnectionRestResponse',
          'params' => [
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function getConnections(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\GetConnectionsRestRequest $request)
    {
        return $this->getConnectionsAsync($request)->wait();
    }

    public function getConnectionsAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\GetConnectionsRestRequest $request)
    {
        return $this->callOperationAsync('GetConnections', $request);
    }

    public function deleteConnection(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\DeleteConnectionRestRequest $request)
    {
        return $this->deleteConnectionAsync($request)->wait();
    }

    public function deleteConnectionAsync(\Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Operations\DeleteConnectionRestRequest $request)
    {
        return $this->callOperationAsync('DeleteConnection', $request);
    }
}