<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PurchaseOrder extends BaseType
{
    private static $propertyTypes = [
        'PurchaseOrderID'       => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'PurchaseOrderID',
        ],
        'PurchaseOrderNumber'   => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'PurchaseOrderNumber',
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
        'DeliveryDate'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DeliveryDate',
        ],
        'AttentionTo'           => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AttentionTo',
        ],
        'Telephone'             => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Telephone',
        ],
        'DeliveryInstructions'  => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DeliveryInstructions',
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
        'CurrencyRate'          => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'CurrencyRate',
        ],
        'CurrencyCode'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'CurrencyCode',
        ],
        'Contact'               => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Contact',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Contact',
        ],
        'BrandingThemeID'       => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'BrandingThemeID',
        ],
        'Status'                => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Status',
        ],
        'LineAmountTypes'       => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'LineAmountTypes',
        ],
        'LineItems'             => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\LineItem',
            'repeatable'        => true,
            'attribute'         => false,
            'elementName'       => 'LineItems',
        ],
        'SubTotal'              => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'SubTotal',
        ],
        'TotalTax'              => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'TotalTax',
        ],
        'Total'                 => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Total',
        ],
        'UpdatedDateUTC'        => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'UpdatedDateUTC',
        ],
        'HasAttachments'        => [
            'type'              => 'boolean',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'HasAttachments',
        ],
        'SentToContact'         => [
            'type'              => 'boolean',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'SentToContact',
        ],
        'DeliveryAddress'       => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DeliveryAddress',
        ],
        'ExpectedArrivalDate'   => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'ExpectedArrivalDate',
        ],
        'ExpectedArrivalDateString'=> [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'ExpectedArrivalDateString',
        ],
        'TotalDiscount'         => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'TotalDiscount',
        ],
        'StatusAttributeString' => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'StatusAttributeString',
        ],
        'ValidationErrors'      => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ValidationError',
            'repeatable'        => true,
            'attribute'         => false,
            'elementName'       => 'ValidationErrors',
        ],
        'Warnings'              => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\ValidationError',
            'repeatable'        => true,
            'attribute'         => false,
            'elementName'       => 'Warnings',
        ],
        'Attachments'           => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\Attachment',
            'repeatable'        => true,
            'attribute'         => false,
            'elementName'       => 'Attachments',
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