<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CalculatedShippingPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'CalculatedShippingAmountForEntireOrder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingAmountForEntireOrder',
        ],
        'CalculatedShippingChargeOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingChargeOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingChargeOption',
        ],
        'CalculatedShippingRateOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingRateOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingRateOption',
        ],
        'InsuranceOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\InsuranceOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InsuranceOption',
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