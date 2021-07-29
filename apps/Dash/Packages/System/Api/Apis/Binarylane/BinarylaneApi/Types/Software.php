<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Software extends BaseType
{
    private static $propertyTypes = [
        'id' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'id',
        ],
        'enabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enabled',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'description',
        ],
        'cost_per_licence_per_month' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'cost_per_licence_per_month',
        ],
        'minimum_licence_count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'minimum_licence_count',
        ],
        'maximum_licence_count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'maximum_licence_count',
        ],
        'licence_step_count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'licence_step_count',
        ],
        'group' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'group',
        ],
        'supported_operating_systems' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'supported_operating_systems',
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