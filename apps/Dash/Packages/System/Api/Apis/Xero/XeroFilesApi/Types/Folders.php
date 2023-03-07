<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class Folders extends XeroType
{
    private static $propertyTypes = [
        'Folders' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\Folder',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Folders',
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