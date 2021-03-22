<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Setup extends BaseType
{
    private static $propertyTypes = [
        'ConversionDate' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ConversionDate',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ConversionDate',
        ],
        'ConversionBalances' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ConversionBalances',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ConversionBalances',
        ],
        'Accounts' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Account',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Accounts',
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