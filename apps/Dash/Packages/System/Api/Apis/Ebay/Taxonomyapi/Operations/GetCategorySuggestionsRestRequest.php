<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetCategorySuggestionsRestRequest extends BaseType
{
    private static $propertyTypes = [
        'category_tree_id' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'category_tree_id',
        ],
        'q' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'q',
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