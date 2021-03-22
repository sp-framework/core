<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetMyeBaySellingRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ScheduledList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ScheduledList',
        ],
        'ActiveList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ActiveList',
        ],
        'SoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SoldList',
        ],
        'UnsoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnsoldList',
        ],
        'BidList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidList',
        ],
        'DeletedFromSoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromSoldList',
        ],
        'DeletedFromUnsoldList' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeletedFromUnsoldList',
        ],
        'SellingSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemListCustomizationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingSummary',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'GetMyeBaySellingRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}