<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CategoryTreeNode extends BaseType
{
    private static $propertyTypes = [
        'category' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\Category',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'category',
        ],
        'categoryTreeNodeLevel' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryTreeNodeLevel',
        ],
        'childCategoryTreeNodes' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\CategoryTreeNode',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'childCategoryTreeNodes',
        ],
        'leafCategoryTreeNode' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'leafCategoryTreeNode',
        ],
        'parentCategoryTreeNodeHref' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'parentCategoryTreeNodeHref',
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