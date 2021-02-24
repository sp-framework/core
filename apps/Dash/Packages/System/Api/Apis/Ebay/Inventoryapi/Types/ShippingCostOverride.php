<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShippingCostOverride extends BaseType
{
    private static $propertyTypes = [
        'additionalShippingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'additionalShippingCost',
        ],
        'priority' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'priority',
        ],
        'shippingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'shippingCost',
        ],
        'shippingServiceType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'shippingServiceType',
        ],
        'surcharge' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Amount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge',
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