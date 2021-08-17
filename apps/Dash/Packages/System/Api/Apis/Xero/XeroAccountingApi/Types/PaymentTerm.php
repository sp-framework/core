<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class PaymentTerm extends XeroType
{
    private static $propertyTypes = [
        'Day' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Day',
        ],
        'Type' => [
          'type' => 'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Type',
        ],
        'Bills' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Bill',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Bills',
        ],
        'Sales' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Bill',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Sales',
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