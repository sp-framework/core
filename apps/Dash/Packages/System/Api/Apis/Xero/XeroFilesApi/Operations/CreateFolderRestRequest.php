<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class CreateFolderRestRequest extends BaseType
{
    private static $propertyTypes = [
        'Folder' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\Folder',
          'attribute' => false,
          'repeatable' => true,
          'elementName' => 'Folder',
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