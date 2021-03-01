<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class MyeBayFavoriteSearchType extends BaseType
{
    private static $propertyTypes = [
        'SearchName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SearchName',
        ],
        'SearchQuery' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SearchQuery',
        ],
        'QueryKeywords' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QueryKeywords',
        ],
        'CategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CategoryID',
        ],
        'ItemSort' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SimpleItemSortCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemSort',
        ],
        'SortOrder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SortOrderCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SortOrder',
        ],
        'EndTimeFrom' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndTimeFrom',
        ],
        'EndTimeTo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndTimeTo',
        ],
        'MaxDistance' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxDistance',
        ],
        'PostalCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PostalCode',
        ],
        'ItemType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemType',
        ],
        'PriceMax' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PriceMax',
        ],
        'PriceMin' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PriceMin',
        ],
        'Currency' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CurrencyCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Currency',
        ],
        'BidCountMax' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidCountMax',
        ],
        'BidCountMin' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidCountMin',
        ],
        'SearchFlag' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SearchFlagCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SearchFlag',
        ],
        'PaymentMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaymentMethodSearchCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethod',
        ],
        'PreferredLocation' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PreferredLocationCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PreferredLocation',
        ],
        'SellerID' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SellerID',
        ],
        'SellerIDExclude' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SellerIDExclude',
        ],
        'ItemsAvailableTo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CountryCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemsAvailableTo',
        ],
        'ItemsLocatedIn' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CountryCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemsLocatedIn',
        ],
        'SellerBusinessType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerBusinessCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerBusinessType',
        ],
        'Condition' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemConditionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Condition',
        ],
        'Quantity' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Quantity',
        ],
        'QuantityOperator' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\QuantityOperatorCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QuantityOperator',
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