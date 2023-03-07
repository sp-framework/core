<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PullReview extends BaseType
{
    private static $propertyTypes = [
        'body' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'body',
        ],
        'comments_count' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'comments_count',
        ],
        'commit_id' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'commit_id',
        ],
        'dismissed' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'dismissed',
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
        'official' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'official',
        ],
        'pull_request_url' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'pull_request_url',
        ],
        'stale' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'stale',
        ],
        'state' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\ReviewStateType',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'state',
        ],
        'submitted_at' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'submitted_at',
        ],
        'team' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\Team',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'team',
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