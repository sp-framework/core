<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class ExpenseClaim extends XeroType
{
    private static $propertyTypes = [
        'ExpenseClaimID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpenseClaimID',
        ],
        'Status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'Payments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Payment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Payments',
        ],
        'User' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\User',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'User',
        ],
        'Receipts' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Receipt',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Receipts',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
        ],
        'Total' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Total',
        ],
        'AmountDue' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountDue',
        ],
        'AmountPaid' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPaid',
        ],
        'PaymentDueDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PaymentDueDate',
        ],
        'ReportingDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReportingDate',
        ],
        'ReceiptID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ReceiptID',
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