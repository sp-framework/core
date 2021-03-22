<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Allocation extends BaseType
{
    private static $propertyTypes = [
        'Invoice' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Invoice',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Invoice',
        ],
        'Overpayment' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Overpayment',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Overpayment',
        ],
        'Prepayment' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Prepayment',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Prepayment',
        ],
        'CreditNote' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\CreditNote',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CreditNote',
        ],
        'Amount' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Amount',
        ],
        'Date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Date',
        ],
        'StatusAttributeString' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'StatusAttributeString',
        ],
        'ValidationErrors' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ValidationError',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'ValidationErrors',
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