<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class VariationType extends BaseType
{
    private static $propertyTypes = [
        'SKU' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SKU',
        ],
        'StartPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StartPrice',
        ],
        'Quantity' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Quantity',
        ],
        'VariationSpecifics' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NameValueListArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationSpecifics',
        ],
        'UnitsAvailable' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnitsAvailable',
        ],
        'UnitCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnitCost',
        ],
        'SellingStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingStatusType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingStatus',
        ],
        'VariationTitle' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationTitle',
        ],
        'VariationViewItemURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationViewItemURL',
        ],
        'Delete' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Delete',
        ],
        'SellingManagerProductInventoryStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellingManagerProductInventoryStatusType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerProductInventoryStatus',
        ],
        'WatchCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WatchCount',
        ],
        'PrivateNotes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PrivateNotes',
        ],
        'DiscountPriceInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DiscountPriceInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DiscountPriceInfo',
        ],
        'VariationProductListingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\VariationProductListingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationProductListingDetails',
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