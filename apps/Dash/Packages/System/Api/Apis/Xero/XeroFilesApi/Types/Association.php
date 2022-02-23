<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class Association extends XeroType
{
    private static $propertyTypes = [
        'FileId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FileId',
        ],
        'ObjectId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ObjectId',
        ],
        'ObjectGroup' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\ObjectGroup',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ObjectGroup',
        ],
        'ObjectType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\ObjectType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ObjectType',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}