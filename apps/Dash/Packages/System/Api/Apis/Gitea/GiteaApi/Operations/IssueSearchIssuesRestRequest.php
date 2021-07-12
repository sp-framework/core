<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class IssueSearchIssuesRestRequest extends BaseType
{
    private static $propertyTypes = [
        'state' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'state',
        ],
        'labels' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'labels',
        ],
        'q' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'q',
        ],
        'priority_repo_id' => [
          'attribute' => false,
          'elementName' => 'priority_repo_id',
        ],
        'type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'type',
        ],
        'since' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'since',
        ],
        'before' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'before',
        ],
        'assigned' => [
          'attribute' => false,
          'elementName' => 'assigned',
        ],
        'created' => [
          'attribute' => false,
          'elementName' => 'created',
        ],
        'mentioned' => [
          'attribute' => false,
          'elementName' => 'mentioned',
        ],
        'review_requested' => [
          'attribute' => false,
          'elementName' => 'review_requested',
        ],
        'page' => [
          'attribute' => false,
          'elementName' => 'page',
        ],
        'limit' => [
          'attribute' => false,
          'elementName' => 'limit',
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