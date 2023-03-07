<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ProductSearchType extends BaseType
{
    private static $propertyTypes = [
        'ProductSearchID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductSearchID',
        ],
        'AttributeSetID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AttributeSetID',
        ],
        'ProductFinderID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductFinderID',
        ],
        'ProductID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductID',
        ],
        'SortAttributeID' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SortAttributeID',
        ],
        'MaxChildrenPerFamily' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxChildrenPerFamily',
        ],
        'SearchAttributes' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SearchAttributesType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SearchAttributes',
        ],
        'Pagination' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Pagination',
        ],
        'AvailableItemsOnly' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AvailableItemsOnly',
        ],
        'QueryKeywords' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QueryKeywords',
        ],
        'CharacteristicSetIDs' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CharacteristicSetIDsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharacteristicSetIDs',
        ],
        'ProductReferenceID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductReferenceID',
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