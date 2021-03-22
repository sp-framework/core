<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class UserResponse extends BaseType
{
    private static $propertyTypes = [
        'accountType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'accountType',
        ],
        'businessAccount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\BusinessAccount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'businessAccount',
        ],
        'individualAccount' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\IndividualAccount',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'individualAccount',
        ],
        'registrationMarketplaceId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'registrationMarketplaceId',
        ],
        'status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'status',
        ],
        'userId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'userId',
        ],
        'username' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'username',
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