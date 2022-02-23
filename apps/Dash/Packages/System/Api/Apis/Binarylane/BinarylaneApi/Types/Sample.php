<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Sample extends BaseType
{
    private static $propertyTypes = [
        'cpu_usage_percent' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'cpu_usage_percent',
        ],
        'cpu_usage_detailed' => [
          'type' => 'number',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'cpu_usage_detailed',
        ],
        'memory_usage_bytes' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'memory_usage_bytes',
        ],
        'network_incoming_kbps' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'network_incoming_kbps',
        ],
        'network_outgoing_kbps' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'network_outgoing_kbps',
        ],
        'storage_usage_megabytes' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_usage_megabytes',
        ],
        'storage_read_kbps' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_read_kbps',
        ],
        'storage_write_kbps' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_write_kbps',
        ],
        'storage_read_requests_per_second' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_read_requests_per_second',
        ],
        'storage_write_requests_per_second' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'storage_write_requests_per_second',
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