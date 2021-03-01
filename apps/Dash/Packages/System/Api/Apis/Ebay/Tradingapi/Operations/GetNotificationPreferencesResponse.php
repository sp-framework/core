<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations;

class GetNotificationPreferencesResponse extends \Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\AbstractResponseType
{
    private static $propertyTypes = [
        'ApplicationDeliveryPreferences' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\ApplicationDeliveryPreferencesType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ApplicationDeliveryPreferences',
        ],
        'DeliveryURLName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryURLName',
        ],
        'UserDeliveryPreferenceArray' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NotificationEnableArrayType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserDeliveryPreferenceArray',
        ],
        'UserData' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NotificationUserDataType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserData',
        ],
        'EventProperty' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\NotificationEventPropertyType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'EventProperty',
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