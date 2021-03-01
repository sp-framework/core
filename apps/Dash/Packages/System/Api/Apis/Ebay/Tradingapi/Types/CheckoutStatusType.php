<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CheckoutStatusType extends BaseType
{
    private static $propertyTypes = [
        'eBayPaymentStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PaymentStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'eBayPaymentStatus',
        ],
        'LastModifiedTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LastModifiedTime',
        ],
        'PaymentMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethod',
        ],
        'Status' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CompleteStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
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
        'PaymentInstrument' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BuyerPaymentInstrumentCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentInstrument',
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