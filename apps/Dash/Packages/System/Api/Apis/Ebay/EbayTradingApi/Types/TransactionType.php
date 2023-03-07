<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class TransactionType extends BaseType
{
    private static $propertyTypes = [
        'AmountPaid' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPaid',
        ],
        'AdjustmentAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AdjustmentAmount',
        ],
        'ConvertedAdjustmentAmount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ConvertedAdjustmentAmount',
        ],
        'Buyer' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UserType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Buyer',
        ],
        'ShippingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingDetails',
        ],
        'ConvertedAmountPaid' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ConvertedAmountPaid',
        ],
        'ConvertedTransactionPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ConvertedTransactionPrice',
        ],
        'CreatedDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreatedDate',
        ],
        'DepositType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DepositTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DepositType',
        ],
        'Item' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Item',
        ],
        'QuantityPurchased' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QuantityPurchased',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionStatusType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'TransactionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionID',
        ],
        'TransactionPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionPrice',
        ],
        'BestOfferSale' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BestOfferSale',
        ],
        'VATPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VATPercent',
        ],
        'ExternalTransaction' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ExternalTransactionType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExternalTransaction',
        ],
        'SellingManagerProductDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerProductDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerProductDetails',
        ],
        'ShippingServiceSelected' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingServiceOptionsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceSelected',
        ],
        'BuyerMessage' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerMessage',
        ],
        'DutchAuctionBid' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DutchAuctionBid',
        ],
        'BuyerPaidStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaidStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerPaidStatus',
        ],
        'SellerPaidStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaidStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaidStatus',
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
        'TotalPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalPrice',
        ],
        'FeedbackLeft' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FeedbackInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackLeft',
        ],
        'FeedbackReceived' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FeedbackInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackReceived',
        ],
        'ContainingOrder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\OrderType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ContainingOrder',
        ],
        'FinalValueFee' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FinalValueFee',
        ],
        'ListingCheckoutRedirectPreference' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ListingCheckoutRedirectPreferenceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ListingCheckoutRedirectPreference',
        ],
        'RefundArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RefundArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RefundArray',
        ],
        'TransactionSiteID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SiteCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionSiteID',
        ],
        'Platform' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionPlatformCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Platform',
        ],
        'CartID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CartID',
        ],
        'SellerContactBuyerByEmail' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerContactBuyerByEmail',
        ],
        'PayPalEmailAddress' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalEmailAddress',
        ],
        'PaisaPayID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaisaPayID',
        ],
        'BuyerGuaranteePrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerGuaranteePrice',
        ],
        'Variation' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\VariationType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Variation',
        ],
        'BuyerCheckoutMessage' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerCheckoutMessage',
        ],
        'TotalTransactionPrice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalTransactionPrice',
        ],
        'Taxes' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TaxesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Taxes',
        ],
        'BundlePurchase' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BundlePurchase',
        ],
        'ActualShippingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ActualShippingCost',
        ],
        'ActualHandlingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ActualHandlingCost',
        ],
        'OrderLineItemID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OrderLineItemID',
        ],
        'eBayPaymentID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPaymentID',
        ],
        'PaymentHoldDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentHoldDetailType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentHoldDetails',
        ],
        'SellerDiscounts' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellerDiscountsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerDiscounts',
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
        'CodiceFiscale' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CodiceFiscale',
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
        'InvoiceSentTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InvoiceSentTime',
        ],
        'UnpaidItem' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UnpaidItemType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnpaidItem',
        ],
        'IntangibleItem' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IntangibleItem',
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
        'ShippingConvenienceCharge' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingConvenienceCharge',
        ],
        'LogisticsPlanType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LogisticsPlanType',
        ],
        'BuyerPackageEnclosures' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerPackageEnclosuresType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerPackageEnclosures',
        ],
        'InventoryReservationID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InventoryReservationID',
        ],
        'ExtendedOrderID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExtendedOrderID',
        ],
        'eBayPlusTransaction' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPlusTransaction',
        ],
        'GiftSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\GiftSummaryType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GiftSummary',
        ],
        'DigitalDeliverySelected' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DigitalDeliverySelectedType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DigitalDeliverySelected',
        ],
        'Gift' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Gift',
        ],
        'GuaranteedShipping' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GuaranteedShipping',
        ],
        'GuaranteedDelivery' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GuaranteedDelivery',
        ],
        'eBayCollectAndRemitTax' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayCollectAndRemitTax',
        ],
        'eBayCollectAndRemitTaxes' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TaxesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayCollectAndRemitTaxes',
        ],
        'Program' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TransactionProgramType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Program',
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