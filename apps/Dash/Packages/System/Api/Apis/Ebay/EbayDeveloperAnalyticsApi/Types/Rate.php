<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Rate extends BaseType
{
    private static $propertyTypes = [
        'limit' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'limit',
        ],
        'remaining' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'remaining',
        ],
        'reset' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'reset',
        ],
        'timeWindow' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'timeWindow',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}