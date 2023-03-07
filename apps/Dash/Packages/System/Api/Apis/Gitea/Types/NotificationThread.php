<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class NotificationThread extends BaseType
{
    private static $propertyTypes = [
        'id' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'id',
        ],
        'pinned' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'pinned',
        ],
        'repository' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\Repository',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'repository',
        ],
        'subject' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\NotificationSubject',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'subject',
        ],
        'unread' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'unread',
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