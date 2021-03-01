<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ProductSearchPageType extends BaseType
{
    private static $propertyTypes = [
        'SearchCharacteristicsSet' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CharacteristicsSetType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SearchCharacteristicsSet',
        ],
        'SearchType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CharacteristicsSearchCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SearchType',
        ],
        'SortCharacteristics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CharacteristicType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SortCharacteristics',
        ],
        'DataElementSet' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DataElementSetType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DataElementSet',
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