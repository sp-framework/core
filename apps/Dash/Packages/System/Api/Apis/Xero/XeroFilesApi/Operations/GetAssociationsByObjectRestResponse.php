<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Traits\HttpHeadersTrait;
use Apps\Dash\Packages\System\Api\Base\Traits\StatusCodeTrait;

class GetAssociationsByObjectRestResponse extends \Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\FileObject
{
    use StatusCodeTrait;
    use HttpHeadersTrait;

    private static $propertyTypes = [
        'errors' => [
            'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\Error',
            'repeatable' => true,
            'attribute' => false,
            'elementName' => 'errors'
        ],
        'warnings' => [
            'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\Error',
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