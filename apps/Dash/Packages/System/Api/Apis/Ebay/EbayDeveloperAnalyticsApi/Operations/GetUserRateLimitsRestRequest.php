<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayDeveloperAnalyticsApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetUserRateLimitsRestRequest extends BaseType
{
    private static $propertyTypes = [
        'api_context' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'api_context',
        ],
        'api_name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'api_name',
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