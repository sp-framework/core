<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class VariesBy extends BaseType
{
    private static $propertyTypes = [
        'aspectsImageVariesBy' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'aspectsImageVariesBy',
        ],
        'specifications' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Specification',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'specifications',
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