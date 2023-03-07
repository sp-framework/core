<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class EditIssueOption extends BaseType
{
    private static $propertyTypes = [
        'assignee' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'assignee',
        ],
        'assignees' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'assignees',
        ],
        'body' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'body',
        ],
        'due_date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'due_date',
        ],
        'milestone' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'milestone',
        ],
        'ref' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ref',
        ],
        'state' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'state',
        ],
        'title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'title',
        ],
        'unset_due_date' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'unset_due_date',
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