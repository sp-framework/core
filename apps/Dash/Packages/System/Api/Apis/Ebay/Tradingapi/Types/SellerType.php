<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SellerType extends BaseType
{
    private static $propertyTypes = [
        'PaisaPayStatus' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaisaPayStatus',
        ],
        'AllowPaymentEdit' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AllowPaymentEdit',
        ],
        'BillingCurrency' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CurrencyCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BillingCurrency',
        ],
        'CheckoutEnabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CheckoutEnabled',
        ],
        'CIPBankAccountStored' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CIPBankAccountStored',
        ],
        'GoodStanding' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GoodStanding',
        ],
        'MerchandizingPref' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\MerchandizingPrefCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MerchandizingPref',
        ],
        'QualifiesForB2BVAT' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QualifiesForB2BVAT',
        ],
        'SellerGuaranteeLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerGuaranteeLevelCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerGuaranteeLevel',
        ],
        'SellerLevel' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerLevelCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerLevel',
        ],
        'SellerPaymentAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AddressType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaymentAddress',
        ],
        'SchedulingInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SchedulingInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SchedulingInfo',
        ],
        'StoreOwner' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreOwner',
        ],
        'StoreURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreURL',
        ],
        'SellerBusinessType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerBusinessCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerBusinessType',
        ],
        'RegisteredBusinessSeller' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RegisteredBusinessSeller',
        ],
        'StoreSite' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SiteCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreSite',
        ],
        'PaymentMethod' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerPaymentMethodCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentMethod',
        ],
        'ProStoresPreference' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProStoresCheckoutPreferenceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProStoresPreference',
        ],
        'CharityRegistered' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityRegistered',
        ],
        'SafePaymentExempt' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SafePaymentExempt',
        ],
        'PaisaPayEscrowEMIStatus' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaisaPayEscrowEMIStatus',
        ],
        'CharityAffiliationDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CharityAffiliationDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CharityAffiliationDetails',
        ],
        'TransactionPercent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TransactionPercent',
        ],
        'IntegratedMerchantCreditCardInfo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\IntegratedMerchantCreditCardInfoType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IntegratedMerchantCreditCardInfo',
        ],
        'FeatureEligibility' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\FeatureEligibilityType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FeatureEligibility',
        ],
        'TopRatedSeller' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TopRatedSeller',
        ],
        'TopRatedSellerDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\TopRatedSellerDetailsType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TopRatedSellerDetails',
        ],
        'RecoupmentPolicyConsent' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\RecoupmentPolicyConsentType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RecoupmentPolicyConsent',
        ],
        'DomesticRateTable' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DomesticRateTable',
        ],
        'InternationalRateTable' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InternationalRateTable',
        ],
        'SellereBayPaymentProcessStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellereBayPaymentProcessStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellereBayPaymentProcessStatus',
        ],
        'SellereBayPaymentProcessConsent' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellereBayPaymentProcessConsentCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellereBayPaymentProcessConsent',
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