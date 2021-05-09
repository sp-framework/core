<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class Address extends BaseType
{
    private static $propertyTypes = [
        'AddressType'           => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AddressType',
        ],
        'AddressLine1'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AddressLine1',
        ],
        'AddressLine2'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AddressLine2',
        ],
        'AddressLine3'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AddressLine3',
        ],
        'AddressLine4'          => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AddressLine4',
        ],
        'City'                  => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'City',
        ],
        'Region'                => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Region',
        ],
        'PostalCode'            => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'PostalCode',
        ],
        'Country'               => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'Country',
        ],
        'AttentionTo'           => [
            'type'              => 'string',
            'repeatable'        => false,
            'attribute'         => false,
            'elementName'       => 'AttentionTo',
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