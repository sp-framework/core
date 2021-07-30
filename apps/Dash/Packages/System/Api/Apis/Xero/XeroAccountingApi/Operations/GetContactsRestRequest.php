<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetContactsRestRequest extends BaseType
{
    private static $propertyTypes = [
        'ifModifiedSince' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'ifModifiedSince',
        ],
        'where' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'where',
        ],
        'order' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'order',
        ],
        'IDs' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'IDs',
        ],
        'page' => [
          'attribute' => false,
          'elementName' => 'page',
        ],
        'includeArchived' => [
          'attribute' => false,
          'elementName' => 'includeArchived',
        ],
        'summaryOnly' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'summaryOnly',
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