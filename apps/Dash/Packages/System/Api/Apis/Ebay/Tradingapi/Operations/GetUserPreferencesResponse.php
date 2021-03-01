<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetUserPreferencesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'BidderNoticePreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\BidderNoticePreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BidderNoticePreferences',
        ],
        'CombinedPaymentPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CombinedPaymentPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CombinedPaymentPreferences',
        ],
        'CrossPromotionPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\CrossPromotionPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CrossPromotionPreferences',
        ],
        'SellerPaymentPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerPaymentPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerPaymentPreferences',
        ],
        'SellerFavoriteItemPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerFavoriteItemPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerFavoriteItemPreferences',
        ],
        'EndOfAuctionEmailPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\EndOfAuctionEmailPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EndOfAuctionEmailPreferences',
        ],
        'EmailShipmentTrackingNumberPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EmailShipmentTrackingNumberPreference',
        ],
        'RequiredShipPhoneNumberPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RequiredShipPhoneNumberPreference',
        ],
        'ProStoresPreference' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ProStoresCheckoutPreferenceType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ProStoresPreference',
        ],
        'UnpaidItemAssistancePreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\UnpaidItemAssistancePreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UnpaidItemAssistancePreferences',
        ],
        'SellerExcludeShipToLocationPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerExcludeShipToLocationPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerExcludeShipToLocationPreferences',
        ],
        'PurchaseReminderEmailPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\PurchaseReminderEmailPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PurchaseReminderEmailPreferences',
        ],
        'SellerThirdPartyCheckoutDisabled' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerThirdPartyCheckoutDisabled',
        ],
        'SellerProfilePreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerProfilePreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerProfilePreferences',
        ],
        'SellerReturnPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SellerReturnPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SellerReturnPreferences',
        ],
        'OfferGlobalShippingProgramPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OfferGlobalShippingProgramPreference',
        ],
        'DispatchCutoffTimePreference' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\DispatchCutoffTimePreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DispatchCutoffTimePreference',
        ],
        'GlobalShippingProgramListingPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'GlobalShippingProgramListingPreference',
        ],
        'OverrideGSPServiceWithIntlServicePreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OverrideGSPServiceWithIntlServicePreference',
        ],
        'PickupDropoffSellerPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PickupDropoffSellerPreference',
        ],
        'OutOfStockControlPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'OutOfStockControlPreference',
        ],
        'eBayPLUSPreference' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\eBayPLUSPreferenceType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'eBayPLUSPreference',
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