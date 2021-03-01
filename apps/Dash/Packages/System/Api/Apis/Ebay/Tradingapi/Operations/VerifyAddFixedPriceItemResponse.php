<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class VerifyAddFixedPriceItemResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'SKU' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SKU',
        ],
        'Fees' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Fees',
        ],
        'ExpressListing' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressListing',
        ],
        'ExpressItemRequirements' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ExpressItemRequirementsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressItemRequirements',
        ],
        'CategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CategoryID',
        ],
        'Category2ID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Category2ID',
        ],
        'DiscountReason' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DiscountReasonCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DiscountReason',
        ],
        'ListingRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ListingRecommendationsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ListingRecommendations',
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