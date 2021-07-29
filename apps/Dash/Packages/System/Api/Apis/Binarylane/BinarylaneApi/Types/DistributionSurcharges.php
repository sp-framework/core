<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class DistributionSurcharges extends BaseType
{
    private static $propertyTypes = [
        'surcharge_base_cost' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge_base_cost',
        ],
        'surcharge_per_memory_megabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge_per_memory_megabyte',
        ],
        'surcharge_per_memory_max_megabytes' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge_per_memory_max_megabytes',
        ],
        'surcharge_per_vcpu' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge_per_vcpu',
        ],
        'surcharge_min_vcpu' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'surcharge_min_vcpu',
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