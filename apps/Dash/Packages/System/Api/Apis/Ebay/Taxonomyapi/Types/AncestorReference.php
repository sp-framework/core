<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AncestorReference extends BaseType
{
    private static $propertyTypes = [
        'categoryId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryId',
        ],
        'categoryName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryName',
        ],
        'categorySubtreeNodeHref' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categorySubtreeNodeHref',
        ],
        'categoryTreeNodeLevel' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryTreeNodeLevel',
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