<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShipToLocationAvailabilityWithAll extends BaseType
{
    private static $propertyTypes = [
        'allocationByFormat' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\FormatAllocation',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'allocationByFormat',
        ],
        'availabilityDistributions' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\AvailabilityDistribution',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'availabilityDistributions',
        ],
        'quantity' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'quantity',
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