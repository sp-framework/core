<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CategorySuggestion extends BaseType
{
    private static $propertyTypes = [
        'category' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\Category',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'category',
        ],
        'categoryTreeNodeAncestors' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\AncestorReference',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'categoryTreeNodeAncestors',
        ],
        'categoryTreeNodeLevel' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryTreeNodeLevel',
        ],
        'relevancy' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'relevancy',
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