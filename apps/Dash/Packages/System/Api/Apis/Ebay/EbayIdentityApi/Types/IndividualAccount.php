<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class IndividualAccount extends BaseType
{
    private static $propertyTypes = [
        'email' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'email',
        ],
        'firstName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'firstName',
        ],
        'lastName' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'lastName',
        ],
        'primaryPhone' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Phone',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'primaryPhone',
        ],
        'registrationAddress' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Address',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'registrationAddress',
        ],
        'secondaryPhone' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Phone',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'secondaryPhone',
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