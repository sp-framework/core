<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Issue extends BaseType
{
    private static $propertyTypes = [
        'assignee' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\User',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'assignee',
        ],
        'assignees' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\User',
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
        'closed_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'closed_at',
        ],
        'comments' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'comments',
        ],
        'created_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'created_at',
        ],
        'due_date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'due_date',
        ],
        'html_url' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'html_url',
        ],
        'id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'id',
        ],
        'is_locked' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'is_locked',
        ],
        'labels' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\Label',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'labels',
        ],
        'milestone' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\Milestone',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'milestone',
        ],
        'number' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'number',
        ],
        'original_author' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'original_author',
        ],
        'original_author_id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'original_author_id',
        ],
        'pull_request' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\PullRequestMeta',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'pull_request',
        ],
        'ref' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ref',
        ],
        'repository' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\RepositoryMeta',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'repository',
        ],
        'state' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\StateType',
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
        'updated_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'updated_at',
        ],
        'url' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'url',
        ],
        'user' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\User',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'user',
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