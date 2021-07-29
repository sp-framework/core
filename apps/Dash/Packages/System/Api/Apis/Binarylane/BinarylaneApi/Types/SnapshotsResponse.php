<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SnapshotsResponse extends BaseType
{
    private static $propertyTypes = [
        'snapshots' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\Image',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'snapshots',
        ],
        'meta' => [
          'attribute' => false,
          'elementName' => 'meta',
        ],
        'links' => [
          'attribute' => false,
          'elementName' => 'links',
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