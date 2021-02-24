<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class OfferSkuResponse extends BaseType
{
    private static $propertyTypes = [
        'errors' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Error',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'errors',
        ],
        'format' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'format',
        ],
        'marketplaceId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'marketplaceId',
        ],
        'offerId' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'offerId',
        ],
        'sku' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'sku',
        ],
        'statusCode' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'statusCode',
        ],
        'warnings' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Error',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'warnings',
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