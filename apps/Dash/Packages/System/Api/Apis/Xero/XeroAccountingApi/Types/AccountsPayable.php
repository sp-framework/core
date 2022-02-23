<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class AccountsPayable extends XeroType
{
    private static $propertyTypes = [
        'Outstanding' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Outstanding',
        ],
        'Overdue' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Overdue',
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