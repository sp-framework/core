<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\RepoType;

class RepoSearchRestRequest extends RepoType
{
    private static $propertyTypes = [
        'q' => [
          'attribute' => false,
          'elementName' => 'q',
        ],
        'topic' => [
          'attribute' => false,
          'elementName' => 'topic',
        ],
        'includeDesc' => [
          'attribute' => false,
          'elementName' => 'includeDesc',
        ],
        'uid' => [
          'attribute' => false,
          'elementName' => 'uid',
        ],
        'priority_owner_id' => [
          'attribute' => false,
          'elementName' => 'priority_owner_id',
        ],
        'team_id' => [
          'attribute' => false,
          'elementName' => 'team_id',
        ],
        'starredBy' => [
          'attribute' => false,
          'elementName' => 'starredBy',
        ],
        'private' => [
          'attribute' => false,
          'elementName' => 'private',
        ],
        'is_private' => [
          'attribute' => false,
          'elementName' => 'is_private',
        ],
        'template' => [
          'attribute' => false,
          'elementName' => 'template',
        ],
        'archived' => [
          'attribute' => false,
          'elementName' => 'archived',
        ],
        'mode' => [
          'attribute' => false,
          'elementName' => 'mode',
        ],
        'exclusive' => [
          'attribute' => false,
          'elementName' => 'exclusive',
        ],
        'sort' => [
          'attribute' => false,
          'elementName' => 'sort',
        ],
        'order' => [
          'attribute' => false,
          'elementName' => 'order',
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