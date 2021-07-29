<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AvailableAdvancedServerFeatures extends BaseType
{
    private static $propertyTypes = [
        'processor_models' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\CpuModel',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'processor_models',
        ],
        'machine_types' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\VmMachineType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'machine_types',
        ],
        'advanced_features' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\AdvancedFeature',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'advanced_features',
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