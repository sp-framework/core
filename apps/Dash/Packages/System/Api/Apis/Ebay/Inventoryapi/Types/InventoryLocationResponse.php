<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class InventoryLocationResponse extends BaseType
{
    private static $propertyTypes = [
        'location' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\Location',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'location',
        ],
        'locationAdditionalInformation' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'locationAdditionalInformation',
        ],
        'locationInstructions' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'locationInstructions',
        ],
        'locationTypes' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'locationTypes',
        ],
        'locationWebUrl' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'locationWebUrl',
        ],
        'merchantLocationKey' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'merchantLocationKey',
        ],
        'merchantLocationStatus' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'merchantLocationStatus',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'operatingHours' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\OperatingHours',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'operatingHours',
        ],
        'phone' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'phone',
        ],
        'specialHours' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Types\SpecialHours',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'specialHours',
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