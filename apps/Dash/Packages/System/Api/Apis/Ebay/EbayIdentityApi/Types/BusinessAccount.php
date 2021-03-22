<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class BusinessAccount extends BaseType
{
    private static $propertyTypes = [
        'address' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Address',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'address',
        ],
        'doingBusinessAs' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'doingBusinessAs',
        ],
        'email' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'email',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'primaryContact' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Contact',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'primaryContact',
        ],
        'primaryPhone' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Phone',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'primaryPhone',
        ],
        'secondaryPhone' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types\Phone',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'secondaryPhone',
        ],
        'website' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'website',
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