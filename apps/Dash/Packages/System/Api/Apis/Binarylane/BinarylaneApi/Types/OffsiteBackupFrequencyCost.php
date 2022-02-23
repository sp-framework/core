<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class OffsiteBackupFrequencyCost extends BaseType
{
    private static $propertyTypes = [
        'daily_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'daily_per_gigabyte',
        ],
        'weekly_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'weekly_per_gigabyte',
        ],
        'monthly_per_gigabyte' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'monthly_per_gigabyte',
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