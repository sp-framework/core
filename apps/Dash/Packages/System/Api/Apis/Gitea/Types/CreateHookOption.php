<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CreateHookOption extends BaseType
{
    private static $propertyTypes = [
        'active' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'active',
        ],
        'branch_filter' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'branch_filter',
        ],
        'config' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\CreateHookOptionConfig',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'config',
        ],
        'events' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'events',
        ],
        'type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'type',
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