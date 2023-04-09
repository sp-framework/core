<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Services;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\RESTService;

class GiteaBaseService extends RepoRESTService
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

    protected function getRepoHeaders()
    {
        $headers = [];

        // Add required headers first.
        $headers[self::HDR_AUTHORIZATION] = 'token ' . $this->getConfig('user_access_token');

        return $headers;
    }
}