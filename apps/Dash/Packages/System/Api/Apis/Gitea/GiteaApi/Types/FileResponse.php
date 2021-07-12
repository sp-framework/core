<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class FileResponse extends BaseType
{
    private static $propertyTypes = [
        'commit' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\FileCommitResponse',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'commit',
        ],
        'content' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\ContentsResponse',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'content',
        ],
        'verification' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types\PayloadCommitVerification',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'verification',
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