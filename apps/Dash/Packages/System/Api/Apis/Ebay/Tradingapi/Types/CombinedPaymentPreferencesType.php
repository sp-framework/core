<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CombinedPaymentPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'CalculatedShippingPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingPreferences',
        ],
        'CombinedPaymentOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CombinedPaymentOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CombinedPaymentOption',
        ],
        'CombinedPaymentPeriod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CombinedPaymentPeriodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CombinedPaymentPeriod',
        ],
        'FlatShippingPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FlatShippingPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FlatShippingPreferences',
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