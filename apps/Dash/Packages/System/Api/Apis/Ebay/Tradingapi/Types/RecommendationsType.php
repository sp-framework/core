<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class RecommendationsType extends BaseType
{
    private static $propertyTypes = [
        'CategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CategoryID',
        ],
        'ProductIdentifiers' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProductIdentifiersType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ProductIdentifiers',
        ],
        'NameRecommendation' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NameRecommendationType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'NameRecommendation',
        ],
        'Updated' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Updated',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        if (!array_key_exists(__CLASS__, self::$xmlNamespaces)) {
            self::$xmlNamespaces[__CLASS__] = 'xmlns="urn:ebay:apis:eBLBaseComponents"';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}