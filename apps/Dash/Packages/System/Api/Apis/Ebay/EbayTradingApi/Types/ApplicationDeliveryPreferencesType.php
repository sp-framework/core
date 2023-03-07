<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ApplicationDeliveryPreferencesType extends BaseType
{
    private static $propertyTypes = [
        'ApplicationURL' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ApplicationURL',
        ],
        'ApplicationEnable' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\EnableCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ApplicationEnable',
        ],
        'AlertEmail' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AlertEmail',
        ],
        'AlertEnable' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\EnableCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AlertEnable',
        ],
        'NotificationPayloadType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\NotificationPayloadTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'NotificationPayloadType',
        ],
        'DeviceType' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DeviceTypeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeviceType',
        ],
        'PayloadVersion' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PayloadVersion',
        ],
        'DeliveryURLDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types\DeliveryURLDetailType',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'DeliveryURLDetails',
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