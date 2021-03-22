<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetReportBalanceSheetRestRequest extends BaseType
{
    private static $propertyTypes = [
        'date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'date',
        ],
        'periods' => [
          'attribute' => false,
          'elementName' => 'periods',
        ],
        'timeframe' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'timeframe',
        ],
        'trackingOptionID1' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'trackingOptionID1',
        ],
        'trackingOptionID2' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'trackingOptionID2',
        ],
        'standardLayout' => [
          'attribute' => false,
          'elementName' => 'standardLayout',
        ],
        'paymentsOnly' => [
          'attribute' => false,
          'elementName' => 'paymentsOnly',
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