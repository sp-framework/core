<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellingManagerOrderStatusType extends BaseType
{
    private static $propertyTypes = [
        'CheckoutStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CheckoutStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutStatus',
        ],
        'PaidStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerPaidStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaidStatus',
        ],
        'ShippedStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SellingManagerShippedStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippedStatus',
        ],
        'eBayPaymentStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPaymentStatus',
        ],
        'PayPalTransactionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalTransactionID',
        ],
        'PaymentMethodUsed' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\BuyerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethodUsed',
        ],
        'FeedbackReceived' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CommentTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackReceived',
        ],
        'FeedbackSent' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeedbackSent',
        ],
        'TotalEmailsSent' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalEmailsSent',
        ],
        'PaymentHoldStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentHoldStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentHoldStatus',
        ],
        'SellerInvoiceNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerInvoiceNumber',
        ],
        'ShippedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippedTime',
        ],
        'PaidTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaidTime',
        ],
        'LastEmailSentTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LastEmailSentTime',
        ],
        'SellerInvoiceTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerInvoiceTime',
        ],
        'IntegratedMerchantCreditCardEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IntegratedMerchantCreditCardEnabled',
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