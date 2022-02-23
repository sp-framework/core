<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Services\BinarylaneApiBaseService;

class BinarylaneApiService extends BinarylaneApiBaseService
{
    protected static $operations =
        [
        'Accounts' => [
          'method' => 'GET',
          'resource' => 'v2/account',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\AccountsRestResponse',
          'params' => [
          ],
        ],
        'Actions' => [
          'method' => 'POST',
          'resource' => 'v2/actions/{action_id}/proceed',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestResponse',
          'params' => [
            'action_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'Customers' => [
          'method' => 'GET',
          'resource' => 'v2/customers/my/invoices',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'DataUsages' => [
          'method' => 'GET',
          'resource' => 'v2/data_usages/current',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DataUsagesRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'Domains' => [
          'method' => 'DELETE',
          'resource' => 'v2/domains/{domain_name}/records/{record_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestResponse',
          'params' => [
            'domain_name' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'record_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'FailoverIps' => [
          'method' => 'GET',
          'resource' => 'v2/failover_ips/{server_id}/available',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestResponse',
          'params' => [
            'server_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'Images' => [
          'method' => 'GET',
          'resource' => 'v2/images/{image_id}/download',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestResponse',
          'params' => [
            'image_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'Keys' => [
          'method' => 'POST',
          'resource' => 'v2/account/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestResponse',
          'params' => [
          ],
        ],
        'LoadBalancers' => [
          'method' => 'DELETE',
          'resource' => 'v2/load_balancers/{load_balancer_id}/forwarding_rules',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestResponse',
          'params' => [
            'load_balancer_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'Regions' => [
          'method' => 'GET',
          'resource' => 'v2/regions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\RegionsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'ReverseNames' => [
          'method' => 'POST',
          'resource' => 'v2/reverse_names/ipv6',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ReverseNamesRestResponse',
          'params' => [
          ],
        ],
        'SampleSets' => [
          'method' => 'GET',
          'resource' => 'v2/samplesets/{server_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SampleSetsRestResponse',
          'params' => [
            'server_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'data_interval' => [
              'valid' => [
              ],
            ],
            'start' => [
              'valid' => [
                'string',
              ],
            ],
            'end' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'ServerActions' => [
          'method' => 'POST',
          'resource' => 'v2/servers/{server_id}/actions#ChangeVpcIpv4',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestResponse',
          'params' => [
            'server_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'Servers' => [
          'method' => 'GET',
          'resource' => 'v2/servers/{server_id}/software',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestResponse',
          'params' => [
            'server_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'Sizes' => [
          'method' => 'GET',
          'resource' => 'v2/sizes',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SizesRestResponse',
          'params' => [
            'server_id' => [
              'valid' => [
          'integer',
              ],
            ],
            'image' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'Software' => [
          'method' => 'GET',
          'resource' => 'v2/software/operating_system/{operating_system_id_or_slug}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestResponse',
          'params' => [
            'operating_system_id_or_slug' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'Vpcs' => [
          'method' => 'GET',
          'resource' => 'v2/vpcs/{vpc_id}/members',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestResponse',
          'params' => [
            'vpc_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'resource_type' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function Accounts(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\AccountsRestRequest $request)
    {
        return $this->AccountsAsync($request)->wait();
    }

    public function AccountsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\AccountsRestRequest $request)
    {
        return $this->callOperationAsync('Accounts', $request);
    }

    public function Actions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->ActionsAsync($request)->wait();
    }

    public function ActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->callOperationAsync('Actions', $request);
    }

    public function Actions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->ActionsAsync($request)->wait();
    }

    public function ActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->callOperationAsync('Actions', $request);
    }

    public function Actions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->ActionsAsync($request)->wait();
    }

    public function ActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ActionsRestRequest $request)
    {
        return $this->callOperationAsync('Actions', $request);
    }

    public function Customers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->CustomersAsync($request)->wait();
    }

    public function CustomersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->callOperationAsync('Customers', $request);
    }

    public function Customers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->CustomersAsync($request)->wait();
    }

    public function CustomersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->callOperationAsync('Customers', $request);
    }

    public function Customers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->CustomersAsync($request)->wait();
    }

    public function CustomersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\CustomersRestRequest $request)
    {
        return $this->callOperationAsync('Customers', $request);
    }

    public function DataUsages(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DataUsagesRestRequest $request)
    {
        return $this->DataUsagesAsync($request)->wait();
    }

    public function DataUsagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DataUsagesRestRequest $request)
    {
        return $this->callOperationAsync('DataUsages', $request);
    }

    public function DataUsages(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DataUsagesRestRequest $request)
    {
        return $this->DataUsagesAsync($request)->wait();
    }

    public function DataUsagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DataUsagesRestRequest $request)
    {
        return $this->callOperationAsync('DataUsages', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function Domains(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->DomainsAsync($request)->wait();
    }

    public function DomainsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\DomainsRestRequest $request)
    {
        return $this->callOperationAsync('Domains', $request);
    }

    public function FailoverIps(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->FailoverIpsAsync($request)->wait();
    }

    public function FailoverIpsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->callOperationAsync('FailoverIps', $request);
    }

    public function FailoverIps(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->FailoverIpsAsync($request)->wait();
    }

    public function FailoverIpsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->callOperationAsync('FailoverIps', $request);
    }

    public function FailoverIps(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->FailoverIpsAsync($request)->wait();
    }

    public function FailoverIpsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\FailoverIpsRestRequest $request)
    {
        return $this->callOperationAsync('FailoverIps', $request);
    }

    public function Images(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->ImagesAsync($request)->wait();
    }

    public function ImagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->callOperationAsync('Images', $request);
    }

    public function Images(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->ImagesAsync($request)->wait();
    }

    public function ImagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->callOperationAsync('Images', $request);
    }

    public function Images(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->ImagesAsync($request)->wait();
    }

    public function ImagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->callOperationAsync('Images', $request);
    }

    public function Images(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->ImagesAsync($request)->wait();
    }

    public function ImagesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ImagesRestRequest $request)
    {
        return $this->callOperationAsync('Images', $request);
    }

    public function Keys(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->KeysAsync($request)->wait();
    }

    public function KeysAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->callOperationAsync('Keys', $request);
    }

    public function Keys(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->KeysAsync($request)->wait();
    }

    public function KeysAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->callOperationAsync('Keys', $request);
    }

    public function Keys(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->KeysAsync($request)->wait();
    }

    public function KeysAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->callOperationAsync('Keys', $request);
    }

    public function Keys(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->KeysAsync($request)->wait();
    }

    public function KeysAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->callOperationAsync('Keys', $request);
    }

    public function Keys(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->KeysAsync($request)->wait();
    }

    public function KeysAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\KeysRestRequest $request)
    {
        return $this->callOperationAsync('Keys', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function LoadBalancers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->LoadBalancersAsync($request)->wait();
    }

    public function LoadBalancersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\LoadBalancersRestRequest $request)
    {
        return $this->callOperationAsync('LoadBalancers', $request);
    }

    public function Regions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\RegionsRestRequest $request)
    {
        return $this->RegionsAsync($request)->wait();
    }

    public function RegionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\RegionsRestRequest $request)
    {
        return $this->callOperationAsync('Regions', $request);
    }

    public function ReverseNames(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ReverseNamesRestRequest $request)
    {
        return $this->ReverseNamesAsync($request)->wait();
    }

    public function ReverseNamesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ReverseNamesRestRequest $request)
    {
        return $this->callOperationAsync('ReverseNames', $request);
    }

    public function ReverseNames(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ReverseNamesRestRequest $request)
    {
        return $this->ReverseNamesAsync($request)->wait();
    }

    public function ReverseNamesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ReverseNamesRestRequest $request)
    {
        return $this->callOperationAsync('ReverseNames', $request);
    }

    public function SampleSets(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SampleSetsRestRequest $request)
    {
        return $this->SampleSetsAsync($request)->wait();
    }

    public function SampleSetsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SampleSetsRestRequest $request)
    {
        return $this->callOperationAsync('SampleSets', $request);
    }

    public function SampleSets(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SampleSetsRestRequest $request)
    {
        return $this->SampleSetsAsync($request)->wait();
    }

    public function SampleSetsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SampleSetsRestRequest $request)
    {
        return $this->callOperationAsync('SampleSets', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Servers(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->ServersAsync($request)->wait();
    }

    public function ServersAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServersRestRequest $request)
    {
        return $this->callOperationAsync('Servers', $request);
    }

    public function Sizes(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SizesRestRequest $request)
    {
        return $this->SizesAsync($request)->wait();
    }

    public function SizesAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SizesRestRequest $request)
    {
        return $this->callOperationAsync('Sizes', $request);
    }

    public function Software(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->SoftwareAsync($request)->wait();
    }

    public function SoftwareAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->callOperationAsync('Software', $request);
    }

    public function Software(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->SoftwareAsync($request)->wait();
    }

    public function SoftwareAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->callOperationAsync('Software', $request);
    }

    public function Software(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->SoftwareAsync($request)->wait();
    }

    public function SoftwareAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\SoftwareRestRequest $request)
    {
        return $this->callOperationAsync('Software', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function Vpcs(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->VpcsAsync($request)->wait();
    }

    public function VpcsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\VpcsRestRequest $request)
    {
        return $this->callOperationAsync('Vpcs', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }

    public function ServerActions(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->ServerActionsAsync($request)->wait();
    }

    public function ServerActionsAsync(\Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations\ServerActionsRestRequest $request)
    {
        return $this->callOperationAsync('ServerActions', $request);
    }
}