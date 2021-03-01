<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CategoryAspect extends BaseType
{
    private static $propertyTypes = [
        'category' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\Category',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'category',
        ],
        'aspects' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\Aspect',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'aspects',
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