<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class ImportSummaryObject extends XeroType
{
    private static $propertyTypes = [
        'ImportSummary' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ImportSummary',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ImportSummary',
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