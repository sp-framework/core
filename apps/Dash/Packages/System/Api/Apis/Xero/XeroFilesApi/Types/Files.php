<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class Files extends XeroType
{
    private static $propertyTypes = [
        'TotalCount' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalCount',
        ],
        'Page' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Page',
        ],
        'PerPage' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PerPage',
        ],
        'Items' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroFilesApi\Types\FileObject',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Items',
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