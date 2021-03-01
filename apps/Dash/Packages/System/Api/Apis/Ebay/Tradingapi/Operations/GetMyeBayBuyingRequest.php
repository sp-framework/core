<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetMyeBayBuyingRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'WatchList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WatchList',
        ],
        'BidList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidList',
        ],
        'BestOfferList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferList',
        ],
        'WonList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WonList',
        ],
        'LostList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LostList',
        ],
        'FavoriteSearches' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBaySelectionType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FavoriteSearches',
        ],
        'FavoriteSellers' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBaySelectionType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FavoriteSellers',
        ],
        'SecondChanceOffer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBaySelectionType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SecondChanceOffer',
        ],
        'BidAssistantList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BidAssistantListType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidAssistantList',
        ],
        'DeletedFromWonList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromWonList',
        ],
        'DeletedFromLostList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromLostList',
        ],
        'BuyingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyingSummary',
        ],
        'UserDefinedLists' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBaySelectionType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserDefinedLists',
        ],
        'HideVariations' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HideVariations',
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

        if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
            self::$requestXmlRootElementNames[__CLASS__] = 'GetMyeBayBuyingRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}