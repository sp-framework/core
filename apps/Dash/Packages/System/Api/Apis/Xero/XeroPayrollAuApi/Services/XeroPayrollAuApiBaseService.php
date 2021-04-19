<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroPayrollAuApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroRESTService;

class XeroPayrollAuApiBaseService extends XeroRESTService
{
    protected static $endPoints =
        [
        'primary' => [
          'production' => 'https://api.xero.com/payroll.xro/1.0',
        ],
      ];

    const HDR_AUTHORIZATION = 'Authorization';

    const HDR_XERO_TENANT_ID = 'xero-tenant-id';

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    protected function getXeroHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = 'Bearer ' . $this->getConfig('user_access_token');

        $headers[self::HDR_XERO_TENANT_ID] = $this->getConfig("tenantId");

        return $headers;
    }
}