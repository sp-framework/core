<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SMSSubscriptionType extends BaseType
{
    private static $propertyTypes = [
        'SMSPhone' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SMSPhone',
        ],
        'UserStatus' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SMSSubscriptionUserStatusCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UserStatus',
        ],
        'CarrierID' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\WirelessCarrierIDCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CarrierID',
        ],
        'ErrorCode' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Types\SMSSubscriptionErrorCodeCodeType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ErrorCode',
        ],
        'ItemToUnsubscribe' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ItemToUnsubscribe',
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