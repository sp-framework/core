<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Network extends BaseType
{
    private static $propertyTypes = [
        'ip_address' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ip_address',
        ],
        'netmask' => [
          'attribute' => false,
          'elementName' => 'netmask',
        ],
        'gateway' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'gateway',
        ],
        'type' => [
          'attribute' => false,
          'elementName' => 'type',
        ],
        'reverse_name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'reverse_name',
        ],
        'nat_target' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'nat_target',
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