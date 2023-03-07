<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ShippingPackageInfoType extends BaseType
{
    private static $propertyTypes = [
        'StoreID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StoreID',
        ],
        'ShippingTrackingEvent' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ShippingTrackingEvent',
        ],
        'ScheduledDeliveryTimeMin' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ScheduledDeliveryTimeMin',
        ],
        'ScheduledDeliveryTimeMax' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ScheduledDeliveryTimeMax',
        ],
        'ActualDeliveryTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ActualDeliveryTime',
        ],
        'EstimatedDeliveryTimeMin' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EstimatedDeliveryTimeMin',
        ],
        'EstimatedDeliveryTimeMax' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'EstimatedDeliveryTimeMax',
        ],
        'HandleByTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HandleByTime',
        ],
        'MinNativeEstimatedDeliveryTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MinNativeEstimatedDeliveryTime',
        ],
        'MaxNativeEstimatedDeliveryTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'MaxNativeEstimatedDeliveryTime',
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