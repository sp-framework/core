<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetCategoriesAspectResponse extends BaseType
{
    private static $propertyTypes = [
        'categoryTreeId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryTreeId',
        ],
        'categoryTreeVersion' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryTreeVersion',
        ],
        'categoryAspects' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\CategoryAspect',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'categoryAspects',
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