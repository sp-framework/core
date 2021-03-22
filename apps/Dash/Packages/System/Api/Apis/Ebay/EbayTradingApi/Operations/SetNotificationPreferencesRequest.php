<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Operations;

class SetNotificationPreferencesRequest extends \Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\AbstractRequestType
{
    private static $propertyTypes = [
        'ApplicationDeliveryPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\ApplicationDeliveryPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ApplicationDeliveryPreferences',
        ],
        'UserDeliveryPreferenceArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\NotificationEnableArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserDeliveryPreferenceArray',
        ],
        'UserData' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\NotificationUserDataType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserData',
        ],
        'EventProperty' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\NotificationEventPropertyType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'EventProperty',
        ],
        'DeliveryURLName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryURLName',
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
            self::$requestXmlRootElementNames[__CLASS__] = 'SetNotificationPreferencesRequest';
        }

        $this->setValues(__CLASS__, $childValues);
    }
}