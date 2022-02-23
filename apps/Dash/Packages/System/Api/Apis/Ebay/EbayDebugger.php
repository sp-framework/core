<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay;

use Apps\Dash\Packages\System\Api\Base\BaseDebugger;

class EbayDebugger extends BaseDebugger
{
    /**
     * @var array $credentialsStrings. RegExp patterns to remove credentials from the debug info.
     */
    private static $credentialsStrings = [
        '/^(X-EBAY-SOA-SECURITY-TOKEN:.*)?$/im' => 'X-EBAY-SOA-SECURITY-TOKEN: SECURITY-TOKEN',
        '/^(X-EBAY-SOA-SECURITY-APPNAME:.*)?$/im' => 'X-EBAY-SOA-SECURITY-APPNAME: SECURITY-APPNAME',
        '/^(X-EBAY-API-AFFILIATE-USER-ID:.*)?$/im' => 'X-EBAY-API-AFFILIATE-USER-ID: AFFILIATE-USER-ID',
        '/^(X-EBAY-API-APP-ID:.*)?$/im' => 'X-EBAY-API-APP-ID: APP-ID',
        '/^(X-EBAY-API-TRACKING-ID:.*)?$/im' => 'X-EBAY-API-TRACKING-ID: TRACKING-ID',
        '/^(X-EBAY-API-TRACKING-PARTNER-CODE:.*)?$/im' => 'X-EBAY-API-TRACKING-PARTNER-CODE: TRACKING-PARTNER-CODE',
        '/^(X-EBAY-API-APP-NAME:.*)?$/im' => 'X-EBAY-API-APP-NAME: APP-NAME',
        '/^(X-EBAY-API-CERT-NAME:.*)?$/im' => 'X-EBAY-API-CERT-NAME: CERT-NAME',
        '/^(X-EBAY-API-DEV-NAME:.*)?$/im' => 'X-EBAY-API-DEV-NAME: DEV-NAME ',
        '/<eBayAuthToken>.*<\/eBayAuthToken>/i' => '<eBayAuthToken>EBAY-AUTH-TOKEN</eBayAuthToken>'
    ];

    /**
     * @param array $config Debug configuration.
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }
}