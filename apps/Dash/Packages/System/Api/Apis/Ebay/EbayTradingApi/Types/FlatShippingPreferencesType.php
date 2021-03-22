<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class FlatShippingPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'AmountPerAdditionalItem' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPerAdditionalItem',
        ],
        'DeductionAmountPerAdditionalItem' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeductionAmountPerAdditionalItem',
        ],
        'FlatRateInsuranceRangeCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FlatRateInsuranceRangeCostType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'FlatRateInsuranceRangeCost',
        ],
        'FlatShippingRateOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FlatShippingRateOptionCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FlatShippingRateOption',
        ],
        'InsuranceOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\InsuranceOptionCodeType',
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