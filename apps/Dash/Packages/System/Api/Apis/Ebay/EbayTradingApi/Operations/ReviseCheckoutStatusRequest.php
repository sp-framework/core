<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class ReviseCheckoutStatusRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemID',
        ],
        'TransactionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionID',
        ],
        'OrderID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderID',
        ],
        'AmountPaid' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPaid',
        ],
        'PaymentMethodUsed' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethodUsed',
        ],
        'CheckoutStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CompleteStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutStatus',
        ],
        'ShippingService' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingService',
        ],
        'ShippingIncludedInTax' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingIncludedInTax',
        ],
        'CheckoutMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CheckoutMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutMethod',
        ],
        'InsuranceType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\InsuranceSelectedCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InsuranceType',
        ],
        'PaymentStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RCSPaymentStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentStatus',
        ],
        'AdjustmentAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AdjustmentAmount',
        ],
        'ShippingAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingAddress',
        ],
        'BuyerID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerID',
        ],
        'ShippingInsuranceCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingInsuranceCost',
        ],
        'SalesTax' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SalesTax',
        ],
        'ShippingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingCost',
        ],
        'EncryptedID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EncryptedID',
        ],
        'ExternalTransaction' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ExternalTransactionType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExternalTransaction',
        ],
        'MultipleSellerPaymentID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MultipleSellerPaymentID',
        ],
        'CODCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CODCost',
        ],
        'OrderLineItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderLineItemID',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'ReviseCheckoutStatusRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}