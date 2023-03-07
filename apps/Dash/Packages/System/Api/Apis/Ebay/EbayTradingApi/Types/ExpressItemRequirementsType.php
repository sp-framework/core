<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ExpressItemRequirementsType extends BaseType
{
    private static $propertyTypes = [
        'SellerExpressEligible' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerExpressEligible',
        ],
        'ExpressOptOut' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressOptOut',
        ],
        'ExpressApproved' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressApproved',
        ],
        'ExpressEligibleListingType' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressEligibleListingType',
        ],
        'ExpressEnabledCategory' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpressEnabledCategory',
        ],
        'EligiblePayPalAccount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EligiblePayPalAccount',
        ],
        'DomesticShippingCost' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DomesticShippingCost',
        ],
        'EligibleReturnPolicy' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EligibleReturnPolicy',
        ],
        'Picture' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Picture',
        ],
        'EligibleItemCondition' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EligibleItemCondition',
        ],
        'PriceAboveMinimum' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PriceAboveMinimum',
        ],
        'PriceBelowMaximum' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PriceBelowMaximum',
        ],
        'EligibleCheckout' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EligibleCheckout',
        ],
        'NoPreapprovedBidderList' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NoPreapprovedBidderList',
        ],
        'NoCharity' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NoCharity',
        ],
        'CombinedShippingDiscount' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CombinedShippingDiscount',
        ],
        'ShipFromEligibleCountry' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShipFromEligibleCountry',
        ],
        'PayPalAccountAcceptsUnconfirmedAddress' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayPalAccountAcceptsUnconfirmedAddress',
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