<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class VpcsRestRequest extends BaseType
{
    private static $propertyTypes = [
        'vpc_id' => [
          'attribute' => false,
          'elementName' => 'vpc_id',
        ],
        'resource_type' => [
          'attribute' => false,
          'elementName' => 'resource_type',
        ],
        'page' => [
          'attribute' => false,
          'elementName' => 'page',
        ],
        'per_page' => [
          'attribute' => false,
          'elementName' => 'per_page',
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