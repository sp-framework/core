<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class InventoryItemGroup extends BaseType
{
    private static $propertyTypes = [
        'aspects' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'aspects',
        ],
        'description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'description',
        ],
        'imageUrls' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'imageUrls',
        ],
        'inventoryItemGroupKey' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'inventoryItemGroupKey',
        ],
        'subtitle' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'subtitle',
        ],
        'title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'title',
        ],
        'variantSKUs' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'variantSKUs',
        ],
        'variesBy' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\VariesBy',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'variesBy',
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