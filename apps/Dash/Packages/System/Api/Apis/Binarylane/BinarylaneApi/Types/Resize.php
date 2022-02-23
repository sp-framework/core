<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Resize extends BaseType
{
    private static $propertyTypes = [
        'size' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'size',
        ],
        'options' => [
          'attribute' => false,
          'elementName' => 'options',
        ],
        'change_image' => [
          'attribute' => false,
          'elementName' => 'change_image',
        ],
        'change_licenses' => [
          'attribute' => false,
          'elementName' => 'change_licenses',
        ],
        'pre_action_backup' => [
          'attribute' => false,
          'elementName' => 'pre_action_backup',
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