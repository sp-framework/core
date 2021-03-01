<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class TransactionStatusType extends BaseType
{
    private static $propertyTypes = [
        'eBayPaymentStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaymentStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPaymentStatus',
        ],
        'CheckoutStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CheckoutStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutStatus',
        ],
        'LastTimeModified' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LastTimeModified',
        ],
        'PaymentMethodUsed' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethodUsed',
        ],
        'CompleteStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CompleteStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CompleteStatus',
        ],
        'BuyerSelectedShipping' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BuyerSelectedShipping',
        ],
        'PaymentHoldStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaymentHoldStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentHoldStatus',
        ],
        'IntegratedMerchantCreditCardEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IntegratedMerchantCreditCardEnabled',
        ],
        'eBayPaymentMismatchDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\eBayPaymentMismatchDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPaymentMismatchDetails',
        ],
        'InquiryStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\InquiryStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InquiryStatus',
        ],
        'ReturnStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ReturnStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReturnStatus',
        ],
        'PaymentInstrument' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyerPaymentInstrumentCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentInstrument',
        ],
        'DigitalStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DigitalStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DigitalStatus',
        ],
        'CancelStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CancelStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CancelStatus',
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