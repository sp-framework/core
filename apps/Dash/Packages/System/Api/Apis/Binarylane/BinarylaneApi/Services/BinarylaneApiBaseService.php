<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneRESTService;

class BinarylaneApiBaseService extends GiteaRESTService
{
    protected static $endPoints =
        [
        'primary' => [
          'production' => 'https://api.binarylane.com.au',
        ],
      ];

    const HDR_AUTHORIZATION = 'Authorization';


    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    protected function getBinarylaneHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = 'token ' . $this->getConfig('user_access_token');

        return $headers;
    }
}