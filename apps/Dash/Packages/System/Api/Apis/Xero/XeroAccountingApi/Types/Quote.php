<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class Quote extends XeroType
{
    private static $propertyTypes = [
        'QuoteID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QuoteID',
        ],
        'QuoteNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'QuoteNumber',
        ],
        'Reference' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Reference',
        ],
        'Terms' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Terms',
        ],
        'Contact' => [
          'type' =>       'string',
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
        'DateString' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DateString',
        ],
        'ExpiryDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpiryDate',
        ],
        'ExpiryDateString' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpiryDateString',
        ],
        'Status' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Status',
        ],
        'CurrencyCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyCode',
        ],
        'CurrencyRate' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyRate',
        ],
        'SubTotal' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'SubTotal',
        ],
        'TotalTax' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalTax',
        ],
        'Total' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Total',
        ],
        'TotalDiscount' => [
          'type' => 'double',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'TotalDiscount',
        ],
        'Title' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Title',
        ],
        'Summary' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Summary',
        ],
        'BrandingThemeID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'BrandingThemeID',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
        ],
        'LineAmountTypes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineAmountTypes',
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