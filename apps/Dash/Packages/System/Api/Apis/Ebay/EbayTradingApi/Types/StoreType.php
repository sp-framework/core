<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class StoreType extends BaseType
{
    private static $propertyTypes = [
        'Name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Name',
        ],
        'URLPath' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'URLPath',
        ],
        'URL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'URL',
        ],
        'SubscriptionLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreSubscriptionLevelCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubscriptionLevel',
        ],
        'Description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Description',
        ],
        'Logo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreLogoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Logo',
        ],
        'Theme' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreThemeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Theme',
        ],
        'HeaderStyle' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreHeaderStyleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HeaderStyle',
        ],
        'HomePage' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HomePage',
        ],
        'ItemListLayout' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreItemListLayoutCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemListLayout',
        ],
        'ItemListSortOrder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreItemListSortOrderCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemListSortOrder',
        ],
        'CustomHeaderLayout' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreCustomHeaderLayoutCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CustomHeaderLayout',
        ],
        'CustomHeader' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CustomHeader',
        ],
        'ExportListings' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExportListings',
        ],
        'CustomCategories' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreCustomCategoryArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CustomCategories',
        ],
        'CustomListingHeader' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\StoreCustomListingHeaderType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CustomListingHeader',
        ],
        'MerchDisplay' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MerchDisplayCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MerchDisplay',
        ],
        'LastOpenedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LastOpenedTime',
        ],
        'TitleWithCompatibility' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TitleWithCompatibility',
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