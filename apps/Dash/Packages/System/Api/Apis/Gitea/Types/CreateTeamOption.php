<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CreateTeamOption extends BaseType
{
    private static $propertyTypes = [
        'can_create_org_repo' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'can_create_org_repo',
        ],
        'description' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'description',
        ],
        'includes_all_repositories' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'includes_all_repositories',
        ],
        'name' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'name',
        ],
        'permission' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'permission',
        ],
        'units' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'units',
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