<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ChangeAdvancedFeatures extends BaseType
{
    private static $propertyTypes = [
        'enabled_advanced_features' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\AdvancedFeature',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'enabled_advanced_features',
        ],
        'processor_model' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'processor_model',
        ],
        'automatic_processor_model' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'automatic_processor_model',
        ],
        'machine_type' => [
          'attribute' => false,
          'elementName' => 'machine_type',
        ],
        'automatic_machine_type' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'automatic_machine_type',
        ],
        'type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'type',
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