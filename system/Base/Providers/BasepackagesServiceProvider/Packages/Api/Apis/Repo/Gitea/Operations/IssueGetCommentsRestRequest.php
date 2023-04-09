<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\RepoType;

class IssueGetCommentsRestRequest extends RepoType
{
    private static $propertyTypes = [
        'owner' => [
          'attribute' => false,
          'elementName' => 'owner',
        ],
        'repo' => [
          'attribute' => false,
          'elementName' => 'repo',
        ],
        'index' => [
          'attribute' => false,
          'elementName' => 'index',
        ],
        'since' => [
          'attribute' => false,
          'elementName' => 'since',
        ],
        'before' => [
          'attribute' => false,
          'elementName' => 'before',
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