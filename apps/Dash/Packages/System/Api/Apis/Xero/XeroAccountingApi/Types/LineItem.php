<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class LineItem extends BaseType
{
    private static $propertyTypes = [
        'LineItemID'            => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'LineItemID',
        ],
        'Description'           => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Description',
        ],
        'Quantity'              => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Quantity',
        ],
        'UnitAmount'            => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'UnitAmount',
        ],
        'ItemCode'              => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'ItemCode',
        ],
        'AccountCode'           => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AccountCode',
        ],
        'TaxType'               => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'TaxType',
        ],
        'TaxAmount'             => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'TaxAmount',
        ],
        'LineAmount'            => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'LineAmount',
        ],
        'Tracking'              => [
            'type'              => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\LineItemTracking',
            'repeatable'        => true,
            'attribute'         => false,
            'elementName'       => 'Tracking',
        ],
        'DiscountRate'          => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DiscountRate',
        ],
        'DiscountAmount'        => [
            'type'              => 'integer',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'DiscountAmount',
        ],
        'RepeatingInvoiceID'    => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'RepeatingInvoiceID',
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