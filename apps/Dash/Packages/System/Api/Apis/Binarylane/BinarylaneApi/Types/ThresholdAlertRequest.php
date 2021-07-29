<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ThresholdAlertRequest extends BaseType
{
    private static $propertyTypes = [
        'alert_type' => [
          'attribute' => false,
          'elementName' => 'alert_type',
        ],
        'enabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enabled',
        ],
        'value' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'value',
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