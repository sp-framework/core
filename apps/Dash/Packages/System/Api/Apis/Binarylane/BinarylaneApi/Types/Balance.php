<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Balance extends BaseType
{
    private static $propertyTypes = [
        'unbilled_total' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'unbilled_total',
        ],
        'available_credit' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'available_credit',
        ],
        'charges' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\ChargeInformation',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'charges',
        ],
        'generated_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'generated_at',
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