<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroType;

class PurchaseOrder extends XeroType
{
    private static $propertyTypes = [
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
        'DateString'            => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Date',
        ],
        'Date'                  => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Date',
        ],
        'DeliveryDateString'    => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DeliveryDate',
        ],
        'DeliveryDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryDate',
        ],
        'LineAmountTypes' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'LineAmountTypes',
        ],
        'PurchaseOrderNumber' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PurchaseOrderNumber',
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
        'CurrencyCode' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'CurrencyCode',
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
        'DeliveryAddress' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryAddress',
        ],
        'AttentionTo' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'AttentionTo',
        ],
        'Telephone' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'Telephone',
        ],
        'DeliveryInstructions' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'DeliveryInstructions',
        ],
        'HasErrors'             => [
            'type'              => 'boolean',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'HasErrors',
        ],
        'IsDiscounted'          => [
            'type'              => 'boolean',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'IsDiscounted',
        ],
        'Reference'             => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Reference',
        ],
        'Type'                  => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Type',
        ],
        'ExpectedArrivalDate' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'ExpectedArrivalDate',
        ],
        'ExpectedArrivalDateString'=> [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'ExpectedArrivalDateString',
        ],
        'PurchaseOrderID' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'PurchaseOrderID',
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
        'HasAttachments' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'HasAttachments',
        ],
        'UpdatedDateUTC' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'UpdatedDateUTC',
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