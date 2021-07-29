<?php

namespace Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class SshKeysResponse extends BaseType
{
    private static $propertyTypes = [
        'ssh_keys' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Binarylane\BinarylaneApi\Types\SshKey',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ssh_keys',
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