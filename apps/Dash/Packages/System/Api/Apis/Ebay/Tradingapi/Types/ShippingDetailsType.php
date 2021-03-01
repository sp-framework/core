<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShippingDetailsType extends BaseType
{
    private static $propertyTypes = [
        'AllowPaymentEdit' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AllowPaymentEdit',
        ],
        'ApplyShippingDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ApplyShippingDiscount',
        ],
        'GlobalShipping' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GlobalShipping',
        ],
        'CalculatedShippingRate' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingRateType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CalculatedShippingRate',
        ],
        'ChangePaymentInstructions' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ChangePaymentInstructions',
        ],
        'InsuranceWanted' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InsuranceWanted',
        ],
        'PaymentEdited' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentEdited',
        ],
        'PaymentInstructions' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentInstructions',
        ],
        'SalesTax' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SalesTaxType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SalesTax',
        ],
        'ShippingRateErrorMessage' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingRateErrorMessage',
        ],
        'ShippingRateType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingRateTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingRateType',
        ],
        'ShippingServiceOptions' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingServiceOptionsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingServiceOptions',
        ],
        'InternationalShippingServiceOption' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\InternationalShippingServiceOptionsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'InternationalShippingServiceOption',
        ],
        'ShippingType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShippingTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingType',
        ],
        'SellingManagerSalesRecordNumber' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellingManagerSalesRecordNumber',
        ],
        'ThirdPartyCheckout' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ThirdPartyCheckout',
        ],
        'TaxTable' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\TaxTableType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TaxTable',
        ],
        'GetItFast' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GetItFast',
        ],
        'ShippingServiceUsed' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingServiceUsed',
        ],
        'DefaultShippingCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DefaultShippingCost',
        ],
        'ShippingDiscountProfileID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingDiscountProfileID',
        ],
        'FlatShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FlatShippingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FlatShippingDiscount',
        ],
        'CalculatedShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingDiscountType',
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
        'InternationalShippingDiscountProfileID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalShippingDiscountProfileID',
        ],
        'InternationalFlatShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FlatShippingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalFlatShippingDiscount',
        ],
        'InternationalCalculatedShippingDiscount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CalculatedShippingDiscountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalCalculatedShippingDiscount',
        ],
        'InternationalPromotionalShippingDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalPromotionalShippingDiscount',
        ],
        'PromotionalShippingDiscountDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PromotionalShippingDiscountDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PromotionalShippingDiscountDetails',
        ],
        'CODCost' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AmountType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CODCost',
        ],
        'ExcludeShipToLocation' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExcludeShipToLocation',
        ],
        'SellerExcludeShipToLocationsPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerExcludeShipToLocationsPreference',
        ],
        'ShipmentTrackingDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ShipmentTrackingDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShipmentTrackingDetails',
        ],
        'RateTableDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\RateTableDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RateTableDetails',
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