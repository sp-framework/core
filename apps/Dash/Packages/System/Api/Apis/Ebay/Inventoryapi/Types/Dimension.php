<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Dimension extends BaseType
{
    private static $propertyTypes = [
        'height' => [
          'attribute' => false,
          'elementName' => 'height',
        ],
        'length' => [
          'attribute' => false,
          'elementName' => 'length',
        ],
        'unit' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'unit',
        ],
        'width' => [
          'attribute' => false,
          'elementName' => 'width',
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