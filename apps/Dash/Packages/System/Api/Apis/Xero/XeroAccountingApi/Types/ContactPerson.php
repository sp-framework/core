<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\XeroAccountingApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class ContactPerson extends BaseType
{
    private static $propertyTypes = [
        'FirstName'         => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'FirstName',
        ],
        'LastName'          => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'LastName',
        ],
        'EmailAddress'      => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'EmailAddress',
        ],
        'IncludeInEmails'   => [
            'type'          => 'boolean',
            'repeatable'    => false,
            'attribute'     => false,
            'elementName'   => 'IncludeInEmails',
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