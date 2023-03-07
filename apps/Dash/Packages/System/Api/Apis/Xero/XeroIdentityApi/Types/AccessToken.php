<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class AccessToken extends BaseType
{
    private static $propertyTypes = [
        'id_token' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'id_token',
        ],
        'access_token' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'access_token',
        ],
        'expires_in' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'expires_in',
        ],
        'token_type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'token_type',
        ],
        'refresh_token' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'refresh_token',
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