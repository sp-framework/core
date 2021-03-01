<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetUserPreferencesRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ShowBidderNoticePreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowBidderNoticePreferences',
        ],
        'ShowCombinedPaymentPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowCombinedPaymentPreferences',
        ],
        'ShowCrossPromotionPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowCrossPromotionPreferences',
        ],
        'ShowSellerPaymentPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowSellerPaymentPreferences',
        ],
        'ShowEndOfAuctionEmailPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowEndOfAuctionEmailPreferences',
        ],
        'ShowSellerFavoriteItemPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowSellerFavoriteItemPreferences',
        ],
        'ShowProStoresPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowProStoresPreferences',
        ],
        'ShowEmailShipmentTrackingNumberPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowEmailShipmentTrackingNumberPreference',
        ],
        'ShowRequiredShipPhoneNumberPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowRequiredShipPhoneNumberPreference',
        ],
        'ShowSellerExcludeShipToLocationPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowSellerExcludeShipToLocationPreference',
        ],
        'ShowUnpaidItemAssistancePreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowUnpaidItemAssistancePreference',
        ],
        'ShowPurchaseReminderEmailPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowPurchaseReminderEmailPreferences',
        ],
        'ShowUnpaidItemAssistanceExclusionList' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowUnpaidItemAssistanceExclusionList',
        ],
        'ShowSellerProfilePreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowSellerProfilePreferences',
        ],
        'ShowSellerReturnPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowSellerReturnPreferences',
        ],
        'ShowGlobalShippingProgramPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowGlobalShippingProgramPreference',
        ],
        'ShowDispatchCutoffTimePreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowDispatchCutoffTimePreferences',
        ],
        'ShowGlobalShippingProgramListingPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowGlobalShippingProgramListingPreference',
        ],
        'ShowOverrideGSPServiceWithIntlServicePreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowOverrideGSPServiceWithIntlServicePreference',
        ],
        'ShowPickupDropoffPreferences' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowPickupDropoffPreferences',
        ],
        'ShowOutOfStockControlPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShowOutOfStockControlPreference',
        ],
        'ShoweBayPLUSPreference' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShoweBayPLUSPreference',
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

        if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
            self::$requestXmlRootElementNames[__CLASS__] = 'GetUserPreferencesRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}