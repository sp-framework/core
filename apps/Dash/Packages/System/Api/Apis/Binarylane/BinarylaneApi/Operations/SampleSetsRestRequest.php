<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SampleSetsRestRequest extends BaseType
{
    private static $propertyTypes = [
        'server_id' => [
          'attribute' => false,
          'elementName' => 'server_id',
        ],
        'data_interval' => [
          'attribute' => false,
          'elementName' => 'data_interval',
        ],
        'start' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'start',
        ],
        'end' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'end',
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