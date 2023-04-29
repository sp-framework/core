<?php
/**
 * Base class for objects that correspond to base64Binary types in the XML.
 *
 * @property string $value
 */
namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Types;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Base\Types\BaseType;

class Base64BinaryType extends BaseType
{
    /**
     * @var array Properties belonging to objects of this class.
     */
    private static $propertyTypes = [
        'value'         => [
            'type'          => 'string',
            'repeatable'    => false,
            'attribute'     => false
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