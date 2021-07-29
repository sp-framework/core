<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CreateServerRequest extends BaseType
{
    private static $propertyTypes = [
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'backups' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'backups',
        ],
        'ipv6' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ipv6',
        ],
        'size' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'size',
        ],
        'image' => [
          'attribute' => false,
          'elementName' => 'image',
        ],
        'region' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'region',
        ],
        'vpc_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'vpc_id',
        ],
        'ssh_keys' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ssh_keys',
        ],
        'new_ssh_key' => [
          'attribute' => false,
          'elementName' => 'new_ssh_key',
        ],
        'options' => [
          'attribute' => false,
          'elementName' => 'options',
        ],
        'licenses' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\License',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'licenses',
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