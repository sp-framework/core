<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model;

use OpenApi\Attributes as OA;
use System\Base\BaseModel;

#[OA\Schema(title: 'Murls Model', description: 'Murls Model')]
class BasepackagesMurls extends BaseModel
{
    #[OA\Property(nullable: false, title: 'ID', description: 'ID', type: 'integer', format: 'int64')]
    public $id;

    #[OA\Property(nullable: false, title: 'App ID', description: 'App ID', type: 'integer', format: 'int64')]
    public $app_id;

    #[OA\Property(nullable: false, title: 'Domain ID', description: 'Domain ID', type: 'integer', format: 'int64')]
    public $domain_id;

    #[OA\Property(nullable: false, title: 'Account ID', description: 'Account ID', type: 'integer', format: 'int64')]
    public $account_id;

    #[OA\Property(nullable: false, title: 'url', type: 'string', description: 'url')]
    public $url;

    #[OA\Property(nullable: false, title: 'Murl', maximum: 50, type: 'string', description: 'Murl')]
    public $murl;

    #[OA\Property(nullable: true, title: 'Hits', description: 'Hits', type: 'integer', format: 'int64')]
    public $hits;

    #[OA\Property(nullable: false, title: 'Valid Till', maximum: 50, description: 'Valid Till', type: 'string', format: 'datetime')]
    public $valid_till;
}