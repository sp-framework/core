<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PRBranchInfo extends BaseType
{
    private static $propertyTypes = [
        'label' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'label',
        ],
        'ref' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ref',
        ],
        'repo' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\Repository',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'repo',
        ],
        'repo_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'repo_id',
        ],
        'sha' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'sha',
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