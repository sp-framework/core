<?php

/**
 * Base class for objects that correspond to int types in the XML.
 *
 * @property integer $value
 */

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Types;

use Apps\Dash\Packages\System\Api\Apis\Ebay\Types\BaseType;

class IntegerType extends BaseType
{
    /**
     * @var array Properties belonging to objects of this class.
     */
    private static $propertyTypes = [
        'value' => [
            'type' => 'integer',
            'repeatable' => false,
            'attribute' => false
        ]
    ];

    /**
     * @param array $values Optional properties and values to assign to the object.
     */
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
