<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AccountEntryType extends BaseType
{
    private static $propertyTypes = [
        'AccountDetailsEntryType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AccountDetailEntryCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountDetailsEntryType',
        ],
        'Description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Description',
        ],
        'Balance' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Balance',
        ],
        'Date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Date',
        ],
        'GrossDetailAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GrossDetailAmount',
        ],
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'Memo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Memo',
        ],
        'ConversionRate' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ConversionRate',
        ],
        'NetDetailAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NetDetailAmount',
        ],
        'RefNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefNumber',
        ],
        'VATPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VATPercent',
        ],
        'Title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Title',
        ],
        'OrderLineItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderLineItemID',
        ],
        'TransactionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionID',
        ],
        'ReceivedTopRatedDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReceivedTopRatedDiscount',
        ],
        'OrderId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderId',
        ],
        'DiscountDetail' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DiscountDetailType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DiscountDetail',
        ],
        'Netted' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Netted',
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