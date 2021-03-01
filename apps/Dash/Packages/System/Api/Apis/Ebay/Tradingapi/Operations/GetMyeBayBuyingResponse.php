<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetMyeBayBuyingResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'BuyingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyingSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyingSummary',
        ],
        'WatchList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WatchList',
        ],
        'BidList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidList',
        ],
        'BestOfferList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferList',
        ],
        'WonList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedOrderTransactionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'WonList',
        ],
        'LostList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LostList',
        ],
        'FavoriteSearches' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBayFavoriteSearchListType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FavoriteSearches',
        ],
        'FavoriteSellers' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MyeBayFavoriteSellerListType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FavoriteSellers',
        ],
        'SecondChanceOffer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ItemType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SecondChanceOffer',
        ],
        'BidAssistantList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BidGroupArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidAssistantList',
        ],
        'DeletedFromWonList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedOrderTransactionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromWonList',
        ],
        'DeletedFromLostList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromLostList',
        ],
        'UserDefinedList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\UserDefinedListType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'UserDefinedList',
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