<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaRESTService;

class GiteaApiBaseService extends GiteaRESTService
{
    protected static $endPoints =
        [
        'primary' => [
          'production' => '/api/v1',
        ],
      ];

    const HDR_AUTHORIZATION = 'Authorization';


    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    protected function getGiteaHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = 'token ' . $this->getConfig('user_access_token');

        return $headers;
    }
}