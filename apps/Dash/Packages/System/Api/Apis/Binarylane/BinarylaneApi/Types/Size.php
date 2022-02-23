<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Size extends BaseType
{
    private static $propertyTypes = [
        'slug' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'slug',
        ],
        'description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'description',
        ],
        'cpu_description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'cpu_description',
        ],
        'storage_description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_description',
        ],
        'size_type' => [
          'attribute' => false,
          'elementName' => 'size_type',
        ],
        'available' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'available',
        ],
        'regions' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'regions',
        ],
        'regions_out_of_stock' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'regions_out_of_stock',
        ],
        'price_monthly' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'price_monthly',
        ],
        'price_hourly' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'price_hourly',
        ],
        'disk' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'disk',
        ],
        'memory' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'memory',
        ],
        'transfer' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'transfer',
        ],
        'excess_transfer_cost_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'excess_transfer_cost_per_gigabyte',
        ],
        'vcpus' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vcpus',
        ],
        'vcpu_units' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vcpu_units',
        ],
        'options' => [
          'attribute' => false,
          'elementName' => 'options',
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