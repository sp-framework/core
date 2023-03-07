<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class RelistItemResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'Fees' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FeesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Fees',
        ],
        'StartTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StartTime',
        ],
        'EndTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndTime',
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
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DiscountReasonCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DiscountReason',
        ],
        'ProductSuggestions' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ProductSuggestionsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductSuggestions',
        ],
        'ListingRecommendations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ListingRecommendationsType',
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