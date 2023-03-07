<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class RespondToBestOfferRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'BestOfferID' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'BestOfferID',
        ],
        'Action' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BestOfferActionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Action',
        ],
        'SellerResponse' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerResponse',
        ],
        'CounterOfferPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CounterOfferPrice',
        ],
        'CounterOfferQuantity' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CounterOfferQuantity',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'RespondToBestOfferRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}