<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Product extends BaseType
{
    private static $propertyTypes = [
        'aspects' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'aspects',
        ],
        'brand' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'brand',
        ],
        'description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'description',
        ],
        'ean' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ean',
        ],
        'epid' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'epid',
        ],
        'imageUrls' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'imageUrls',
        ],
        'isbn' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'isbn',
        ],
        'mpn' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'mpn',
        ],
        'subtitle' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'subtitle',
        ],
        'title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'title',
        ],
        'upc' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'upc',
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