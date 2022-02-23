<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SampleSet extends BaseType
{
    private static $propertyTypes = [
        'server_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'server_id',
        ],
        'period' => [
          'attribute' => false,
          'elementName' => 'period',
        ],
        'average' => [
          'attribute' => false,
          'elementName' => 'average',
        ],
        'maximum_memory_megabytes' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'maximum_memory_megabytes',
        ],
        'maximum_storage_gigabytes' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'maximum_storage_gigabytes',
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