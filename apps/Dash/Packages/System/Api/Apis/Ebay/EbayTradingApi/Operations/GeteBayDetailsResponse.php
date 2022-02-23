<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class GeteBayDetailsResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'CountryDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CountryDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'CountryDetails',
        ],
        'CurrencyDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\CurrencyDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'CurrencyDetails',
        ],
        'DispatchTimeMaxDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DispatchTimeMaxDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DispatchTimeMaxDetails',
        ],
        'PaymentOptionDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\PaymentOptionDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'PaymentOptionDetails',
        ],
        'RegionDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RegionDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'RegionDetails',
        ],
        'ShippingLocationDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingLocationDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingLocationDetails',
        ],
        'ShippingServiceDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingServiceDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingServiceDetails',
        ],
        'SiteDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SiteDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'SiteDetails',
        ],
        'TaxJurisdiction' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TaxJurisdictionType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'TaxJurisdiction',
        ],
        'URLDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\URLDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'URLDetails',
        ],
        'TimeZoneDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\TimeZoneDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'TimeZoneDetails',
        ],
        'ItemSpecificDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ItemSpecificDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ItemSpecificDetails',
        ],
        'UnitOfMeasurementDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\UnitOfMeasurementDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'UnitOfMeasurementDetails',
        ],
        'RegionOfOriginDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RegionOfOriginDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'RegionOfOriginDetails',
        ],
        'ShippingPackageDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingPackageDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingPackageDetails',
        ],
        'ShippingCarrierDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingCarrierDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingCarrierDetails',
        ],
        'ReturnPolicyDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ReturnPolicyDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReturnPolicyDetails',
        ],
        'InternationalReturnPolicyDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ReturnPolicyDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalReturnPolicyDetails',
        ],
        'ListingStartPriceDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ListingStartPriceDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ListingStartPriceDetails',
        ],
        'BuyerRequirementDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\SiteBuyerRequirementDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'BuyerRequirementDetails',
        ],
        'ListingFeatureDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ListingFeatureDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ListingFeatureDetails',
        ],
        'VariationDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\VariationDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'VariationDetails',
        ],
        'ExcludeShippingLocationDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ExcludeShippingLocationDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ExcludeShippingLocationDetails',
        ],
        'UpdateTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdateTime',
        ],
        'RecoupmentPolicyDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\RecoupmentPolicyDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'RecoupmentPolicyDetails',
        ],
        'ShippingCategoryDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ShippingCategoryDetailsType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ShippingCategoryDetails',
        ],
        'ProductDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ProductDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProductDetails',
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