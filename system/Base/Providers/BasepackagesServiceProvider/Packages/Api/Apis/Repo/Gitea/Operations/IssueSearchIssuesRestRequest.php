<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\RepoType;

class IssueSearchIssuesRestRequest extends RepoType
{
    private static $propertyTypes = [
        'state' => [
          'attribute' => false,
          'elementName' => 'state',
        ],
        'labels' => [
          'attribute' => false,
          'elementName' => 'labels',
        ],
        'milestones' => [
          'attribute' => false,
          'elementName' => 'milestones',
        ],
        'q' => [
          'attribute' => false,
          'elementName' => 'q',
        ],
        'priority_repo_id' => [
          'attribute' => false,
          'elementName' => 'priority_repo_id',
        ],
        'type' => [
          'attribute' => false,
          'elementName' => 'type',
        ],
        'since' => [
          'attribute' => false,
          'elementName' => 'since',
        ],
        'before' => [
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
        'owner' => [
          'attribute' => false,
          'elementName' => 'owner',
        ],
        'team' => [
          'attribute' => false,
          'elementName' => 'team',
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