<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Invoice extends BaseType
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
        'LineItems' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\LineItem',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'LineItems',
        ],
        'Date' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Date',
        ],
        'DueDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DueDate',
        ],
        'LineAmountTypes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineAmountTypes',
        ],
        'InvoiceNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InvoiceNumber',
        ],
        'Reference' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Reference',
        ],
        'BrandingThemeID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BrandingThemeID',
        ],
        'Url' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Url',
        ],
        'CurrencyCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyCode',
        ],
        'CurrencyRate' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyRate',
        ],
        'Status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'SentToContact' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SentToContact',
        ],
        'ExpectedPaymentDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpectedPaymentDate',
        ],
        'PlannedPaymentDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PlannedPaymentDate',
        ],
        'CISDeduction' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CISDeduction',
        ],
        'CISRate' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CISRate',
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
        'TotalDiscount' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalDiscount',
        ],
        'InvoiceID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'InvoiceID',
        ],
        'RepeatingInvoiceID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'RepeatingInvoiceID',
        ],
        'HasAttachments' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasAttachments',
        ],
        'IsDiscounted' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'IsDiscounted',
        ],
        'Payments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Payment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Payments',
        ],
        'Prepayments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Prepayment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Prepayments',
        ],
        'Overpayments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Overpayment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Overpayments',
        ],
        'AmountDue' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountDue',
        ],
        'AmountPaid' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountPaid',
        ],
        'FullyPaidOnDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'FullyPaidOnDate',
        ],
        'AmountCredited' => [
          'type' => 'number',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AmountCredited',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
        ],
        'CreditNotes' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\CreditNote',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'CreditNotes',
        ],
        'Attachments' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Attachment',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Attachments',
        ],
        'HasErrors' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasErrors',
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
        'Warnings' => [
          'type' => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ValidationError',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'Warnings',
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