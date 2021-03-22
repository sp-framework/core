<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Prepayment extends BaseType
{
    private static $propertyTypes = [
        'Type' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Type',
        ],
        'Contact' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Contact',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Contact',
        ],
        'Date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Date',
        ],
        'Status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'LineAmountTypes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineAmountTypes',
        ],
        'LineItems' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\LineItem',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'LineItems',
        ],
        'SubTotal' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubTotal',
        ],
        'TotalTax' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalTax',
        ],
        'Total' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Total',
        ],
        'Reference' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Reference',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
        ],
        'CurrencyCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyCode',
        ],
        'PrepaymentID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PrepaymentID',
        ],
        'CurrencyRate' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyRate',
        ],
        'RemainingCredit' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RemainingCredit',
        ],
        'Allocations' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Allocation',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Allocations',
        ],
        'AppliedAmount' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AppliedAmount',
        ],
        'HasAttachments' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasAttachments',
        ],
        'Attachments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Attachment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Attachments',
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