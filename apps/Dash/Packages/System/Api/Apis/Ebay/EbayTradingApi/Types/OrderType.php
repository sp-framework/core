<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class OrderType extends BaseType
{
    private static $propertyTypes = [
        'OrderID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderID',
        ],
        'OrderStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\OrderStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderStatus',
        ],
        'AdjustmentAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AdjustmentAmount',
        ],
        'AmountPaid' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPaid',
        ],
        'AmountSaved' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountSaved',
        ],
        'CheckoutStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CheckoutStatusType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutStatus',
        ],
        'ShippingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingDetails',
        ],
        'CreatingUserRole' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TradingRoleCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreatingUserRole',
        ],
        'CreatedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreatedTime',
        ],
        'PaymentMethods' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerPaymentMethodCodeType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PaymentMethods',
        ],
        'SellerEmail' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerEmail',
        ],
        'ShippingAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingAddress',
        ],
        'ShippingServiceSelected' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingServiceOptionsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceSelected',
        ],
        'Subtotal' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Subtotal',
        ],
        'Total' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Total',
        ],
        'ExternalTransaction' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ExternalTransactionType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExternalTransaction',
        ],
        'TransactionArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionArray',
        ],
        'BuyerUserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerUserID',
        ],
        'PaidTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaidTime',
        ],
        'ShippedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippedTime',
        ],
        'IntegratedMerchantCreditCardEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IntegratedMerchantCreditCardEnabled',
        ],
        'BundlePurchase' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BundlePurchase',
        ],
        'BuyerCheckoutMessage' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerCheckoutMessage',
        ],
        'EIASToken' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EIASToken',
        ],
        'PaymentHoldStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentHoldStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentHoldStatus',
        ],
        'PaymentHoldDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentHoldDetailType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentHoldDetails',
        ],
        'RefundAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundAmount',
        ],
        'RefundStatus' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundStatus',
        ],
        'RefundArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RefundArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundArray',
        ],
        'IsMultiLegShipping' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsMultiLegShipping',
        ],
        'MultiLegShippingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\MultiLegShippingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MultiLegShippingDetails',
        ],
        'MonetaryDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentsInformationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MonetaryDetails',
        ],
        'PickupDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PickupDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PickupDetails',
        ],
        'PickupMethodSelected' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PickupMethodSelectedType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PickupMethodSelected',
        ],
        'SellerUserID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerUserID',
        ],
        'SellerEIASToken' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerEIASToken',
        ],
        'CancelReason' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CancelReason',
        ],
        'CancelStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CancelStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CancelStatus',
        ],
        'CancelReasonDetails' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CancelReasonDetails',
        ],
        'ShippingConvenienceCharge' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingConvenienceCharge',
        ],
        'CancelDetail' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CancelDetailType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'CancelDetail',
        ],
        'LogisticsPlanType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LogisticsPlanType',
        ],
        'BuyerTaxIdentifier' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TaxIdentifierType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'BuyerTaxIdentifier',
        ],
        'BuyerPackageEnclosures' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerPackageEnclosuresType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerPackageEnclosures',
        ],
        'ExtendedOrderID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExtendedOrderID',
        ],
        'ContainseBayPlusTransaction' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContainseBayPlusTransaction',
        ],
        'eBayCollectAndRemitTax' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayCollectAndRemitTax',
        ],
        'OrderLineItemCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderLineItemCount',
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