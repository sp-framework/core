<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class ConversionBalances extends XeroType
{
    private static $propertyTypes = [
        'AccountCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AccountCode',
        ],
        'Balance' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Balance',
        ],
        'BalanceDetails' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\BalanceDetails',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'BalanceDetails',
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