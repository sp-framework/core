<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayIdentityApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Phone extends BaseType
{
    private static $propertyTypes = [
        'countryCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'countryCode',
        ],
        'number' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'number',
        ],
        'phoneType' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'phoneType',
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