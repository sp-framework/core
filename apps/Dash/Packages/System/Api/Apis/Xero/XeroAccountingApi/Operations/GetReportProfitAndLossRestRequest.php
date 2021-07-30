<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Operations;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class GetReportProfitAndLossRestRequest extends BaseType
{
    private static $propertyTypes = [
        'FromDate' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'FromDate',
        ],
        'ToDate' => [
          'type' =>       'string',
          'attribute' => false,
          'repeatable' => false,
          'elementName' => 'ToDate',
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
        'trackingCategoryID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'trackingCategoryID',
        ],
        'trackingCategoryID2' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'trackingCategoryID2',
        ],
        'trackingOptionID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'trackingOptionID',
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