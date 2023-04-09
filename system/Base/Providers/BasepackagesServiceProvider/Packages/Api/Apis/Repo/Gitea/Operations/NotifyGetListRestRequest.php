<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\RepoType;

class NotifyGetListRestRequest extends RepoType
{
    private static $propertyTypes = [
        'all' => [
          'attribute' => false,
          'elementName' => 'all',
        ],
        'status-types' => [
          'attribute' => false,
          'elementName' => 'status-types',
        ],
        'subject-type' => [
          'attribute' => false,
          'elementName' => 'subject-type',
        ],
        'since' => [
          'attribute' => false,
          'elementName' => 'since',
        ],
        'before' => [
          'attribute' => false,
          'elementName' => 'before',
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