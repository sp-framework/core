<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class TimeZoneDetailsType extends BaseType
{
    private static $propertyTypes = [
        'TimeZoneID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TimeZoneID',
        ],
        'StandardLabel' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StandardLabel',
        ],
        'StandardOffset' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StandardOffset',
        ],
        'DaylightSavingsLabel' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DaylightSavingsLabel',
        ],
        'DaylightSavingsOffset' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DaylightSavingsOffset',
        ],
        'DaylightSavingsInEffect' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DaylightSavingsInEffect',
        ],
        'DetailVersion' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DetailVersion',
        ],
        'UpdateTime' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdateTime',
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