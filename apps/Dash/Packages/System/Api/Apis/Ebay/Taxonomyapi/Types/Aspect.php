<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Aspect extends BaseType
{
    private static $propertyTypes = [
        'aspectConstraint' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\AspectConstraint',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'aspectConstraint',
        ],
        'aspectValues' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\AspectValue',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'aspectValues',
        ],
        'localizedAspectName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'localizedAspectName',
        ],
        'relevanceIndicator' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Types\RelevanceIndicator',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'relevanceIndicator',
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