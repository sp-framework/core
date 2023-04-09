<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Traits\HttpHeadersTrait;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Traits\StatusCodeTrait;
class RepoGetBranchRestResponse extends \System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Types\Branch
{
    use StatusCodeTrait;
    use HttpHeadersTrait;

    private static $propertyTypes = [
        'errors' => [
            'type' => 'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Types\Error',
            'repeatable' => true,
            'attribute' => false,
            'elementName' => 'errors'
        ],
        'warnings' => [
            'type' => 'System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Types\Error',
            'repeatable' => true,
            'attribute' => false,
            'elementName' => 'warnings'
        ]
    ];

    public function __construct(array $values = [], $statusCode = 200, array $headers = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);

        $this->statusCode = (int)$statusCode;

        $this->setHeaders($headers);
    }
}