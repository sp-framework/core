<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellerPaymentPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'AlwaysUseThisPaymentAddress' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AlwaysUseThisPaymentAddress',
        ],
        'DisplayPayNowButton' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DisplayPayNowButtonCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DisplayPayNowButton',
        ],
        'PayPalPreferred' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalPreferred',
        ],
        'DefaultPayPalEmailAddress' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DefaultPayPalEmailAddress',
        ],
        'PayPalAlwaysOn' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalAlwaysOn',
        ],
        'SellerPaymentAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaymentAddress',
        ],
        'UPSRateOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\UPSRateOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UPSRateOption',
        ],
        'FedExRateOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FedExRateOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FedExRateOption',
        ],
        'USPSRateOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\USPSRateOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'USPSRateOption',
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