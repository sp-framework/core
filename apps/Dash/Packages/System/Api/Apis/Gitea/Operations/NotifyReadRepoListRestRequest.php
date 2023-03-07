<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class NotifyReadRepoListRestRequest extends BaseType
{
    private static $propertyTypes = [
        'owner' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'owner',
        ],
        'repo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'repo',
        ],
        'all' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'all',
        ],
        'status-types' => [
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'status-types',
        ],
        'to-status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'to-status',
        ],
        'last_read_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'last_read_at',
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