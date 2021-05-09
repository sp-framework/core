<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class PurchaseOrders extends BaseType
{
    private static $propertyTypes = [
        'Id'                => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'Id',
        ],
        'Status'            => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'Status',
        ],
        'ProviderName'      => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'ProviderName',
        ],
        'DateTimeUTC'       => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'DateTimeUTC',
        ],
        'PurchaseOrders'    => [
            'type'          => 'Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types\PurchaseOrder',
            'repeatable'    => true,
            'attribute'     => false,
            'elementName'   => 'PurchaseOrders',
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