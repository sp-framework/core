<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class EbayOfferDetailsWithKeys extends BaseType
{
    private static $propertyTypes = [
        'availableQuantity' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'availableQuantity',
        ],
        'categoryId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'categoryId',
        ],
        'charity' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Charity',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'charity',
        ],
        'format' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'format',
        ],
        'hideBuyerDetails' => [
          'attribute' => false,
          'elementName' => 'hideBuyerDetails',
        ],
        'includeCatalogProductDetails' => [
          'attribute' => false,
          'elementName' => 'includeCatalogProductDetails',
        ],
        'listingDescription' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'listingDescription',
        ],
        'listingDuration' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'listingDuration',
        ],
        'listingPolicies' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\ListingPolicies',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'listingPolicies',
        ],
        'listingStartDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'listingStartDate',
        ],
        'lotSize' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'lotSize',
        ],
        'marketplaceId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'marketplaceId',
        ],
        'merchantLocationKey' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'merchantLocationKey',
        ],
        'pricingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\PricingSummary',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'pricingSummary',
        ],
        'quantityLimitPerBuyer' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'quantityLimitPerBuyer',
        ],
        'secondaryCategoryId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'secondaryCategoryId',
        ],
        'sku' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'sku',
        ],
        'storeCategoryNames' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'storeCategoryNames',
        ],
        'tax' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Tax',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'tax',
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