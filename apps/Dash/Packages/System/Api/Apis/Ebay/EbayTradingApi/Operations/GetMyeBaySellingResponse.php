<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetMyeBaySellingResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'SellingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingSummary',
        ],
        'ScheduledList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ScheduledList',
        ],
        'ActiveList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ActiveList',
        ],
        'SoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedOrderTransactionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SoldList',
        ],
        'UnsoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnsoldList',
        ],
        'Summary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MyeBaySellingSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Summary',
        ],
        'BidList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidList',
        ],
        'DeletedFromSoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedOrderTransactionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromSoldList',
        ],
        'DeletedFromUnsoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaginatedItemArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromUnsoldList',
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