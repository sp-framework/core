<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class RefreshToken extends BaseType
{
    private static $propertyTypes = [
        'grant_type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'grant_type',
        ],
        'refresh_token' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'refresh_token',
        ],
        'client_id' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'client_id',
        ],
        'client_secret' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'client_secret',
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