<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CompatibleProduct extends BaseType
{
    private static $propertyTypes = [
        'compatibilityProperties' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\NameValueList',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'compatibilityProperties',
        ],
        'notes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'notes',
        ],
        'productFamilyProperties' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\ProductFamilyProperties',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'productFamilyProperties',
        ],
        'productIdentifier' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\ProductIdentifier',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'productIdentifier',
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