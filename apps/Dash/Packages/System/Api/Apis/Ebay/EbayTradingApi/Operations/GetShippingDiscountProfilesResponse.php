<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GetShippingDiscountProfilesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'CurrencyID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CurrencyCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyID',
        ],
        'FlatShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\FlatShippingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FlatShippingDiscount',
        ],
        'CalculatedShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CalculatedShippingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingDiscount',
        ],
        'PromotionalShippingDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionalShippingDiscount',
        ],
        'CalculatedHandlingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CalculatedHandlingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedHandlingDiscount',
        ],
        'PromotionalShippingDiscountDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PromotionalShippingDiscountDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionalShippingDiscountDetails',
        ],
        'ShippingInsurance' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingInsuranceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingInsurance',
        ],
        'InternationalShippingInsurance' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingInsuranceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalShippingInsurance',
        ],
        'CombinedDuration' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CombinedPaymentPeriodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CombinedDuration',
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